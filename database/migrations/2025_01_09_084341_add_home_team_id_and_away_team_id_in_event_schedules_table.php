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
        Schema::table('event_schedules', function (Blueprint $table) {
            $table->foreignId('homeTeamId')->nullable()->constrained('teams');
            $table->foreignId('awayTeamId')->nullable()->constrained('teams');
            $table->foreignId('winnerTeamId')->nullable()->constrained('teams');
            $table->boolean('isExternalTeamWinner')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('homeTeamId');
            $table->dropConstrainedForeignId('awayTeamId');
            $table->dropConstrainedForeignId('winnerTeamId');
            $table->dropColumn('isExternalTeamWinner');
        });
    }
};
