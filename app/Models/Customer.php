<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use SoftDeletes, Notifiable;

    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'gender',
        'date_of_birth',
        'status',
        'notes',
        'preferred_language',
        'active',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'active'        => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
