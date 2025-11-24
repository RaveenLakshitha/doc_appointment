<?php
// database/migrations/2025_11_06_xxxxxx_add_deleted_at_to_patients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes(); // Adds `deleted_at` timestamp nullable
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Drops `deleted_at`
        });
    }
};