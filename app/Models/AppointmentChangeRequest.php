<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentChangeRequest extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_RESCHEDULE = 'reschedule';
    const TYPE_CANCEL     = 'cancel';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'request_type',
        'status',
        'reason',
        'requested_date',
        'requested_time',
        'slot',
        'preferred_time',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'reviewed_at'    => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForAppointment($query, int $appointmentId)
    {
        return $query->where('appointment_id', $appointmentId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReschedule(): bool
    {
        return $this->request_type === self::TYPE_RESCHEDULE;
    }

    public function isCancel(): bool
    {
        return $this->request_type === self::TYPE_CANCEL;
    }

    public function getTypeLabel(): string
    {
        return $this->request_type === self::TYPE_RESCHEDULE
            ? __('file.reschedule') ?? 'Reschedule'
            : __('file.cancel_appointment') ?? 'Cancel';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => __('file.pending')  ?? 'Pending',
            self::STATUS_APPROVED => __('file.approved') ?? 'Approved',
            self::STATUS_REJECTED => __('file.rejected') ?? 'Rejected',
            default               => $this->status,
        };
    }
}
