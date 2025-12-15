<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'status',
        'last_maintenance',
        'notes',
    ];

    protected $casts = [
        'last_maintenance' => 'date',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'equipment_service');
    }
}