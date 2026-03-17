<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentRejected extends Notification
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
            'reason' => $this->appointment->rejection_reason,
            'message' => $this->appointment->rejection_reason 
                ? __('file.notification_appointment_rejected_with_reason', ['patient' => $patientName, 'reason' => $this->appointment->rejection_reason])
                : __('file.notification_appointment_rejected', ['patient' => $patientName]),
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'x-circle',
            'color' => 'red',
            'link' => route('appointments.show', $this->appointment->id),
        ];
    }
}
