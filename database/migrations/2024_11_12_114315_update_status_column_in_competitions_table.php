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
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', ['Scheduled', 'Completed', 'Ongoing', 'Cancelled'])->default('Scheduled')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', ['0', '1'])->default('1')->change();
        });
    }
};
