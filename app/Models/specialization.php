<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this line

class Specialization extends Model
{
    protected $fillable = [
        'name', 'description', 'department_id'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'primary_specialization_id');
    }
}