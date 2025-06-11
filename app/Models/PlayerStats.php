<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerStats extends Model
{
    use HasFactory;

    protected $table = 'rank_system';

    protected $fillable = [
        'Player',
        'Nick',
        'Steam ID',
        'IP',
        'XP',
        'Rank XP',
        'Next Rank XP',
        'Level',
        'Rank Name',
        'Kills',
        'Deaths',
        'Headshots',
        'Assists',
        'Shots',
        'Hits',
        'Damage',
        'Stolen',
        'Recupered',
        'Captured',
        'MVP',
        'Rounds Won',
        'Played Time',
        'First Login',
        'Last Login',
        'Skill',
        'Skill Range',
        'Flags',
        'Online',
        'New',
        'Steam',
        'Avatar',
        'Profile'
    ];

    protected $casts = [
        'XP' => 'integer',
        'Level' => 'integer',
        'Kills' => 'integer',
        'Deaths' => 'integer',
        'Headshots' => 'integer',
        'Assists' => 'integer',
        'Shots' => 'integer',
        'Hits' => 'integer',
        'Damage' => 'integer',
        'Stolen' => 'integer',
        'Recupered' => 'integer',
        'Captured' => 'integer',
        'MVP' => 'integer',
        'Rounds Won' => 'integer',
        'Played Time' => 'integer',
        'First Login' => 'datetime',
        'Last Login' => 'datetime',
        'Skill' => 'float',
        'Online' => 'boolean',
        'New' => 'boolean',
    ];
}