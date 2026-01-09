<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'doctor_id', 'room_id', 'start_time', 'end_time',
        'valid_from', 'valid_until', 'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(DoctorScheduleDay::class);
    }

    public function getDaysOfWeekAttribute()
    {
        return $this->days->pluck('day_of_week')->toArray();
    }
}