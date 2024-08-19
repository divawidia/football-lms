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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playerId')->constrained('players')->nullOnDelete();
            $table->foreignId('adminId')->constrained('admins')->nullOnDelete();
            $table->foreignId('academyId')->constrained('academies')->cascadeOnDelete();
            $table->foreignId('taxId')->constrained('taxes')->nullOnDelete();
            $table->string('invoiceNumber')->unique();
            $table->dateTime('dueDate');
            $table->integer('ammountDue');
            $table->dateTime('sentDate');
            $table->integer('totalTax')->nullable();
            $table->integer('subtotal')->nullable();
            $table->text('paymentURL');
            $table->enum('status', ['paid','open', 'pastDue', 'uncollectible']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
