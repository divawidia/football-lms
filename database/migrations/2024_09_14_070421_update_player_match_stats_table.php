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
        Schema::table('player_match_stats', function (Blueprint $table){
            $table->integer('minutesPlayed')->default(0)->nullable()->change();
            $table->integer('goals')->default(0)->nullable()->change();
            $table->integer('assists')->default(0)->nullable()->change();
            $table->integer('ownGoal')->default(0)->nullable()->change();
            $table->integer('shots')->default(0)->nullable()->change();
            $table->integer('passes')->default(0)->nullable()->change();
            $table->integer('fouls')->default(0)->nullable()->change();
            $table->integer('yellowCards')->default(0)->nullable()->change();
            $table->integer('redCards')->default(0)->nullable()->change();
            $table->integer('saves')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_match_stats', function (Blueprint $table){
            $table->integer('minutesPlayed')->change();
            $table->integer('goals')->change();
            $table->integer('assists')->change();
            $table->integer('ownGoal')->change();
            $table->integer('shots')->change();
            $table->integer('passes')->change();
            $table->integer('fouls')->change();
            $table->integer('yellowCards')->change();
            $table->integer('redCards')->change();
            $table->integer('saves')->change();
        });
    }
};
