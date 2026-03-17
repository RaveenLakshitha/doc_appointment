<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescription_medications', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_medications', 'inventory_item_id')) {
                $table->foreignId('inventory_item_id')->nullable()->after('prescription_id')->constrained('inventory_items')->onDelete('set null');
            }
            if (!Schema::hasColumn('prescription_medications', 'per_day')) {
                $table->integer('per_day')->default(1)->after('frequency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_medications', function (Blueprint $table) {
            $table->dropColumn(['per_day', 'inventory_item_id']);
        });
    }
};
