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
        Schema::table('match_scores', function (Blueprint $table){
            $table->enum('isOwnGoal', [1,0])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_scores', function (Blueprint $table){
            $table->dropColumn('isOwnGoal', [1,0]);
        });
    }
};
