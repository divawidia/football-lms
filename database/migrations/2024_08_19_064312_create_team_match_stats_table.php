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
        Schema::create('team_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->integer('teamScore');
            $table->integer('opponentTeamScore');
            $table->integer('teamOwnGoal');
            $table->integer('opponentTeamOwnGoal');
            $table->integer('teamPossesion');
            $table->integer('opponentTeamPossesion');
            $table->integer('teamShotOnTarget');
            $table->integer('opponentTeamShotOnTarget');
            $table->integer('teamShots');
            $table->integer('opponentTeamShots');
            $table->integer('teamTouches');
            $table->integer('opponentTeamTouches');
            $table->integer('teamTackles');
            $table->integer('opponentTeamTackles');
            $table->integer('teamClearances');
            $table->integer('opponentTeamClearances');
            $table->integer('teamCorners');
            $table->integer('opponentTeamCorners');
            $table->integer('teamOffsides');
            $table->integer('opponentOffsides');
            $table->integer('teamYellowCards');
            $table->integer('opponentTeamYellowCards');
            $table->integer('teamRedCards');
            $table->integer('opponentTeamRedCards');
            $table->integer('teamFoulsConceded');
            $table->integer('opponentFoulsConceded');
            $table->integer('resultStatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_match_stats');
    }
};
