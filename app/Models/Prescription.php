<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'prescription_date',
        'appointment_id',
        'type',
        'diagnosis',
        'notes',
    ];

    protected $casts = [
        'prescription_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medications()
    {
        return $this->hasMany(PrescriptionMedication::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}