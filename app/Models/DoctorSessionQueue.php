<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSessionQueue extends Model
{
    protected $fillable = [
        'doctor_id',
        'queue_date',
        'session_key',
        'last_number',
    ];

    protected $casts = [
        'queue_date' => 'date',
    ];
}