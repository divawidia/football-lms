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
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('eventType');
            $table->dropColumn('eventName');
            $table->dropColumn('isOpponentTeamMatch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->enum('eventType', ['Training', 'Match'])->nullable();
            $table->string('eventName')->nullable();
            $table->boolean('isOpponentTeamMatch')->nullable();
        });
    }
};
