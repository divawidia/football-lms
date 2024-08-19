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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->nullOnDelete();
            $table->enum('cycle', ['monthly', 'quarterly', 'semianually', 'anually']);
            $table->date('startDate');
            $table->date('nextDueDate');
            $table->integer('ammountDue');
            $table->enum('status', ['scheduled', 'unsubscribed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
