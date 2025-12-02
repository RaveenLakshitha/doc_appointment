<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            
            // === Personal Information ===
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->enum('preferred_contact_method', ['phone', 'email', 'sms'])->nullable();

            // === Emergency Contact ===
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();

            // === Medical Profile ===
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable(); // 0–300 cm
            $table->unsignedSmallInteger('weight_kg')->nullable(); // 0–500 kg
            $table->json('allergies')->nullable();
            $table->json('current_medications')->nullable();
            $table->json('chronic_conditions')->nullable();
            $table->json('past_surgeries')->nullable();           // e.g. [{"name": "Appendectomy", "date": "2020-05-12"}]
            $table->json('previous_hospitalizations')->nullable();

            // === Family Medical History ===
            $table->boolean('family_history_diabetes')->default(false);
            $table->boolean('family_history_hypertension')->default(false);
            $table->boolean('family_history_heart_disease')->default(false);
            $table->boolean('family_history_cancer')->default(false);
            $table->boolean('family_history_asthma')->default(false);
            $table->boolean('family_history_mental_health')->default(false);
            $table->text('family_history_notes')->nullable();

            // === Lifestyle ===
            $table->enum('smoking_status', ['never', 'former', 'current'])->nullable();
            $table->enum('alcohol_consumption', ['none', 'occasional', 'moderate', 'heavy'])->nullable();
            $table->enum('exercise_frequency', ['never', 'rarely', 'weekly', 'daily'])->nullable();
            $table->string('dietary_habits')->nullable();

            // === Insurance & Billing ===
            $table->string('primary_insurance_provider')->nullable();
            $table->string('primary_policy_number')->nullable();
            $table->string('primary_group_number')->nullable();
            $table->string('primary_policy_holder_name')->nullable();
            $table->string('primary_relationship_to_patient')->nullable();
            $table->string('primary_insurance_phone')->nullable();

            $table->string('secondary_insurance_provider')->nullable();
            $table->string('secondary_policy_number')->nullable();

            $table->enum('preferred_billing_method', ['insurance_first', 'self_pay', 'insurance_only'])->nullable();
            $table->json('payment_methods')->nullable(); // e.g. ["credit_card", "cash"]

            // === Communication & Consent ===
            $table->boolean('receive_appointment_reminders')->default(true);
            $table->boolean('receive_lab_results')->default(true);
            $table->boolean('receive_prescription_notifications')->default(true);
            $table->boolean('receive_newsletter')->default(false);

            // === Files ===
            $table->string('profile_photo_path')->nullable();
            $table->boolean('consent_hipaa')->default(false);
            $table->boolean('consent_treatment')->default(false);
            $table->boolean('consent_financial')->default(false);
            $table->json('additional_documents')->nullable(); // e.g. ["documents/id.pdf", "documents/insurance.pdf"]

            // === Legacy / System ===
            $table->string('medical_record_number')->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};