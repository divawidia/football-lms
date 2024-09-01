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
        Schema::create('competition_opponent_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->integer('matchPlayed')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goalsFor')->default(0);
            $table->integer('goalsAgaints')->default(0);
            $table->integer('goalsDifference')->default(0);
            $table->integer('points')->default(0);
            $table->integer('redCards')->default(0);
            $table->integer('yellowCards')->default(0);
            $table->string('groupDivision')->nullable();
            $table->string('competitionResult')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_opponent_team');
    }
};
