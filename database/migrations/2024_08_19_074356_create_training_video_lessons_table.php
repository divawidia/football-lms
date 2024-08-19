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
        Schema::create('training_video_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainingVideoId')->constrained('training_videos')->cascadeOnDelete();
            $table->string('lessonTitle');
            $table->text('description')->nullable();
            $table->text('lessonVideoURL');
            $table->integer('totalMinutes');
            $table->enum('status', [1,0]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_video_lessons');
    }
};
