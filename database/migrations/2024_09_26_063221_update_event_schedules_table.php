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
        Schema::table('event_schedules', function (Blueprint $table){
            $table->foreignId('teamWinner')->nullable()->constrained('teams')->cascadeOnDelete();
            $table->enum('isDraw', [1, 0])->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_schedules', function (Blueprint $table){
            $table->dropConstrainedForeignId('teamWinner');
            $table->dropColumn('isDraw', [1, 0]);
        });
    }
};
