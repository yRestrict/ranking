<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\RankSystemController;

Route::get('/ranking', [RankSystemController::class, 'index'])->name('ranking.index');
Route::get('/ranking/consult', [RankSystemController::class, 'consultRanking'])->name('ranking.consult');


Route::get('/top15.php', [RankSystemController::class, 'top15'])->name('ranking.top15');

Route::get('/top15', [RankSystemController::class, 'top15'])->name('ranking.top15.friendly');

Route::get('/api/player-position/{steamId}', function($steamId) {
    $controller = new RankSystemController();
    $serverIp = request()->query('server');
    $order = request()->query('order', '13');
    
    $position = $controller->getPlayerPosition($steamId, $serverIp, $order);
    
    return response()->json([
        'steam_id' => $steamId,
        'position' => $position,
        'server_ip' => $serverIp,
        'order' => $order
    ]);
})->name('api.player.position');

Route::get('/api/generate-plugin-url/{steamId}', function($steamId) {
    $controller = new RankSystemController();
    $serverIp = request()->query('server');
    $order = request()->query('order');
    $position = request()->query('position');
    
    $url = $controller->generatePluginUrl($steamId, $serverIp, $order, $position);
    
    return response()->json([
        'steam_id' => $steamId,
        'plugin_url' => $url,
        'server_ip' => $serverIp
    ]);
})->name('api.generate.plugin.url');