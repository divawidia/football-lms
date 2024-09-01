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
        Schema::create('competition_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->integer('matchPlayed');
            $table->integer('won');
            $table->integer('drawn');
            $table->integer('lost');
            $table->integer('goalsFor');
            $table->integer('goalsAgaints');
            $table->integer('goalsDifference');
            $table->integer('points');
            $table->integer('competitionResult');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_team');
    }
};
