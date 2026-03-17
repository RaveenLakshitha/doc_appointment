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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('item')->nullable()->constrained('inventory_items')->onDelete('set null');
            $table->integer('total_qty')->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('order_tax', 10, 2)->default(0);
            $table->decimal('order_discount', 10, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: Received, 2: Partial, 3: Pending, 4: Ordered');
            $table->tinyInteger('payment_status')->default(1)->comment('1: Due, 2: Paid');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
