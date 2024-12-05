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
        Schema::table('player_attendance', function (Blueprint $table) {
            $table->foreignId('teamId')->nullable()->constrained('teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_attendance', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teamId');
        });
    }
};
