<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name', 'head_doctor_id', 'location', 'status', 'email', 'phone', 'description'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function headDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'head_doctor_id');
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'department_id');
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class);
    }
}