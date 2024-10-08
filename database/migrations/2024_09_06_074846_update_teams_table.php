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
        Schema::table('teams', function (Blueprint $table){
            $table->enum('teamSide', ['Academy Team', 'Opponent Team']);
            $table->dropConstrainedForeignId('academyId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table){
            $table->dropColumn('teamSide');
            $table->foreignId('academyId')->constrained('academies');
        });
    }
};
