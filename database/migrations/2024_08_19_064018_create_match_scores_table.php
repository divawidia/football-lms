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
        Schema::create('match_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('assistPlayerId')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('eventId')->constrained('event_schedules')->cascadeOnDelete();
            $table->integer('minuteScored');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_scores');
    }
};
