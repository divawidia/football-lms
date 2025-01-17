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
        Schema::rename('event_schedules', 'matches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('matches', 'event_schedules');
    }
};
