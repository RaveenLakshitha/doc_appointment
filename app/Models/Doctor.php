<?php
// app/Models/Doctor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name','middle_name','last_name','date_of_birth','gender',
        'address','city','state','zip_code','email','phone',
        'emergency_contact_name','emergency_contact_phone',
        'license_number','license_expiry_date',
        'qualifications','years_experience','education','certifications',
        'department_id','position_id','hourly_rate','profile_photo','is_active',
        'primary_specialization_id'
    ];

    protected $casts = [
        'date_of_birth'      => 'date',
        'license_expiry_date'=> 'date',
        'hourly_rate'        => 'decimal:2',
        'years_experience'   => 'integer',
        'is_active'          => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function primarySpecialization()
    {
        return $this->belongsTo(\App\Models\Specialization::class, 'primary_specialization_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'doctor_service');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function rooms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'doctor_schedules')
                    ->withPivot('days_of_week', 'start_time', 'end_time', 'valid_from', 'valid_until', 'is_active')
                    ->withTimestamps();
    }

    public function scopeOrderByFullName($query, $direction = 'asc')
    {
        return $query->orderByRaw("
            CONCAT(
                COALESCE(last_name, ''),
                COALESCE(first_name, ''),
                COALESCE(middle_name, '')
            ) {$direction}"
        );
    }

    // Add relationship
    public function positionOption()
    {
        return $this->belongsTo(\App\Models\OptionList::class, 'position_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}