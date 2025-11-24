<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')
                  ->constrained('inventory_items')
                  ->onDelete('cascade');
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->onDelete('cascade');
            $table->string('supplier_item_code')->nullable();
            $table->decimal('supplier_price', 10, 2)->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->timestamps();

            $table->unique(['inventory_item_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_supplier');
    }
};