<?php
// database/migrations/2025_11_17_xxxxxx_add_performance_indexes_to_patients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Fast name search (most common)
            $table->index(['last_name', 'first_name']);

            // Email & phone lookup (login, search)
            $table->index('email');
            $table->index('phone');

            // Medical record number (unique ID)
            $table->index('medical_record_number');

            // Active patients filter (99% of queries)
            $table->index('is_active');

            // Soft delete performance
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['last_name', 'first_name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['medical_record_number']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['deleted_at']);
        });
    }
};