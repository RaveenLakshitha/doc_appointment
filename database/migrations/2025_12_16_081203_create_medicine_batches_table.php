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
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')
                  ->constrained('inventory_items')
                  ->onDelete('cascade')
                  ->index();

            $table->string('batch_number')->index(); // For quick lookup
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->index(); // Important for expiry alerts and sorting
            $table->integer('initial_quantity');
            $table->integer('current_quantity')->default(0);

            $table->timestamps();

            // Useful index for finding expired or near-expiry batches
            $table->index(['inventory_item_id', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};