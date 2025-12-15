<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'department_id',
        'type',
        'duration_minutes',
        'price',
        'is_active',
        'description',
        'patient_preparation',
        'requires_insurance',
        'requires_referral',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'duration_minutes'    => 'integer',
        'is_active'           => 'boolean',
        'requires_insurance'  => 'boolean',
        'requires_referral'   => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_service');
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'equipment_service');
    }

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(ServiceAvailabilitySlot::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}