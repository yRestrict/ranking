<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeaponKillsTable extends Migration
{
    public function up()
    {
        Schema::create('weapon_kills', function (Blueprint $table) {
            $table->id();
            $table->string('Player');
            $table->string('ServerIp');
            $table->integer('Knife')->default(0);
            $table->integer('Glock18')->default(0);
            $table->integer('USP')->default(0);
            $table->integer('P228')->default(0);
            $table->integer('Deagle')->default(0);
            $table->integer('Fiveseven')->default(0);
            $table->integer('Elite')->default(0);
            $table->integer('M3')->default(0);
            $table->integer('XM1014')->default(0);
            $table->integer('TMP')->default(0);
            $table->integer('MAC10')->default(0);
            $table->integer('MP5 Navy')->default(0);
            $table->integer('UMP45')->default(0);
            $table->integer('P90')->default(0);
            $table->integer('M249')->default(0);
            $table->integer('Galil')->default(0);
            $table->integer('Famas')->default(0);
            $table->integer('AK47')->default(0);
            $table->integer('M4A1')->default(0);
            $table->integer('SG552')->default(0);
            $table->integer('AUG')->default(0);
            $table->integer('Scout')->default(0);
            $table->integer('AWP')->default(0);
            $table->integer('G3SG1')->default(0);
            $table->integer('SG550')->default(0);
            $table->integer('HE Grenade')->default(0);
            $table->timestamps();

            $table->index(['Player', 'ServerIp']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('weapon_kills');
    }
}