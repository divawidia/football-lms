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
        Schema::table('training_video_players', function (Blueprint $table) {
            $table->enum('status', ['Completed', 'On Progress'])->default('on Progress')->change();
            $table->integer('progress')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_video_players', function (Blueprint $table) {
            $table->enum('status', ['completed', 'onProgress'])->change();
            $table->integer('progress')->change();
        });
    }
};
