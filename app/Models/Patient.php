<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // === Personal Information ===
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'age',
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
        'attended_psychotherapy',
        'preferred_session_time',
        'recommended_by',
        'recommended_document',
        
        // === Preferences ===
        'age_group_id',
        'preferred_language_id',

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

        'preferred_billing_method',
        'payment_methods',

        'tax_id',
        'tax_full_name',
        'tax_postal_code',
        'tax_regime',
        'tax_invoice_usage',
        'tax_cfdi_path',

        'medical_record_number',
        'is_active',
        'is_deleted',
        'primary_care_provider_id',
        'document',
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',

        // Booleans
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',

        // Integers
        'height_cm' => 'integer',
        'weight_kg' => 'integer',

        // JSON / Arrays
        'allergies' => 'array',
        'current_medications' => 'array',
        'chronic_conditions' => 'array',
        'past_surgeries' => 'array',
        'previous_hospitalizations' => 'array',
        'payment_methods' => 'array',
        'family_history_diabetes' => 'boolean',
        'family_history_hypertension' => 'boolean',
        'family_history_heart_disease' => 'boolean',
        'family_history_cancer' => 'boolean',
        'family_history_asthma' => 'boolean',
        'family_history_mental_health' => 'boolean',
        'age' => 'integer',
        'attended_psychotherapy' => 'boolean',
    ];

    // === Relationships ===
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    // === Accessors ===
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    // === Scopes ===
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_deleted', false);
    }

    public function primaryCareProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_care_provider_id');
    }

    public function invoices()
    {
        return $this->hasMany(BillingInvoice::class);
    }

    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function preferredLanguage(): BelongsTo
    {
        return $this->belongsTo(OptionList::class, 'preferred_language_id');
    }

    /**
     * Get CFDI usage options with their translation keys.
     *
     * @return array
     */
    public static function getCfdiUsageOptions(): array
    {
        return [
            'D01' => 'file.cfdi_d01',
            'D02' => 'file.cfdi_d02',
            'D03' => 'file.cfdi_d03',
            'D05' => 'file.cfdi_d05',
            'D10' => 'file.cfdi_d10',
            'G01' => 'file.cfdi_g01',
            'G03' => 'file.cfdi_g03',
            'I01' => 'file.cfdi_i01',
            'I02' => 'file.cfdi_i02',
        ];
    }

    /**
     * Get translated label for tax_invoice_usage.
     *
     * @return string
     */
    public function getTaxInvoiceUsageLabelAttribute(): string
    {
        if (!$this->tax_invoice_usage) {
            return __('file.not_specified');
        }

        $options = self::getCfdiUsageOptions();
        if (isset($options[$this->tax_invoice_usage])) {
            return __($options[$this->tax_invoice_usage]);
        }

        return $this->tax_invoice_usage;
    }

}