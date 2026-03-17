<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'receive_appointment_reminders',
                'receive_lab_results',
                'receive_prescription_notifications',
                'receive_newsletter',
                'profile_photo_path',
                'consent_hipaa',
                'consent_treatment',
                'consent_financial',
                'additional_documents'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('receive_appointment_reminders')->default(true);
            $table->boolean('receive_lab_results')->default(true);
            $table->boolean('receive_prescription_notifications')->default(true);
            $table->boolean('receive_newsletter')->default(false);
            $table->string('profile_photo_path')->nullable();
            $table->boolean('consent_hipaa')->default(false);
            $table->boolean('consent_treatment')->default(false);
            $table->boolean('consent_financial')->default(false);
            $table->json('additional_documents')->nullable();
        });
    }
};
