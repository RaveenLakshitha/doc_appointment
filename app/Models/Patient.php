<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // === Personal Information ===
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'alternative_phone',
        'email',
        'preferred_contact_method', // phone, email, sms

        // === Emergency Contact ===
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_email',

        // === Medical Profile ===
        'blood_type',
        'height_cm',
        'weight_kg',
        'allergies',
        'current_medications',
        'chronic_conditions',
        'past_surgeries',           // JSON or text with dates
        'previous_hospitalizations', // JSON or text with dates

        // === Family Medical History ===
        'family_history_diabetes',
        'family_history_hypertension',
        'family_history_heart_disease',
        'family_history_cancer',
        'family_history_asthma',
        'family_history_mental_health',
        'family_history_notes',

        // === Lifestyle ===
        'smoking_status',           // never, former, current
        'alcohol_consumption',      // none, occasional, moderate, heavy
        'exercise_frequency',       // never, rarely, weekly, daily
        'dietary_habits',

        // === Insurance & Billing ===
        'primary_insurance_provider',
        'primary_policy_number',
        'primary_group_number',
        'primary_policy_holder_name',
        'primary_relationship_to_patient',
        'primary_insurance_phone',

        'secondary_insurance_provider',
        'secondary_policy_number',

        'preferred_billing_method', // insurance_first, self_pay, etc.
        'payment_methods',          // JSON: ['credit_card', 'debit_card', 'cash', ...]

        'receive_appointment_reminders',
        'receive_lab_results',
        'receive_prescription_notifications',
        'receive_newsletter',

        'profile_photo_path',
        'consent_hipaa',
        'consent_treatment',
        'consent_financial',
        'additional_documents',     

        'medical_record_number',
        'is_active',
        'is_deleted',
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',

        // Booleans
        'is_active'                     => 'boolean',
        'is_deleted'                    => 'boolean',
        'receive_appointment_reminders' => 'boolean',
        'receive_lab_results'           => 'boolean',
        'receive_prescription_notifications' => 'boolean',
        'receive_newsletter'            => 'boolean',
        'consent_hipaa'                 => 'boolean',
        'consent_treatment'             => 'boolean',
        'consent_financial'             => 'boolean',

        // Integers
        'height_cm' => 'integer',
        'weight_kg' => 'integer',

        // JSON / Arrays
        'allergies'                  => 'array',
        'current_medications'        => 'array',
        'chronic_conditions'         => 'array',
        'past_surgeries'             => 'array',
        'previous_hospitalizations'  => 'array',
        'payment_methods'            => 'array',
        'additional_documents'       => 'array',
        'family_history_diabetes'    => 'boolean',
        'family_history_hypertension'=> 'boolean',
        'family_history_heart_disease'=> 'boolean',
        'family_history_cancer'      => 'boolean',
        'family_history_asthma'      => 'boolean',
        'family_history_mental_health'=> 'boolean',
    ];

    // === Relationships ===
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // === Accessors ===
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? Storage::disk('public')->url($this->profile_photo_path)
            : null;
    }

    // === Scopes ===
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_deleted', false);
    }
    
}