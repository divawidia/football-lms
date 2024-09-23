<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_schedule', function (Blueprint $table){
            $table->integer('teamScore')->default(0)->nullable()->change();
            $table->integer('teamOwnGoal')->default(0)->nullable()->change();
            $table->integer('teamPossesion')->default(0)->nullable()->change();
            $table->integer('teamShotOnTarget')->default(0)->nullable()->change();
            $table->integer('teamShots')->default(0)->nullable()->change();
            $table->integer('teamTouches')->default(0)->nullable()->change();
            $table->integer('teamTackles')->default(0)->nullable()->change();
            $table->integer('teamClearances')->default(0)->nullable()->change();
            $table->integer('teamCorners')->default(0)->nullable()->change();
            $table->integer('teamOffsides')->default(0)->nullable()->change();
            $table->integer('teamYellowCards')->default(0)->nullable()->change();
            $table->integer('teamRedCards')->default(0)->nullable()->change();
            $table->integer('teamFoulsConceded')->default(0)->nullable()->change();
            $table->enum('resultStatus', ['Draw', 'Win', 'Lose'])->nullable()->change();
            $table->integer('teamPasses')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_schedule', function (Blueprint $table){
            $table->integer('teamScore')->nullable()->change();
            $table->integer('teamOwnGoal')->nullable()->change();
            $table->integer('teamPossesion')->nullable()->change();
            $table->integer('teamShotOnTarget')->nullable()->change();
            $table->integer('teamShots')->nullable()->change();
            $table->integer('teamTouches')->nullable()->change();
            $table->integer('teamTackles')->nullable()->change();
            $table->integer('teamClearances')->nullable()->change();
            $table->integer('teamCorners')->nullable()->change();
            $table->integer('teamOffsides')->nullable()->change();
            $table->integer('teamYellowCards')->nullable()->change();
            $table->integer('teamRedCards')->nullable()->change();
            $table->integer('teamFoulsConceded')->nullable()->change();
            $table->integer('resultStatus')->nullable()->change();
            $table->integer('teamPasses')->nullable()->change();
        });
    }
};
