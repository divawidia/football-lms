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
        Schema::create('player_lesson', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->cascadeOnDelete();
            $table->foreignId('lessonId')->constrained('training_video_lessons')->cascadeOnDelete();
            $table->enum('completionStatus', ['1', '0'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_lesson');
    }
};
