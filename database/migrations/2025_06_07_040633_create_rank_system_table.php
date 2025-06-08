<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankSystemTable extends Migration
{
    public function up()
    {
        Schema::create('rank_system', function (Blueprint $table) {
            $table->id();
            $table->string('Player');
            $table->string('Nick');
            $table->string('Steam ID');
            $table->string('IP');
            $table->string('ServerIp');
            $table->integer('XP')->default(0);
            $table->integer('Rank XP')->default(0);
            $table->integer('Next Rank XP')->default(0);
            $table->integer('Level')->default(0);
            $table->string('Rank Name')->nullable();
            $table->integer('Kills')->default(0);
            $table->integer('Assists')->default(0);
            $table->integer('Headshots')->default(0);
            $table->integer('Deaths')->default(0);
            $table->integer('Shots')->default(0);
            $table->integer('Hits')->default(0);
            $table->integer('Damage')->default(0);
            $table->integer('Stolen')->default(0);
            $table->integer('Recupered')->default(0);
            $table->integer('Captured')->default(0);
            $table->integer('MVP')->default(0);
            $table->integer('Rounds Won')->default(0);
            $table->integer('Played Time')->default(0);
            $table->timestamp('First Login')->nullable();
            $table->timestamp('Last Login')->nullable();
            $table->boolean('Online')->default(false);
            $table->string('Skill')->nullable();
            $table->boolean('Steam')->default(false);
            $table->string('Flags')->default('');
            $table->boolean('New')->default(false);
            $table->string('Avatar')->nullable();
            $table->string('Profile')->nullable();
            $table->integer('Skill Range')->default(0);
            $table->timestamps();

            // Índices
            $table->index(['Steam ID', 'ServerIp']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rank_system');
    }
}