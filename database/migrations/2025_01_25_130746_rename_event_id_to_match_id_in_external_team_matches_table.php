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
        Schema::table('external_team_matches', function (Blueprint $table) {
            $table->dropForeign('external_team_matches_eventid_foreign');
            $table->renameColumn('eventId', 'matchId');
        });
        Schema::table('external_team_matches', function (Blueprint $table) {
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_team_matches', function (Blueprint $table) {
            $table->dropForeign('external_team_matches_matchid_foreign');
            $table->renameColumn('matchId', 'eventId');
        });
        Schema::table('external_team_matches', function (Blueprint $table) {
            $table->foreign('eventId')->references('id')->on('matches')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
