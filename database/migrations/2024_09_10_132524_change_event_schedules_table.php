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
            $table->dropConstrainedForeignId('teamId');
            $table->dropColumn('endDateTime');
            $table->dropColumn('startDateTime');
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->enum('eventType', ['Training', 'Match'])->change();
            $table->enum('matchType', ['Friendly Match', 'Competition'])->nullable()->change();
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
            $table->string('eventName')->change();
            $table->dropColumn('endTime');
            $table->dropColumn('startTime');
            $table->dropColumn('date');
            $table->enum('matchType', ['friendlyMatch', 'cup', 'league'])->change();
            $table->enum('eventType', ['training', 'match'])->change();
            $table->dropConstrainedForeignId('competitionId');
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
            $table->foreignId('coachId')->constrained('coaches')->cascadeOnDelete();
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
        });
    }
};
