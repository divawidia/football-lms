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
        Schema::create('league_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams');
            $table->foreignId('competitionId')->constrained('competitions');
            $table->integer('matchPlayed')->default(0)->nullable();
            $table->integer('won')->default(0)->nullable();
            $table->integer('drawn')->default(0)->nullable();
            $table->integer('lost')->default(0)->nullable();
            $table->integer('goalsFor')->default(0)->nullable();
            $table->integer('goalsAgainst')->default(0)->nullable();
            $table->integer('goalsDifference')->default(0)->nullable();
            $table->integer('points')->default(0)->nullable();
            $table->integer('standingPositions')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_standings');
    }
};
