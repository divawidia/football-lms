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
        Schema::table('event_schedules', function (Blueprint $table){
            $table->dropConstrainedForeignId('coachId');
            $table->dropColumn('endDateTime');
            $table->dropColumn('startDateTime');
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->enum('eventType', ['Training', 'Match'])->change();
            $table->enum('matchType', ['Friendly Match', 'Competition'])->change();
            $table->date('date');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('eventName')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_schedules', function (Blueprint $table){
            $table->foreignId('coachId')->constrained('coaches')->cascadeOnDelete();
            $table->datetimes('endDateTime');
            $table->datetimes('startDateTime');
            $table->dropConstrainedForeignId('competitionId');
            $table->enum('eventType', ['training', 'match'])->change();
            $table->enum('matchType', ['friendlyMatch', 'cup', 'league'])->change();
            $table->dropColumn('date');
            $table->dropColumn('startTime');
            $table->dropColumn('endTime');
            $table->string('eventName')->change();
        });
    }
};
