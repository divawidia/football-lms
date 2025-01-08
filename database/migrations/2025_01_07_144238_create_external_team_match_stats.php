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
        Schema::create('external_team_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->string('teamName');
            $table->integer('teamScore')->nullable()->default(0);
            $table->integer('teamOwnGoal')->nullable()->default(0);
            $table->integer('teamPossesion')->nullable()->default(0);
            $table->integer('teamShotOnTarget')->nullable()->default(0);
            $table->integer('teamShots')->nullable()->default(0);
            $table->integer('teamTouches')->nullable()->default(0);
            $table->integer('teamTackles')->nullable()->default(0);
            $table->integer('teamClearances')->nullable()->default(0);
            $table->integer('teamCorners')->nullable()->default(0);
            $table->integer('teamOffsides')->nullable()->default(0);
            $table->integer('teamYellowCards')->nullable()->default(0);
            $table->integer('teamRedCards')->nullable()->default(0);
            $table->integer('teamFoulsConceded')->nullable()->default(0);
            $table->enum('resultStatus', ['Draw','Win','Lose'])->nullable();
            $table->integer('teamPasses')->nullable()->default(0);
            $table->integer('goalConceded')->nullable()->default(0);
            $table->integer('cleanSheets')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_team_matches');
    }
};
