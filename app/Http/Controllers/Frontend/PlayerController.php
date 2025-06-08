<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PlayersStat;

/**
 * @group PlayersStat
*/
class PlayersStatController extends Controller
{
    public PlayersStat $model;

    public function __construct()
    {
        $this->model = new PlayersStat();
    }
}