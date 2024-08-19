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
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teamId')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('userId')->constrained('users')->nullOnDelete();
            $table->foreignId('opponentTeamsId')->constrained('opponent_teams')->nullOnDelete();
            $table->unsignedBigInteger('opponentTeamsId')->nullable();
            $table->enum('eventType', ['training', 'match']);
            $table->enum('matchType', ['friendlyMatch', 'cup', 'league'])->nullable();
            $table->string('eventName');
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
            $table->string('place');
            $table->text('note')->nullable();
            $table->enum('status', [1,0]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
