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
        Schema::table('event_schedules', function (Blueprint $table) {
            $table->enum('status',  ['Scheduled', 'Completed', 'Ongoing', 'Cancelled'])->default('Scheduled')->change();
            $table->enum('isOpponentTeamMatch', ['1','0'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_schedules', function (Blueprint $table) {
            $table->dropColumn('isOpponentTeamMatch');
            $table->enum('status', ['0', '1'])->default('1')->change();
        });
    }
};
