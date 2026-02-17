<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppAppointmentRequest extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_appointment_requests';

    protected $fillable = [
        'flow_token',
        'received_at',
        'appointment_type',
        'visit_type',
        'preferred_time',
        'reason',
        'notes',
        'doctor_id',
        'specialization_id',
        'existing_patient_id',
        'new_patient_data',
        'status',
        'source',
        'processed_at',
        'remarks',
    ];

    protected $casts = [
        'received_at'     => 'datetime',
        'processed_at'    => 'datetime',
        'new_patient_data' => 'array',           // JSON → array
        'notes'           => 'string',
        'reason'          => 'string',
    ];

    // Optional: default values / accessors

    protected $attributes = [
        'status' => 'pending',
        'source' => 'whatsapp_flow',
    ];

    // Relationships (if you want them later)

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'existing_patient_id');
    }

    // Helper accessor example
    public function getPatientNameAttribute(): ?string
    {
        if ($this->existing_patient_id && $this->patient) {
            return $this->patient->first_name . ' ' . $this->patient->last_name;
        }

        if ($this->new_patient_data) {
            return ($this->new_patient_data['first_name'] ?? '') . ' ' .
                   ($this->new_patient_data['last_name'] ?? '');
        }

        return null;
    }

    public function getIsNewPatientAttribute(): bool
    {
        return $this->existing_patient_id === null && $this->new_patient_data !== null;
    }
}