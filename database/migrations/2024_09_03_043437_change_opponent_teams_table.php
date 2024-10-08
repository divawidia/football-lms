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
        Schema::table('opponent_teams', function (Blueprint $table){
            $table->dropColumn('division');
            $table->integer('totalPlayers')->nullable();
            $table->string('academyName')->nullable();
            $table->string('coachName')->nullable()->change();
            $table->string('directorName')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opponent_teams', function (Blueprint $table){
            $table->string('division');
            $table->dropColumn('totalPlayers');
            $table->dropColumn('academyName');
            $table->string('coachName')->change();
            $table->string('directorName')->change();
        });
    }
};
