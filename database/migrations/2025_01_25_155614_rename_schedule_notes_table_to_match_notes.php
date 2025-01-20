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
        Schema::rename('schedule_notes', 'match_notes');
        Schema::table('match_notes', function (Blueprint $table) {
            $table->dropForeign('schedule_notes_scheduleid_foreign');
            $table->renameColumn('scheduleId', 'matchId');
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('match_notes', 'schedule_notes');
        Schema::table('schedule_notes', function (Blueprint $table) {
            $table->dropForeign('schedule_notes_matchid_foreign');
            $table->renameColumn('matchId', 'scheduleId');
            $table->foreign('scheduleId')->references('id')->on('matches')->cascadeOnDelete();
        });
    }
};
