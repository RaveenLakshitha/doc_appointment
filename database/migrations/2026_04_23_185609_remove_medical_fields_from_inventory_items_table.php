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
            $table->dropColumn([
                'controlled_substance',
                'hazardous_material',
                'sterile',
                'additional_info'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->boolean('controlled_substance')->default(false);
            $table->boolean('hazardous_material')->default(false);
            $table->boolean('sterile')->default(false);
            $table->text('additional_info')->nullable();
        });
    }
};
