<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PlayerStats;

/**
 * @group PlayersStat
*/
class PlayerController extends Controller
{
    public PlayerStats $model;

    public function __construct()
    {
        $this->model = new PlayerStats();
    }
}