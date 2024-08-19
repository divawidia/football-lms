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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adminId')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('categoryId')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('productName');
            $table->integer('price');
            $table->text('description')->nullable();
            $table->enum('priceOption', ['subscription', 'one time payment']);
            $table->enum('subscriptionCycle', ['monthly', 'quarterly', 'semianually', 'anually'])->nullable();
            $table->enum('status', [1,0]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
