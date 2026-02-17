<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('appointments', 'queue_number')) {
                $table->unsignedInteger('queue_number')->nullable()->after('scheduled_start');
            }

            if (!Schema::hasColumn('appointments', 'session_key')) {
                $table->string('session_key', 40)->nullable()->after('queue_number');
            }

            // Add index only if it doesn't exist
            $indexName = 'appointments_doctor_id_scheduled_start_index';
            if (!Schema::hasIndex('appointments', $indexName)) {
                $table->index(['doctor_id', 'scheduled_start'], $indexName);
            }

            // Optional second index
            $indexName2 = 'appointments_doctor_session_scheduled_index'; // better descriptive name
            if (!Schema::hasIndex('appointments', $indexName2)) {
                $table->index(['doctor_id', 'session_key', 'scheduled_start'], $indexName2);
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['queue_number', 'session_key']);

            $table->dropIndex('appointments_doctor_id_scheduled_start_index');
            $table->dropIndex('appointments_doctor_session_scheduled_index');
        });
    }
};
