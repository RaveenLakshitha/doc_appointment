<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'notes',
        'marked_by',
        'marked_at',
        'ip_address',
        'leave_request_id',     // ← make sure this is here
    ];

    protected $casts = [
        'date'      => 'date:Y-m-d',
        'clock_in'  => 'datetime:H:i:s',
        'clock_out' => 'datetime:H:i:s',
        'marked_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'marked_by');
    }

    // ────────────────────────────────────────────────
    //  This is the missing relationship that caused the error
    // ────────────────────────────────────────────────
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    // Optional but recommended – allows $attendance->leaveType directly
    public function leaveType(): HasOneThrough
    {
        return $this->hasOneThrough(
            LeaveType::class,         // target model
            LeaveRequest::class,      // through model
            'id',                     // foreign key on leave_requests → leave_request.id
            'id',                     // foreign key on leave_types → leave_type.id
            'leave_request_id',       // local key on attendances
            'leave_type_id'           // local key on leave_requests
        );
    }

    // Your existing helper methods
    public function isCheckedIn(): bool
    {
        return $this->clock_in !== null;
    }

    public function isCheckedOut(): bool
    {
        return $this->clock_out !== null;
    }

    public function getDurationInMinutesAttribute(): ?int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }
        return $this->clock_out->diffInMinutes($this->clock_in);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($attendance) {
            if (static::where('employee_id', $attendance->employee_id)
                      ->whereDate('date', $attendance->date)
                      ->exists()) {
                throw new \Exception("Attendance for this date already exists.");
            }
        });
    }
}