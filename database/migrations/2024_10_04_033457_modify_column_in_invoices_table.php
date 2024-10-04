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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('snapToken')->nullable();
            $table->dropColumn('paymentUrl');
            $table->dropColumn('sentDate');
            $table->enum('status', ['Paid','Open','Past Due','Uncollectible'])->default('Open')->change();
            $table->dropConstrainedForeignId('adminId');
            $table->dropConstrainedForeignId('playerId');
            $table->foreignId('creatorUserId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiverUserId')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('snapToken')->nullable();
                $table->string('paymentUrl');
                $table->dateTime('sentDate');
                $table->enum('status', ['paid','open','pastDue','uncollectible'])->change();
                $table->foreignId('adminId')->nullable()->constrained('admins')->nullOnDelete();
                $table->foreignId('playerId')->nullable()->constrained('players')->nullOnDelete();
                $table->dropConstrainedForeignId('creatorUserId');
                $table->dropConstrainedForeignId('receiverUserId');
            });
        });
    }
};
