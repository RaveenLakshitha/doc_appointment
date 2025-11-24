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

        'primary_specialty','secondary_specialty','license_number',
        'license_expiry_date','qualifications','years_experience',
        'education','certifications','department','position','hourly_rate',
        'profile_photo','is_active',
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

    public function specialization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Specialization::class, 'primary_specialization_id');
    }
}