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
        Schema::create('external_team_match', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->string('teamName');
            $table->integer('teamScore')->nullable();
            $table->integer('teamOwnGoal')->nullable();
            $table->integer('teamPossesion')->nullable();
            $table->integer('teamShotOnTarget')->nullable();
            $table->integer('teamShots')->nullable();
            $table->integer('teamTouches')->nullable();
            $table->integer('teamTackles')->nullable();
            $table->integer('teamClearances')->nullable();
            $table->integer('teamCorners')->nullable();
            $table->integer('teamOffsides')->nullable();
            $table->integer('teamYellowCards')->nullable();
            $table->integer('teamRedCards')->nullable();
            $table->integer('teamFoulsConceded')->nullable();
            $table->enum('resultStatus', ['Draw','Win','Lose'])->nullable();
            $table->integer('teamPasses')->nullable();
            $table->integer('goalConceded')->nullable();
            $table->integer('cleanSheets')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_team_match');
    }
};
