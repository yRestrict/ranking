<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RankSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankSystemController extends Controller
{
    public RankSystem $model;

    public function __construct()
    {
        $this->model = new RankSystem();
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit', 15);
        $ip = $request->query('ip_server');
        
        $ranking = $this->getRankingData($ip, $limit);

        $allowGlobalView = $this->isValidIp($ip) ? 0 : 1;

        $maxHeadshots = collect($ranking)->max('Headshots') ?: 1;

        $ranking = collect($ranking)->map(function($player) use ($maxHeadshots) {
            $player->hsPercentage = $maxHeadshots > 0 ? round(($player->Headshots / $maxHeadshots) * 100) : 0;
            return $player;
        });

        return view('frontend.ranking.index', [
            'ranking' => $ranking,
            'currentLimit' => $limit,
            'currentIp' => $ip,
            'allowGlobalView' => $allowGlobalView,
            'currentSearch' => $request->query('search', ''), // Adicionado
            'currentOrder' => $request->query('order', '')    // Adicionado
        ]);
    }

    public function consultRanking(Request $request)
    {
        $limit = $request->query('limit', 15);
        $ip = $request->query('ip_server');
        
        $ranking = $this->getRankingData($ip, $limit);
        
        return response()->json($ranking);
    }

    private function getRankingData($ip, $limit)
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
        
        return $query->groupBy([
                'ServerIp',
                'Steam ID',
                'Player',
                'Nick',
                'IP',
                'Flags',
                'Avatar'
            ])
            ->orderByRaw('(SUM(Kills) - SUM(Deaths)) DESC')
            ->orderByDesc(DB::raw('SUM(Assists)'))
            ->orderByDesc(DB::raw('SUM(Headshots)'))
            ->orderByDesc(DB::raw('SUM(MVP)'))
            ->orderByDesc(DB::raw('SUM(`Rounds Won`)'))
            ->orderByDesc(DB::raw('SUM(XP)'))
            ->orderBy('Nick')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function isValidIp($serverIp)
    {
        return !empty($serverIp) && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{4,5}$/', $serverIp);
    }
}