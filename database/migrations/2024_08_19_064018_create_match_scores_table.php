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
            $table->foreignId('playerId')->constrained('players')->nullOnDelete();
            $table->foreignId('assistPlayerId')->constrained('payers')->nullOnDelete();
            $table->foreignId('eventId')->constrained('evenet_schedules')->cascadeOnDelete();
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
