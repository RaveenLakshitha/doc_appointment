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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->nullable()->change();
            $table->integer('current_stock')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->default(0)->nullable(false)->change();
            $table->integer('current_stock')->default(0)->nullable(false)->change();
        });
    }
};
