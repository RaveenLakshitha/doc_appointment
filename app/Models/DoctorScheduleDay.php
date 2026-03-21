<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorScheduleDay extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['doctor_schedule_id', 'day_of_week', 'room_id', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function schedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}