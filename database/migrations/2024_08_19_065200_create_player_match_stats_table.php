<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->integer('minutesPlayed');
            $table->integer('goals');
            $table->integer('assists');
            $table->integer('ownGoal');
            $table->integer('shots');
            $table->integer('passes');
            $table->integer('fouls');
            $table->integer('yellowCards');
            $table->integer('redCards');
            $table->integer('saves');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};
