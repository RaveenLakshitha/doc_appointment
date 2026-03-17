<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgeGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'min_age', 'max_age', 'description', 'is_active',
    ];

    protected $casts = [
        'min_age'   => 'integer',
        'max_age'   => 'integer',
        'is_active' => 'boolean',
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'age_group_doctor')
                    ->withTimestamps();
    }
}