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

        // Obter dados paginados
        $paginatedData = $this->getPaginatedRankingData($ip, $search, $order, $page, $limit);
        
        $ranking = collect($paginatedData['data']);
        $totalRecords = $paginatedData['total'];

        // Calcular porcentagem de headshots
        $maxHeadshots = $ranking->max('Headshots') ?: 1;
        $ranking = $ranking->map(function($player) use ($maxHeadshots) {
            $player->hsPercentage = $maxHeadshots > 0 ? round(($player->Headshots / $maxHeadshots) * 100) : 0;
            return $player;
        });

        // Criar objeto de paginação customizado
        $paginator = $this->createPaginator($ranking, $totalRecords, $limit, $page, $request);

        return view('frontend.ranking.index', [
            'ranking' => $ranking,
            'paginator' => $paginator,
            'currentLimit' => $limit,
            'currentIp' => $ip,
            'allowGlobalView' => $allowGlobalView,
            'currentSearch' => $search,
            'currentOrder' => $order,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page
        ]);
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
        // Contar total de registros
        $totalRecords = $this->getTotalRecords($ip, $search);
        
        // Calcular offset
        $offset = ($page - 1) * $limit;
        
        // Obter dados da página atual
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

        // Aplicar ordenação baseada no parâmetro order
        $this->applyOrderBy($query, $order);
        
        return $query->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function applyOrderBy($query, $order)
    {
        $orderMapping = [
            '0' => [DB::raw('SUM(XP)'), 'DESC'],
            '1' => ['Nick', 'ASC'],
            '2' => [DB::raw('SUM(Kills)'), 'DESC'],
            '3' => [DB::raw('SUM(Assists)'), 'DESC'],
            '4' => [DB::raw('SUM(Deaths)'), 'DESC'],
            '6' => [DB::raw('SUM(Headshots)'), 'DESC'],
            '10' => [DB::raw('SUM(`Rounds Won`)'), 'DESC'],
            '11' => [DB::raw('SUM(MVP)'), 'DESC'],
            '12' => [DB::raw('SUM(Level)'), 'DESC'],
            '13' => [DB::raw('(SUM(Kills) - SUM(Deaths))'), 'DESC'], // Default
            '14' => [DB::raw('SUM(XP)'), 'ASC'],
            '15' => ['Nick', 'DESC'],
            '16' => [DB::raw('SUM(Kills)'), 'ASC'],
            '17' => [DB::raw('SUM(Assists)'), 'ASC'],
            '18' => [DB::raw('SUM(Deaths)'), 'ASC'],
            '20' => [DB::raw('SUM(Headshots)'), 'ASC'],
            '24' => [DB::raw('SUM(`Rounds Won`)'), 'ASC'],
            '25' => [DB::raw('SUM(MVP)'), 'ASC'],
            '26' => [DB::raw('SUM(Level)'), 'ASC'],
        ];

        if (isset($orderMapping[$order])) {
            $query->orderBy($orderMapping[$order][0], $orderMapping[$order][1]);
        } else {
            // Ordenação padrão (order 13)
            $query->orderByRaw('(SUM(Kills) - SUM(Deaths)) DESC')
                  ->orderByDesc(DB::raw('SUM(Assists)'))
                  ->orderByDesc(DB::raw('SUM(Headshots)'))
                  ->orderByDesc(DB::raw('SUM(MVP)'))
                  ->orderByDesc(DB::raw('SUM(`Rounds Won`)'))
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

        // Preservar parâmetros da query string
        $paginator->appends($request->query());

        return $paginator;
    }

    private function isValidIp($serverIp)
    {
        return !empty($serverIp) && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{4,5}$/', $serverIp);
    }
}