<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_treatment')
                    ->withPivot('price')
                    ->withTimestamps();
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_treatment')
                    ->withPivot(['quantity', 'price_at_time', 'notes'])
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)
                     ->whereNull('deleted_at');
    }
}