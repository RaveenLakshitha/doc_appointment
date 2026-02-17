<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('unit_of_measure');
            $table->integer('unit_quantity')->default(1);
            $table->string('storage_location')->nullable();
            $table->json('additional_info')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->string('model_version')->nullable();
            $table->boolean('expiry_tracking')->default(false);
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('controlled_substance')->default(false);
            $table->boolean('hazardous_material')->default(false);
            $table->boolean('sterile')->default(false);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->integer('maximum_stock_level')->nullable();
            $table->integer('reorder_point')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->foreignId('primary_supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('supplier_item_code')->nullable();
            $table->decimal('supplier_price', 10, 2)->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->timestamps();

            $table->index('sku');
            $table->index('category_id');
            $table->index('current_stock');
            $table->index('reorder_point');
            $table->index(['current_stock', 'reorder_point']);
        });

        Schema::create('inventory_item_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('supplier_item_code')->nullable();
            $table->decimal('supplier_price', 10, 2)->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->unique(['inventory_item_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_supplier');
        Schema::dropIfExists('inventory_items');
    }
};