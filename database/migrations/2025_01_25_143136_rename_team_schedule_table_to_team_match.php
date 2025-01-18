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
        Schema::rename('team_schedule', 'team_match');

        Schema::table('team_match', function (Blueprint $table) {
            $table->dropForeign('team_schedule_eventid_foreign');
            $table->renameColumn('eventId', 'matchId');
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('team_match', 'team_schedule');

        Schema::table('team_schedule', function (Blueprint $table) {
            $table->dropForeign('team_schedule_matchid_foreign');
            $table->renameColumn('matchId', 'eventId');
            $table->foreign('eventId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
