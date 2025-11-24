<?php
// database/migrations/2025_11_17_xxxxxx_add_indexes_to_doctors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->index(['last_name', 'first_name']);
            $table->index('is_active');
            $table->index('primary_specialty');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex(['last_name', 'first_name']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['primary_specialty']);
            $table->dropIndex(['deleted_at']);
        });
    }
};