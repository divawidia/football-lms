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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('location');
            $table->enum('status', ['Scheduled', 'Completed', 'Ongoing', 'Cancelled'])->default('Scheduled');
            $table->date('date');
            $table->time('startTime');
            $table->time('endTime');
            $table->timestamp('startDatetime');
            $table->timestamp('endDatetime');
            $table->boolean('isReminderNotified');
            $table->foreignId('teamId')->constrained('teams')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
