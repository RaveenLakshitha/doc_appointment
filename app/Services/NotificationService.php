<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification to roles that have it enabled in settings.
     *
     * @param string $eventType
     * @param mixed $notification
     * @param array $extraUsers Additional users to notify regardless of role settings
     */
    public static function send(string $eventType, $notification, array $extraUsers = [])
    {
        $enabledRoles = NotificationSetting::where('event_type', $eventType)
            ->where('is_enabled', true)
            ->pluck('role_name')
            ->toArray();

        if (empty($enabledRoles)) {
            if (!empty($extraUsers)) {
                Notification::send($extraUsers, $notification);
            }
            return;
        }

        $usersToNotify = User::role($enabledRoles)->get();

        // Merge with extra users and remove duplicates
        if (!empty($extraUsers)) {
            $usersToNotify = $usersToNotify->merge($extraUsers)->unique('id');
        }

        if ($usersToNotify->isNotEmpty()) {
            Notification::send($usersToNotify, $notification);
        }
    }
}
