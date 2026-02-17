<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveEntitlement extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',               // e.g. 2026
        'entitled_days',
        'used_days',
        'remaining_days',     // can be calculated or stored
        'accrual_rate',       // optional (days per month)
        'last_accrued_at',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Helper
    public function getRemainingAttribute(): float
    {
        return $this->entitled_days - $this->used_days;
    }
}