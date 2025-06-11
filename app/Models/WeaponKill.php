<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeaponKill extends Model
{
    use HasFactory;

    protected $table = 'weapon_kills';

    protected $primaryKey = ['Player', 'ServerIp'];
    public $incrementing = false;

    protected $fillable = [
        'ServerIp',
        'Player',
        'Knife',
        'Glock18',
        'USP',
        'P228',
        'Deagle',
        'Fiveseven',
        'Elite',
        'M3',
        'XM1014',
        'TMP',
        'MAC10',
        'MP5 Navy',
        'UMP45',
        'P90',
        'M249',
        'Galil',
        'Famas',
        'AK47',
        'M4A1',
        'SG552',
        'AUG',
        'Scout',
        'AWP',
        'G3SG1',
        'SG550',
        'HE Grenade',
        'LastKiller'
    ];

    protected $casts = [
        'Knife' => 'integer',
        'Glock18' => 'integer',
        'USP' => 'integer',
        'P228' => 'integer',
        'Deagle' => 'integer',
        'Fiveseven' => 'integer',
        'Elite' => 'integer',
        'M3' => 'integer',
        'XM1014' => 'integer',
        'TMP' => 'integer',
        'MAC10' => 'integer',
        'MP5 Navy' => 'integer',
        'UMP45' => 'integer',
        'P90' => 'integer',
        'M249' => 'integer',
        'Galil' => 'integer',
        'Famas' => 'integer',
        'AK47' => 'integer',
        'M4A1' => 'integer',
        'SG552' => 'integer',
        'AUG' => 'integer',
        'Scout' => 'integer',
        'AWP' => 'integer',
        'G3SG1' => 'integer',
        'SG550' => 'integer',
        'HE Grenade' => 'integer',
    ];
}