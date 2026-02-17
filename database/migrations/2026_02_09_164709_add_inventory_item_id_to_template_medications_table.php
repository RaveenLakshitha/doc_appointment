<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('template_medications', function (Blueprint $table) {
        $table->foreignId('inventory_item_id')
              ->nullable()
              ->constrained('inventory_items')
              ->onDelete('set null');  // or 'restrict' / 'cascade'

        // Optional: make name nullable if you're going to derive it from inventory item
        $table->string('name')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('template_medications', function (Blueprint $table) {
        $table->dropForeign(['inventory_item_id']);
        $table->dropColumn('inventory_item_id');
        // If you made name nullable, revert if needed
    });
}
};
