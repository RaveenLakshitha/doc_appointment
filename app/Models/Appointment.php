<?php
// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_datetime',
        'duration_minutes',
        'status',
        'notes',
        'appointment_type',      // new
        'reason_for_visit',      // new
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'duration_minutes'     => 'integer',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_datetime', '>', now());
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }
}