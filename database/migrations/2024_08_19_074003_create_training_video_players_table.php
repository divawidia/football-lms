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
        Schema::create('training_video_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->nullOnDelete();
            $table->foreignId('trainingVideoId')->constrained('training_videos')->nullOnDelete();
            $table->integer('progress');
            $table->enum('status', ['completed', 'onProgress']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_video_players');
    }
};
