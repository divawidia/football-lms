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
        Schema::table('players', function (Blueprint $table){
            $table->dropColumn('position');
            $table->foreignId('positionId')->nullable()->constrained('player_positions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table){
            $table->string('position');
            $table->dropConstrainedForeignId('positionId');
        });
    }
};
