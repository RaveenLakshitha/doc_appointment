<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('generic_name')->nullable()->after('name');
            $table->string('medicine_type')->nullable();
            $table->string('dosage')->nullable(); 
            $table->text('side_effects')->nullable();
            $table->text('precautions_warnings')->nullable();
            $table->decimal('tax_rate', 8, 2)->default(0)->after('unit_price');
            $table->json('storage_conditions')->nullable(); 
            $table->boolean('is_active')->default(true)->index(); 
            $table->string('medicine_image')->nullable(); 
            $table->string('package_image')->nullable(); 
            $table->boolean('expiry_tracking')->default(true)->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn([
                'generic_name', 'medicine_type', 'dosage', 'side_effects',
                'precautions_warnings', 'tax_rate', 'storage_conditions',
                'is_active', 'medicine_image', 'package_image'
            ]);
        });
    }
};