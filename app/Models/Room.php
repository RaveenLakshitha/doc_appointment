<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'room_number',
        'name',
        'description',
        'is_active',
        'room_type',
        'floor',
        'capacity',
        'price_per_day',
        'facilities',
        'phone',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'capacity'      => 'integer',
        'price_per_day' => 'decimal:2',
        'facilities'    => 'array',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasFacility(string $facility): bool
    {
        return in_array($facility, $this->facilities ?? []);
    }

    public function getFacilitiesListAttribute(): string
    {
        if (empty($this->facilities)) {
            return '-';
        }

        $labels = [
            'television'            => 'Television',
            'air_conditioning'      => 'Air Conditioning',
            'wifi'                  => 'WiFi',
            'telephone'             => 'Telephone',
            'attached_bathroom'     => 'Attached Bathroom',
            'wheelchair_accessible' => 'Wheelchair Accessible',
            'oxygen_supply'         => 'Oxygen Supply',
            'nurse_call_button'     => 'Nurse Call Button',
        ];

        return collect($this->facilities)
            ->map(fn($f) => $labels[$f] ?? ucwords(str_replace('_', ' ', $f)))
            ->join(', ');
    }

    public function getStatusAttribute(): bool
    {
        return $this->is_active;
    }
}