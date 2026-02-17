<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'whatsapp_conversations';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'phone',
        'step',
        'data',
        'last_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data'        => 'array',      // Automatically decode/encode JSON ↔ array
        'last_active' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Get the data attribute as array (convenience accessor)
     */
    public function getDataAttribute($value)
    {
        return $value ?? [];
    }

    /**
     * Helper: Update a specific key in the data JSON
     */
    public function updateData(string $key, $value): bool
    {
        $data = $this->data;
        $data[$key] = $value;
        return $this->update(['data' => $data]);
    }

    /**
     * Helper: Get a value from data with default
     */
    public function getDataValue(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Scope: Find active conversation (last active < 1 hour ago, for example)
     */
    public function scopeActive($query, int $minutes = 60)
    {
        return $query->where('last_active', '>=', now()->subMinutes($minutes));
    }

    /**
     * Optional: Relationship if you later link to Patient
     */
    // public function patient()
    // {
    //     return $this->belongsTo(Patient::class, 'phone', 'phone');
    // }
}