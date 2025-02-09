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
        Schema::dropIfExists('group_divisions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitionId')->constrained('competitions')->cascadeOnDelete();
            $table->string('groupName');
            $table->timestamps();
        });
    }
};
