<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'event_type',
        'role_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get users who should receive notifications for a specific event type.
     */
    public static function getRecipients(string $eventType)
    {
        $roles = self::where('event_type', $eventType)
            ->where('is_enabled', true)
            ->pluck('role_name')
            ->toArray();

        if (empty($roles)) {
            return collect();
        }

        return User::role($roles)->get();
    }
}
