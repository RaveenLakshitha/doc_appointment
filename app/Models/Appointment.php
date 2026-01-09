<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Type constants
    const TYPE_SPECIFIC         = 'specific';         // Specific doctor selected
    const TYPE_ANY              = 'any';              // Any available doctor

    protected $fillable = [
        'patient_id',
        'doctor_id',           // nullable when TYPE_ANY
        'status',
        'appointment_type',
        'reason_for_visit',
        'doctor_notes',
        'patient_notes',
        'admin_notes',
        'scheduled_start',     // datetime of the appointment
        'scheduled_end',       // datetime of the appointment
        'room_id',             // optional
        'cancelled_at',
        'cancelled_by',        // user_id who cancelled
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end'   => 'datetime',
        'cancelled_at'    => 'datetime',
        'status'          => 'string',
        'appointment_type' => 'string',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class); // assuming you have a Patient model
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_start', '>=', Carbon::now())
                     ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // Accessors
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getIsPastAttribute(): bool
    {
        return $this->scheduled_end && $this->scheduled_end->isPast();
    }
}