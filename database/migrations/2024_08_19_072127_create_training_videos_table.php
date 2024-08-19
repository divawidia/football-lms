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
        Schema::create('training_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->string('trainingTitle');
            $table->text('description')->nullable();
            $table->string('previewPhoto');
            $table->integer('totalLesson');
            $table->integer('totalMinute');
            $table->enum('level', ['beginer', 'intermediate', 'expert']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_videos');
    }
};
