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
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('city_id');
//            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
//            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
//            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
//            $table->dropConstrainedForeignId('country_id');
//            $table->dropConstrainedForeignId('state_id');
//            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn('country_id');
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->string('country');
            $table->string('state');
            $table->string('city');
        });
    }
};
