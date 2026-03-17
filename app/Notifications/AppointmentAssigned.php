<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentAssigned extends Notification
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
        $doctorName = $this->appointment->doctor?->getFullNameAttribute() ?? __('file.unknown_doctor');

        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $patientName,
            'doctor_name' => $doctorName,
            'scheduled' => $this->appointment->scheduled_start
                ? $this->appointment->scheduled_start->format('M d, Y h:i A')
                : __('file.not_scheduled_yet'),
            'message' => __('file.notification_appointment_assigned', ['patient' => $patientName, 'doctor' => $doctorName]),
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'user-plus',
            'color' => 'blue',
            'link' => route('appointments.show', $this->appointment->id),
        ];
    }
}
