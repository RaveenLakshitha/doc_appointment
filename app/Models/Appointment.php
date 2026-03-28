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
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RUNNING = 'running';

    // Type constants
    const TYPE_SPECIFIC = 'specific';         // Specific doctor selected
    const TYPE_ANY = 'any';              // Any available doctor

    public static function getPreferredTimeOptions(): array
    {
        return [
            'next' => __('file.next_available'),
            '7days' => __('file.within_7_days'),
            '15days' => __('file.within_15_days'),
        ];
    }

    protected $fillable = [
        'patient_id',
        'doctor_id',           // nullable when TYPE_ANY
        'status',
        'appointment_type',
        'reason_for_visit',
        'doctor_notes',
        'patient_notes',
        'session_key',
        'queue_number',
        'admin_notes',
        'scheduled_start',     // datetime of the appointment
        'scheduled_end',       // datetime of the appointment
        'room_id',             // optional
        'cancelled_at',
        'cancelled_by',        // user_id who cancelled
        'specialization_id',
        'appointment_number',
        'completed_at',
        'completed_by',
        'age_group_id',
        'preferred_language_id',
        'preferred_time',
        'duration_minutes',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => 'string',
        'appointment_type' => 'string',
        'appointment_id' => 'integer', // Assuming integer based on typical ID fields
        'type' => 'string',  // e.g. 'POS', 'Appointment', 'Insurance', 'Manual'
        'age_group_id' => 'integer',
        'preferred_language_id' => 'integer',
        'duration_minutes' => 'integer',
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

    public function getQueueLabelAttribute(): string
    {
        if (!$this->queue_number) {
            return '—';
        }
        return $this->session_key
            ? strtoupper(substr($this->session_key, 0, 1)) . $this->queue_number
            : (string) $this->queue_number;
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function getQueueDisplayAttribute(): string
    {
        if (!$this->queue_number) {
            return '—';
        }

        return $this->session_key
            ? strtoupper($this->session_key) . ' #' . $this->queue_number
            : '#' . $this->queue_number;
    }

    public function isInQueue(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->queue_number !== null;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoices()
    {
        return $this->hasMany(BillingInvoice::class);
    }

    // Add this relationship
    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'appointment_treatment')
            ->withPivot(['quantity', 'price_at_time', 'notes'])
            ->withTimestamps();
    }

    // Helper to get total cost of all treatments
    public function getTotalTreatmentPriceAttribute(): float
    {
        return $this->treatments->sum(
            fn($treatment) => $treatment->pivot->quantity * $treatment->pivot->price_at_time
        );
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
     * Generates a robust sequential appointment number for the current year.
     * Format: VN-YY-000001
     */
    public static function generateNextAppointmentNumber(): string
    {
        $yearSuffix = now()->format('y');
        $prefix = "VN-{$yearSuffix}-";

        // Find the last generated number for this year, including soft-deleted ones.
        // Since we use zero-padding (%06d), alphabetical sort works correctly for sequences.
        $lastAppointment = self::withTrashed()
            ->where('appointment_number', 'like', $prefix . '%')
            ->whereNotNull('appointment_number')
            ->orderBy('appointment_number', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastAppointment) {
            $lastNumber = (int) str_replace($prefix, '', $lastAppointment->appointment_number);
            $nextSeq = $lastNumber + 1;
        }

        return sprintf("%s%06d", $prefix, $nextSeq);
    }
}