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
            $table->enum('status', [1, 0]);
            $table->string('logo')->nullable()->change();
            $table->string('contactName')->nullable()->change();
            $table->string('contactPhone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('logo')->change();
            $table->string('contactName')->change();
            $table->string('contactPhone')->change();
        });
    }
};
