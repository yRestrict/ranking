<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RankSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class RankSystemController extends Controller
{
    public RankSystem $model;
    private const ITEMS_PER_PAGE = 15;

    public function __construct()
    {
        $this->model = new RankSystem();
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit', self::ITEMS_PER_PAGE);
        $ip = $request->query('ip_server');
        $search = $request->query('search', '');
        $order = $request->query('order', '13');
        $page = $request->query('page', 1);

        $allowGlobalView = $this->isValidIp($ip) ? 0 : 1;

        $paginatedData = $this->getPaginatedRankingData($ip, $search, $order, $page, $limit);
        
        $ranking = collect($paginatedData['data']);
        $totalRecords = $paginatedData['total'];

        $maxHeadshots = $ranking->max('Headshots') ?: 1;
        $ranking = $ranking->map(function($player) use ($maxHeadshots) {
            $player->hsPercentage = $maxHeadshots > 0 ? round(($player->Headshots / $maxHeadshots) * 100) : 0;
            return $player;
        });
 
        $paginator = $this->createPaginator($ranking, $totalRecords, $limit, $page, $request);

        $serverNames = $this->getServerNames();

        return view('frontend.ranking.index', [
            'ranking' => $ranking,
            'paginator' => $paginator,
            'currentLimit' => $limit,
            'serverNames' => $serverNames,
            'currentIp' => $ip,
            'allowGlobalView' => $allowGlobalView,
            'currentSearch' => $search,
            'currentOrder' => $order,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page
        ]);
    }

    // Nova rota para compatibilidade com o plugin do servidor
    public function top15(Request $request)
    {
        $top = $request->query('top', 15);
        $player = $request->query('player', '');
        $order = $request->query('order', '13');
        $defaultOrder = $request->query('default_order', '13');
        $style = $request->query('style', '0');
        $search = $request->query('search', '');
        $srv = $request->query('srv', '');
        
        // Calcula a página baseada no parâmetro 'top'
        $page = max(1, ceil($top / self::ITEMS_PER_PAGE));
        
        $paginatedData = $this->getPaginatedRankingData($srv, $search, $order, $page, self::ITEMS_PER_PAGE);
        
        $ranking = collect($paginatedData['data']);
        $totalRecords = $paginatedData['total'];

        $maxHeadshots = $ranking->max('Headshots') ?: 1;
        $ranking = $ranking->map(function($player) use ($maxHeadshots) {
            $player->hsPercentage = $maxHeadshots > 0 ? round(($player->Headshots / $maxHeadshots) * 100) : 0;
            return $player;
        });

        $serverNames = $this->getServerNames();

        // Se style=1, retorna apenas o conteúdo (para iframe)
        if ($style == '1') {
            return view('frontend.ranking.top15', [
                'ranking' => $ranking,
                'serverNames' => $serverNames,
                'currentPlayer' => $player,
                'currentOrder' => $order,
                'currentSearch' => $search,
                'currentServer' => $srv,
                'totalRecords' => $totalRecords,
                'currentPage' => $page,
                'topPosition' => $top
            ]);
        }

        // Se style=0, retorna a view completa
        return view('frontend.ranking.index', [
            'ranking' => $ranking,
            'paginator' => $this->createPaginator($ranking, $totalRecords, self::ITEMS_PER_PAGE, $page, $request),
            'currentLimit' => self::ITEMS_PER_PAGE,
            'serverNames' => $serverNames,
            'currentIp' => $srv,
            'allowGlobalView' => $this->isValidIp($srv) ? 0 : 1,
            'currentSearch' => $search,
            'currentOrder' => $order,
            'totalPages' => ceil($totalRecords / self::ITEMS_PER_PAGE),
            'currentPage' => $page,
            'highlightPlayer' => $player
        ]);
    }

    // Método para buscar posição específica de um jogador
    public function getPlayerPosition($steamId, $serverIp = null, $order = '13')
    {
        $query = DB::table('rank_system')
            ->select([
                'Steam ID',
                DB::raw('SUM(XP) AS XP'),
                DB::raw('SUM(Kills) AS Kills'),
                DB::raw('SUM(Deaths) AS Deaths'),
                DB::raw('SUM(Assists) AS Assists'),
                DB::raw('SUM(Headshots) AS Headshots'),
                DB::raw('SUM(MVP) AS MVP'),
                DB::raw('SUM(`Rounds Won`) AS Rounds_Won'),
                DB::raw('SUM(Stolen) AS Stolen'),
                DB::raw('SUM(Recupered) AS Recupered'),
                DB::raw('SUM(Captured) AS Captured'),
                DB::raw('(SUM(Kills) - SUM(Deaths)) AS KD_Diff')
            ])
            ->groupBy('Steam ID');

        if ($serverIp) {
            $query->where('ServerIp', $serverIp);
        }

        $this->applyOrderBy($query, $order);

        $rankedPlayers = $query->get();
        
        foreach ($rankedPlayers as $index => $player) {
            if ($player->{'Steam ID'} === $steamId) {
                return $index + 1; // Posição (1-based)
            }
        }

        return null; // Jogador não encontrado
    }

    // Método para gerar URL compatível com o plugin
    public function generatePluginUrl($steamId, $serverIp, $order = null, $position = null)
    {
        $order = $order ?? config('ranking.default_order', '13');
        
        if ($position === null) {
            $position = $this->getPlayerPosition($steamId, $serverIp, $order);
        }

        $url = url('/top15.php') . '?' . http_build_query([
            'top' => $position ?: 15,
            'player' => $steamId,
            'order' => $order,
            'default_order' => $order,
            'style' => 1,
            'search' => '',
            'srv' => $serverIp
        ]);

        return $url;
    }

    public function consultRanking(Request $request)
    {
        $limit = $request->query('limit', self::ITEMS_PER_PAGE);
        $ip = $request->query('ip_server');
        $search = $request->query('search', '');
        $order = $request->query('order', '13');
        $page = $request->query('page', 1);
        
        $paginatedData = $this->getPaginatedRankingData($ip, $search, $order, $page, $limit);
        
        return response()->json([
            'data' => $paginatedData['data'],
            'total' => $paginatedData['total'],
            'current_page' => $page,
            'per_page' => $limit,
            'total_pages' => ceil($paginatedData['total'] / $limit)
        ]);
    }

    private function getPaginatedRankingData($ip, $search, $order, $page, $limit)
    {
        $totalRecords = $this->getTotalRecords($ip, $search);
        
        $offset = ($page - 1) * $limit;
        
        $data = $this->getRankingData($ip, $search, $order, $limit, $offset);
        
        return [
            'data' => $data,
            'total' => $totalRecords
        ];
    }

    private function getTotalRecords($ip, $search)
    {
        $query = DB::table('rank_system');
        
        if ($ip) {
            $query->where('ServerIp', $ip);
        }
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('Nick', 'LIKE', '%' . $search . '%')
                  ->orWhere('IP', 'LIKE', '%' . $search . '%')
                  ->orWhere('Steam ID', 'LIKE', '%' . $search . '%');
            });
        }
        
        return $query->distinct('Steam ID')->count();
    }

    private function getRankingData($ip, $search = '', $order = '13', $limit = self::ITEMS_PER_PAGE, $offset = 0)
    {
        $query = DB::table('rank_system')
            ->select([
                'ServerIp',
                'Player',
                'Nick',
                'Steam ID',
                'IP',
                DB::raw('SUM(XP) AS XP'),
                DB::raw('SUM(Level) AS Level'),
                DB::raw('SUM(Kills) AS Kills'),
                DB::raw('SUM(Assists) AS Assists'),
                DB::raw('SUM(Headshots) AS Headshots'),
                DB::raw('SUM(Deaths) AS Deaths'),
                DB::raw('SUM(MVP) AS MVP'),
                DB::raw('SUM(`Rounds Won`) AS Rounds_Won'),
                DB::raw('SUM(Stolen) AS Stolen'),
                DB::raw('SUM(Recupered) AS Recupered'),
                DB::raw('SUM(Captured) AS Captured'),
                DB::raw('SUM(`Skill Range`) AS Skill_Range'),
                'Flags',
                DB::raw('MAX(Online) AS Online'),
                DB::raw('MIN(New) AS New'),
                DB::raw('MAX(Steam) AS Steam'),
                'Avatar'
            ]);
        
        if ($ip) {
            $query->where('ServerIp', $ip);
        }
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('Nick', 'LIKE', '%' . $search . '%')
                  ->orWhere('IP', 'LIKE', '%' . $search . '%')
                  ->orWhere('Steam ID', 'LIKE', '%' . $search . '%');
            });
        }
        
        $query->groupBy([
            'ServerIp',
            'Steam ID',
            'Player',
            'Nick',
            'IP',
            'Flags',
            'Avatar'
        ]);

        $this->applyOrderBy($query, $order);
        
        return $query->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function applyOrderBy($query, $order)
    {
        $orderMapping = [
            '0' => [DB::raw('SUM(XP)'), 'DESC', [['Nick', 'ASC']]],
            '1' => ['Nick', 'ASC'],
            '2' => [DB::raw('SUM(Kills)'), 'DESC', [['Nick', 'ASC']]],
            '3' => [DB::raw('SUM(Assists)'), 'DESC', [['Nick', 'ASC']]],
            '4' => [DB::raw('SUM(Deaths)'), 'DESC', [['Nick', 'ASC']]],
            '5' => [DB::raw('SUM(`Skill Range`)'), 'DESC', [['Nick', 'ASC']]],
            '6' => [DB::raw('SUM(Headshots)'), 'DESC', [['Nick', 'ASC']]],
            '7' => [DB::raw('SUM(Stolen)'), 'DESC', [['Nick', 'ASC']]],
            '8' => [DB::raw('SUM(Recupered)'), 'DESC', [['Nick', 'ASC']]],
            '9' => [DB::raw('SUM(Captured)'), 'DESC', [['Nick', 'ASC']]],
            '10' => [DB::raw('SUM(`Rounds Won`)'), 'DESC', [['Nick', 'ASC']]],
            '11' => [DB::raw('SUM(MVP)'), 'DESC', [['Nick', 'ASC']]],
            '12' => [DB::raw('SUM(Level)'), 'DESC', [[DB::raw('SUM(XP)'), 'DESC']]],
            '13' => [DB::raw('(SUM(Kills) - SUM(Deaths))'), 'DESC', [
                [DB::raw('SUM(Assists)'), 'DESC'],
                [DB::raw('SUM(Headshots)'), 'DESC'],
                [DB::raw('SUM(MVP)'), 'DESC'],
                [DB::raw('SUM(`Rounds Won`)'), 'DESC'],
                [DB::raw('SUM(Stolen)'), 'DESC'],
                [DB::raw('SUM(Recupered)'), 'DESC'],
                [DB::raw('SUM(Captured)'), 'DESC'],
                [DB::raw('SUM(XP)'), 'DESC'],
                ['Nick', 'ASC']
            ]],
            
            '14' => [DB::raw('SUM(XP)'), 'ASC', [['Nick', 'ASC']]],
            '15' => ['Nick', 'DESC'],
            '16' => [DB::raw('SUM(Kills)'), 'ASC', [['Nick', 'ASC']]],
            '17' => [DB::raw('SUM(Assists)'), 'ASC', [['Nick', 'ASC']]],
            '18' => [DB::raw('SUM(Deaths)'), 'ASC', [['Nick', 'ASC']]],
            '19' => [DB::raw('SUM(`Skill Range`)'), 'ASC', [['Nick', 'ASC']]],
            '20' => [DB::raw('SUM(Headshots)'), 'ASC', [['Nick', 'ASC']]],
            '21' => [DB::raw('SUM(Stolen)'), 'ASC', [['Nick', 'ASC']]],
            '22' => [DB::raw('SUM(Recupered)'), 'ASC', [['Nick', 'ASC']]],
            '23' => [DB::raw('SUM(Captured)'), 'ASC', [['Nick', 'ASC']]],
            '24' => [DB::raw('SUM(`Rounds Won`)'), 'ASC', [['Nick', 'ASC']]],
            '25' => [DB::raw('SUM(MVP)'), 'ASC', [['Nick', 'ASC']]],
            '26' => [DB::raw('SUM(Level)'), 'ASC', [[DB::raw('SUM(XP)'), 'ASC'], ['Nick', 'ASC']]],
        ];

        if (isset($orderMapping[$order])) {
            $orderConfig = $orderMapping[$order];
            
            $query->orderBy($orderConfig[0], $orderConfig[1]);
            
            if (isset($orderConfig[2]) && is_array($orderConfig[2])) {
                foreach ($orderConfig[2] as $secondaryOrder) {
                    $query->orderBy($secondaryOrder[0], $secondaryOrder[1]);
                }
            }
        } else {
            $query->orderByRaw('(SUM(Kills) - SUM(Deaths)) DESC')
                  ->orderByDesc(DB::raw('SUM(Assists)'))
                  ->orderByDesc(DB::raw('SUM(Headshots)'))
                  ->orderByDesc(DB::raw('SUM(MVP)'))
                  ->orderByDesc(DB::raw('SUM(`Rounds Won`)'))
                  ->orderByDesc(DB::raw('SUM(Stolen)'))
                  ->orderByDesc(DB::raw('SUM(Recupered)'))
                  ->orderByDesc(DB::raw('SUM(Captured)'))
                  ->orderByDesc(DB::raw('SUM(XP)'))
                  ->orderBy('Nick');
        }
    }

    private function createPaginator($items, $total, $perPage, $currentPage, $request)
    {
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $paginator->appends($request->query());

        return $paginator;
    }

    private function getServerNames()
    {
        return [
            '104.234.65.242:27400' => '[Servidor 1]',
            '123.456.789.11:27015' => '[Servidor 2]',
        ];
    }
    
    private function isValidIp($serverIp)
    {
        return !empty($serverIp) && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{4,5}$/', $serverIp);
    }
}