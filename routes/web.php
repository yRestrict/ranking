<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\PlayerController;
use App\Http\Controllers\Frontend\RankSystemController;

Route::prefix('ranking')->group(function() {
    Route::get('/', [RankSystemController::class, 'index'])
         ->name('ranking.index');
});
