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
            $table->integer('teamScore');
            $table->integer('opponentTeamScore');
            $table->integer('teamOwnGoal');
            $table->integer('teamPossesion');
            $table->integer('teamShotOnTarget');
            $table->integer('teamShots');
            $table->integer('teamTouches');
            $table->integer('teamTackles');
            $table->integer('teamClearances');
            $table->integer('teamCorners');
            $table->integer('teamOffsides');
            $table->integer('teamYellowCards');
            $table->integer('teamRedCards');
            $table->integer('teamFoulsConceded');
            $table->integer('resultStatus');
            $table->integer('teamPasses');
            $table->integer('goalConceded');
            $table->integer('cleanSheets');
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
