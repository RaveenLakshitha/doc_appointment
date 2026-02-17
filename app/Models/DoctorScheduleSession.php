<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorScheduleSession extends Model
{
    protected $fillable = [
        'doctor_schedule_id',
        'name',
        'key',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'max_patients',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
        'is_active'  => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class);
    }
}