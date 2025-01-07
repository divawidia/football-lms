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
            $table->boolean('isInternal')->nullable();
            $table->enum('type', ['league', 'Knockout', 'Friendly'])->nullable()->change();
            $table->dropColumn('contactPhone');
            $table->dropColumn('contactName');
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('isInternal');
            $table->enum('type', ['League', 'Tournament'])->nullable()->change();
            $table->string('contactPhone')->nullable();
            $table->string('contactName')->nullable();
            $table->text('description')->nullable();
        });
    }
};
