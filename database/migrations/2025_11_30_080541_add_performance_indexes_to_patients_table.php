<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Give each index a custom, guaranteed-unique name
            $table->index(['last_name', 'first_name'], 'idx_patients_last_first_name');
            $table->index('email', 'idx_patients_email');
            $table->index('phone', 'idx_patients_phone');
            $table->index('medical_record_number', 'idx_patients_mrn');
            $table->index('is_active', 'idx_patients_is_active');
            $table->index('deleted_at', 'idx_patients_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_last_first_name');
            $table->dropIndex('idx_patients_email');
            $table->dropIndex('idx_patients_phone');
            $table->dropIndex('idx_patients_mrn');
            $table->dropIndex('idx_patients_is_active');
            $table->dropIndex('idx_patients_deleted_at');
        });
    }
};