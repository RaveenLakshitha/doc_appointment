<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class NewAppointmentCreated extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Which channels should receive this notification?
     * Only 'database' — no mail, no broadcast for now.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * The data that will be stored in the notifications table (JSON column 'data')
     */
    public function toArray(object $notifiable): array
    {
        $patientName = $this->appointment->patient?->getFullNameAttribute() ?? 'Unknown Patient';
        $typeLabel = match ($this->appointment->appointment_type) {
            Appointment::TYPE_SPECIFIC => __('file.specific_doctor'),
            Appointment::TYPE_ANY => __('file.any_doctor'),
            default => __('file.unknown_type'),
        };

        return [
            'title' => __('file.new_appointment') ?? 'New Appointment',
            'appointment_id' => $this->appointment->id,
            'patient_name' => $patientName,
            'reason' => $this->appointment->reason_for_visit,
            'type' => $typeLabel,
            'scheduled' => $this->appointment->scheduled_start
                ? $this->appointment->scheduled_start->format('M d, Y h:i A')
                : __('file.not_scheduled_yet'),
            'message' => __('file.notification_new_appointment', ['patient' => $patientName]),
            'created_at_human' => $this->appointment->created_at->diffForHumans(),
            // Optional: for UI styling / icons
            'icon' => 'calendar-plus',
            'color' => 'blue',
            'link' => route('appointments.show', $this->appointment->id),
        ];
    }
}