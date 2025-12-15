<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAvailabilitySlot extends Model
{
    protected $table = 'service_availability_slots';

    protected $fillable = [
        'service_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}