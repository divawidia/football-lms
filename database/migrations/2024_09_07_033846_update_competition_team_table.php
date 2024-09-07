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
        Schema::table('competition_team', function (Blueprint $table){
            $table->dropConstrainedForeignId('competitionId');
            $table->dropColumn('groupDivision');
            $table->foreignId('divisionId')->constrained('group_divisions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_team', function (Blueprint $table){
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->string('groupDivision');
            $table->dropConstrainedForeignId('divisionId');
        });
    }
};
