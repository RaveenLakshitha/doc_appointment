<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentCompleted extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $patientName = $this->appointment->patient?->getFullNameAttribute() ?? __('file.unknown_patient');

        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $patientName,
            'message' => __('file.notification_appointment_completed', ['patient' => $patientName]),
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'flag-checkered',
            'color' => 'indigo',
            'link' => route('appointments.show', $this->appointment->id),
        ];
    }
}
