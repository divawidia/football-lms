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
        Schema::table('training_videos', function (Blueprint $table) {
            $table->enum('level', ['Beginner', 'Intermediate', 'Expert'])->change();
            $table->integer('totalLesson')->default(0)->change();
            $table->integer('totalMinute')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_videos', function (Blueprint $table) {
            $table->enum('level', ['beginer', 'intermediate', 'expert'])->change();
            $table->integer('totalLesson')->change();
            $table->integer('totalMinute')->change();
        });
    }
};
