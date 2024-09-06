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
        Schema::drop('opponent_teams');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('opponent_teams', function (Blueprint $table) {
            $table->id();
            $table->string('teamName');
            $table->string('ageGroup');
            $table->string('logo')->nullable();
            $table->string('coachName');
            $table->string('directorName');
            $table->enum('status', [1,0]);
            $table->integer('totalPlayers');
            $table->string('academyName');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
