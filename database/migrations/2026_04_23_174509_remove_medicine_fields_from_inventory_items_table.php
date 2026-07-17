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
                'generic_name',
                'medicine_type',
                'dosage',
                'side_effects',
                'precautions_warnings',
                'storage_conditions',
                'medicine_image',
                'package_image'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('generic_name')->nullable();
            $table->string('medicine_type')->nullable();
            $table->string('dosage')->nullable();
            $table->string('side_effects')->nullable();
            $table->string('precautions_warnings')->nullable();
            $table->json('storage_conditions')->nullable();
            $table->string('medicine_image')->nullable();
            $table->string('package_image')->nullable();
        });
    }
};
