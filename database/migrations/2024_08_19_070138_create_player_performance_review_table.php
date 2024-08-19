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
        Schema::create('player_performance_review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->cascadeOnDelete();
            $table->foreignId('coachId')->constrained('coaches')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->nullOnDelete();
            $table->text('performanceReview');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_performance_review');
    }
};
