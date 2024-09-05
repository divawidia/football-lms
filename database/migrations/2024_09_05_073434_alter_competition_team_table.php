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
        Schema::table('competition_team', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opponentTeamId');
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
            $table->integer('matchPlayed')->default(0)->nullable()->change();
            $table->integer('won')->default(0)->nullable()->change();
            $table->integer('drawn')->default(0)->nullable()->change();
            $table->integer('lost')->default(0)->nullable()->change();
            $table->integer('goalsFor')->default(0)->nullable()->change();
            $table->integer('goalsAgaints')->default(0)->nullable()->change();
            $table->integer('goalsDifference')->default(0)->nullable()->change();
            $table->integer('points')->default(0)->nullable()->change();
            $table->integer('redCards')->default(0)->nullable()->change();
            $table->integer('yellowCards')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_team', function (Blueprint $table) {
            $table->foreignId('opponentTeamId')->constrained('teams')->cascadeOnDelete();
            $table->dropConstrainedForeignId('teamId');
            $table->integer('matchPlayed')->default(0)->change();
            $table->integer('won')->default(0)->change();
            $table->integer('drawn')->default(0)->change();
            $table->integer('lost')->default(0)->change();
            $table->integer('goalsFor')->default(0)->change();
            $table->integer('goalsAgaints')->default(0)->change();
            $table->integer('goalsDifference')->default(0)->change();
            $table->integer('points')->default(0)->change();
            $table->integer('redCards')->default(0)->change();
            $table->integer('yellowCards')->default(0)->change();
        });
    }
};
