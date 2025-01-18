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
        Schema::table('coach_match_stats', function (Blueprint $table) {
            $table->dropForeign('coach_match_stats_eventid_foreign');
            $table->renameColumn('eventId', 'matchId');
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coach_match_stats', function (Blueprint $table) {
            $table->dropForeign('coach_match_stats_matchid_foreign');
            $table->renameColumn('matchId', 'eventId');
            $table->foreign('eventId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
