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
        Schema::table('academies', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('country');
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('city_id');
//            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
//            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
//            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academies', function (Blueprint $table) {
            $table->string('state');
            $table->string('city');
            $table->string('country');
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('country_id');
        });
    }
};
