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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('tax_id')->nullable();
            $table->string('tax_full_name')->nullable();
            $table->string('tax_postal_code')->nullable();
            $table->string('tax_regime')->nullable();
            $table->string('tax_invoice_usage')->nullable();
            $table->string('tax_cfdi_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'tax_id',
                'tax_full_name',
                'tax_postal_code',
                'tax_regime',
                'tax_invoice_usage',
                'tax_cfdi_path',
            ]);
        });
    }
};
