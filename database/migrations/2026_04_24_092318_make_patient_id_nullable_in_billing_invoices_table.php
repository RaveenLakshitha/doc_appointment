<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Make patient_id nullable on billing_invoices so that POS sales
     * can be created for Customers without requiring a Patient record.
     */
    public function up(): void
    {
        // Drop the FK if it exists (safely)
        try {
            Schema::table('billing_invoices', function (Blueprint $table) {
                $table->dropForeign(['patient_id']);
            });
        } catch (\Throwable $e) {
            // FK doesn't exist — that's fine, carry on
        }

        // Alter the column to be nullable using a raw statement
        DB::statement('ALTER TABLE billing_invoices MODIFY COLUMN patient_id BIGINT UNSIGNED NULL');

        // Re-add FK as nullable (set null on delete)
        try {
            Schema::table('billing_invoices', function (Blueprint $table) {
                $table->foreign('patient_id')
                      ->references('id')
                      ->on('patients')
                      ->onDelete('set null');
            });
        } catch (\Throwable $e) {
            // FK may already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('billing_invoices', function (Blueprint $table) {
                $table->dropForeign(['patient_id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        DB::statement('ALTER TABLE billing_invoices MODIFY COLUMN patient_id BIGINT UNSIGNED NOT NULL');

        try {
            Schema::table('billing_invoices', function (Blueprint $table) {
                $table->foreign('patient_id')
                      ->references('id')
                      ->on('patients')
                      ->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
