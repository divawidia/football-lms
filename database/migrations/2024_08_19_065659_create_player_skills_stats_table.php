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
        Schema::create('player_skills_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->cascadeOnDelete();
            $table->foreignId('coachId')->constrained('coaches')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->nullOnDelete();
            $table->integer('controlling');
            $table->integer('recieving');
            $table->integer('dribbling');
            $table->integer('passing');
            $table->integer('shooting');
            $table->integer('crossing');
            $table->integer('turning');
            $table->integer('ballHandling');
            $table->integer('powerKicking');
            $table->integer('goalKeeping');
            $table->integer('offensivePlay');
            $table->integer('defensivePlay');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_skills_stats');
    }
};
