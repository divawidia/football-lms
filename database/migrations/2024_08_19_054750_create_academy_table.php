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
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phoneNumber');
            $table->string('academyName')->unique();
            $table->text('address');
            $table->string('state');
            $table->string('city');
            $table->string('country');
            $table->integer('zipCode');
            $table->string('directorName');
            $table->enum('status', [0, 1]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academies');
    }
};
