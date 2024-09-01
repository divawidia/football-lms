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
            $table->string('logo');
            $table->string('coachName');
            $table->integer('totalPlayers');
            $table->string('contactPhone');
            $table->text('description');
            $table->timestamps();
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
