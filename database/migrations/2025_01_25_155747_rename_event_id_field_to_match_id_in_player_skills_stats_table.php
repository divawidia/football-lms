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
        Schema::table('player_skills_stats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('eventId');
            $table->foreignId('matchId')->nullable()->constrained('matches')->cascadeOnDelete();
            $table->foreignId('trainingId')->nullable()->constrained('trainings')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_skills_stats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matchId');
            $table->foreignId('eventId')->nullable()->constrained('matches')->cascadeOnDelete();
            $table->dropConstrainedForeignId('trainingId');
        });
    }
};
