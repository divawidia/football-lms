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
        Schema::table('team_schedule', function (Blueprint $table){
            $table->integer('goalConceded')->default(0)->nullable();
            $table->integer('cleanSheets')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_schedule', function (Blueprint $table){
            $table->dropColumn('goalConceded');
            $table->dropColumn('cleanSheets');
        });
    }
};
