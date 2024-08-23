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
            $table->dropColumn('name');
            $table->string('firstName');
            $table->string('lastName');
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->dropColumn('firstName');
            $table->dropColumn('lastName');
            $table->dropColumn('foto');
            $table->dropColumn('lastSeen');
            $table->dropColumn('dob');
            $table->dropColumn('gender', ['male', 'female', 'others']);
            $table->dropColumn('address');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('country');
            $table->dropColumn('zipCode');
            $table->dropColumn('phoneNumber');
            $table->dropColumn('status', [0, 1]);
            $table->dropConstrainedForeignId('academyId');
            $table->dropSoftDeletes();
        });
    }
};
