<?php

// app/Models/AppointmentRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequest extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    const DOCTOR_SELECTION_SPECIFIC         = 'specific';
    const DOCTOR_SELECTION_ANY              = 'any';
    const DOCTOR_SELECTION_PRIMARY_PROVIDER = 'primary_provider';

    protected $fillable = [
        'patient_id',
        'specialization_id',
        'doctor_id',                       // When patient picks a specific doctor
        'primary_care_provider_id',        // User ID (from patient.primary_care_provider_id)
        'doctor_selection_mode',           // specific | any | primary_provider
        'doctor_schedule_id',
        'requested_date',
        'requested_start_time',
        'preferred_time_range_start',
        'preferred_time_range_end',
        'duration_minutes',
        'reason_for_visit',
        'notes',
        'status',
        'approved_by',                     // User who approved
        'approved_at',
        'rejected_reason',
        'assigned_doctor_id',              // Filled when primary provider assigns a specialist
        'appointment_id',
    ];

    protected $casts = [
        'requested_date'             => 'date',
        'requested_start_time'       => 'datetime:H:i',
        'preferred_time_range_start' => 'datetime:H:i',
        'preferred_time_range_end'   => 'datetime:H:i',
        'approved_at'                => 'datetime',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id'); // requested specific doctor
    }

    public function primaryCareProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_care_provider_id');
        // This is the User with role 'primary_care_provider'
    }

    public function assignedDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'assigned_doctor_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeNeedsAssignment($query)
    {
        return $query->where('doctor_selection_mode', self::DOCTOR_SELECTION_PRIMARY_PROVIDER)
                     ->where('status', self::STATUS_PENDING)
                     ->whereNull('assigned_doctor_id');
    }
}