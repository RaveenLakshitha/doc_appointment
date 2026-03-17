<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class NotificationSettingController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();
        $settings = NotificationSetting::all()->groupBy('event_type');

        $events = [
            'appointment_created' => __('file.event_appointment_created'),
            'appointment_approved' => __('file.event_appointment_approved'),
            'appointment_completed' => __('file.event_appointment_completed'),
            'appointment_paid' => __('file.event_appointment_paid'),
        ];

        return view('admin.notification-settings.index', compact('roles', 'settings', 'events'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
            'settings.*' => 'nullable|array',
            'settings.*.*' => 'boolean',
        ]);

        $submittedSettings = $request->input('settings', []);

        $events = [
            'appointment_created',
            'appointment_approved',
            'appointment_completed',
            'appointment_paid',
        ];

        $roles = Role::where('guard_name', 'web')
            ->get();

        foreach ($events as $event) {
            foreach ($roles as $role) {
                $isEnabled = isset($submittedSettings[$event][$role->name]);

                NotificationSetting::updateOrCreate(
                    ['event_type' => $event, 'role_name' => $role->name],
                    ['is_enabled' => $isEnabled]
                );
            }
        }

        return redirect()->back()->with('success', 'Notification settings updated successfully.');
    }
}
