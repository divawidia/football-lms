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
        Schema::create('player_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('playerId')->constrained('players')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_teams');
    }
};
