<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes; // ← very useful for history / audit

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',           // present, absent, late, half_day, leave, holiday, ...
        'notes',
        'marked_by',        // ← who recorded it (admin / employee / supervisor)
        'marked_at',        // ← when it was recorded
        'ip_address',       // ← basic audit trail
    ];

    protected $casts = [
        'date'      => 'date:Y-m-d',
        'clock_in'  => 'datetime:H:i:s',   // better to store seconds
        'clock_out' => 'datetime:H:i:s',
        'marked_at' => 'datetime',
    ];

    protected $dates = ['deleted_at']; // if using softDeletes

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'marked_by');
    }

    // Very useful helper methods
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

    // Prevent duplicate entries on the same day
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