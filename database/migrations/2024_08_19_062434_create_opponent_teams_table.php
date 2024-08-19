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
        Schema::create('opponent_teams', function (Blueprint $table) {
            $table->id();
            $table->string('teamName');
            $table->string('ageGroup');
            $table->string('division');
            $table->string('logo')->nullable();
            $table->string('coachName');
            $table->string('directorName');
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
        Schema::dropIfExists('opponent_teams');
    }
};
