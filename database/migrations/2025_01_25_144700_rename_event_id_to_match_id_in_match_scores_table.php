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
        Schema::table('match_scores', function (Blueprint $table) {
            $table->dropForeign('match_scores_eventid_foreign');
            $table->renameColumn('eventId', 'matchId');
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_scores', function (Blueprint $table) {
            $table->dropForeign('match_scores_matchid_foreign');
            $table->renameColumn('matchId', 'eventId');
            $table->foreign('eventId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
