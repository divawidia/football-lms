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
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable();
            $table->timestamp('lastSeen')->nullable();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'others']);
            $table->text('address');
            $table->string('state');
            $table->string('city');
            $table->string('country');
            $table->integer('zipCode');
            $table->string('phoneNumber');
            $table->enum('status', [0, 1]);
            $table->foreignId('academyId')->constrained('academies')->cascadeOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
