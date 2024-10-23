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
        Schema::create('coach_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coachId')->nullable()->constrained('coaches')->nullOnDelete();
            $table->foreignId('teamId')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->integer('teamScore')->default(0);
            $table->integer('opponentTeamScore')->default(0);
            $table->integer('teamOwnGoal')->default(0);
            $table->integer('teamPossesion')->default(0);
            $table->integer('teamShotOnTarget')->default(0);
            $table->integer('teamShots')->default(0);
            $table->integer('teamTouches')->default(0);
            $table->integer('teamTackles')->default(0);
            $table->integer('teamClearances')->default(0);
            $table->integer('teamCorners')->default(0);
            $table->integer('teamOffsides')->default(0);
            $table->integer('teamYellowCards')->default(0);
            $table->integer('teamRedCards')->default(0);
            $table->integer('teamFoulsConceded')->default(0);
            $table->integer('resultStatus')->default(0);
            $table->integer('teamPasses')->default(0);
            $table->integer('goalConceded')->default(0);
            $table->integer('cleanSheets')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_match_stats');
    }
};
