<?php
// database/migrations/2025_11_17_xxxxxx_add_performance_indexes_to_appointments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Doctor's calendar (MOST IMPORTANT)
            $table->index(['doctor_id', 'appointment_datetime']);

            // Patient history
            $table->index(['patient_id', 'appointment_datetime']);

            // Today’s appointments / Dashboard
            $table->index('appointment_datetime');

            // Status filters: scheduled, waitlist, completed
            $table->index('status');

            // Soft delete
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'appointment_datetime']);
            $table->dropIndex(['patient_id', 'appointment_datetime']);
            $table->dropIndex(['appointment_datetime']);
            $table->dropIndex(['status']);
            $table->dropIndex(['deleted_at']);
        });
    }
};