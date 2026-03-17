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
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->nullOnDelete();
        });

        Schema::table('cash_register_transactions', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_transactions', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn('purchase_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['cash_register_id']);
            $table->dropColumn('cash_register_id');
        });
    }
};
