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
        Schema::rename('coach_attendance', 'coach_match_attendance');

        Schema::table('coach_match_attendance', function (Blueprint $table) {
            $table->dropForeign('coach_attendance_scheduleid_foreign');
            $table->renameColumn('scheduleId', 'matchId');
            $table->foreign('matchId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('coach_match_attendance', 'coach_attendance');

        Schema::table('coach_attendance', function (Blueprint $table) {
            $table->dropForeign('coach_attendance_matchid_foreign');
            $table->renameColumn('matchId', 'scheduleId');
            $table->foreign('scheduleId')->references('id')->on('matches')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
