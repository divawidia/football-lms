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
        Schema::create('player_training_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainingId')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('playerId')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('teamId')->nullable()->constrained('teams')->nullOnDelete();
            $table->enum('attendanceStatus', ['Required Action', 'Attended', 'Illness', 'Injured', 'Other'])->default('Required Action');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_training_attendance');
    }
};
