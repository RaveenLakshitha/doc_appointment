<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorScheduleDay extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['doctor_schedule_id', 'day_of_week'];

    public function schedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }
}