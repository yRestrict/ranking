<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PlayersStat
 *
 * @property int $id
 * @property int $player_id
 * @property int|null $xp
 * @property int|null $kills
 * @property int|null $deaths
 * @property int|null $captures
 * @property int|null $defenses
 *
 * @package App\Models
 */
class PlayersStat extends Model
{
	protected $table = 'players_stats';
	public $timestamps = false;

	protected $casts = [
		'player_id' => 'int',
		'xp' => 'int',
		'kills' => 'int',
		'deaths' => 'int',
		'captures' => 'int',
		'defenses' => 'int'
	];

	protected $fillable = [
		'player_id',
		'xp',
		'kills',
		'deaths',
		'captures',
		'defenses'
	];
}