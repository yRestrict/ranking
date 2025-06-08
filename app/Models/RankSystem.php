<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RankSystem
 *
 * @property string $ServerIp
 * @property string $Player
 * @property string $Nick
 * @property string $Steam ID
 * @property string $IP
 * @property int $XP
 * @property int $Rank XP
 * @property int $Next Rank XP
 * @property int $Level
 * @property string $Rank Name
 * @property int $Kills
 * @property int $Deaths
 * @property int $Headshots
 * @property int $Assists
 * @property int $Shots
 * @property int $Hits
 * @property int $Damage
 * @property int $Stolen
 * @property int $Recupered
 * @property int $Captured
 * @property int $MVP
 * @property int $Rounds Won
 * @property int $Played Time
 * @property string $First Login
 * @property string $Last Login
 * @property string $Skill
 * @property float $Skill Range
 * @property string $Flags
 * @property int $Online
 * @property int $New
 * @property int $Steam
 * @property string $Avatar
 * @property string $Profile
 *
 * @package App\Models
 */
class RankSystem extends Model 
{
	protected $table = 'rank_system';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'XP' => 'int',
		'Rank XP' => 'int',
		'Next Rank XP' => 'int',
		'Level' => 'int',
		'Kills' => 'int',
		'Deaths' => 'int',
		'Headshots' => 'int',
		'Assists' => 'int',
		'Shots' => 'int',
		'Hits' => 'int',
		'Damage' => 'int',
		'Stolen' => 'int',
		'Recupered' => 'int',
		'Captured' => 'int',
		'MVP' => 'int',
		'Rounds Won' => 'int',
		'Played Time' => 'int',
		'Skill Range' => 'float',
		'Online' => 'int',
		'New' => 'int',
		'Steam' => 'int'
	];

	protected $fillable = [
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
}