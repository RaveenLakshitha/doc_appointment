<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\AppointmentChangeRequest;

class NewAppointmentChangeRequestNotification extends Notification
{
    use Queueable;

    protected $changeRequest;

    public function __construct(AppointmentChangeRequest $changeRequest)
    {
        $this->changeRequest = $changeRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $patientName = $this->changeRequest->patient?->getFullNameAttribute() ?? 'Unknown Patient';
        $typeLabel = ucfirst($this->changeRequest->request_type);

        return [
            'title_key' => 'file.new_change_request',
            'title' => 'New Change Request',
            'change_request_id' => $this->changeRequest->id,
            'patient_name' => $patientName,
            'reason' => $this->changeRequest->reason,
            'type' => $typeLabel,
            'message_key' => 'file.notification_new_change_request',
            'message' => 'New appointment ' . $this->changeRequest->request_type . ' request from ' . $patientName,
            'created_at_human' => $this->changeRequest->created_at->diffForHumans(),
            'icon' => 'calendar-refresh',
            'color' => 'yellow',
            'link' => route('appointment-change-requests.admin.index'),
        ];
    }
}
