<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class AppointmentPaid extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $patientName = $this->payment->invoice->patient?->getFullNameAttribute() ?? __('file.unknown_patient');
        $amount = number_format($this->payment->amount, 2);

        $currency = \App\Models\Setting::first()->currency ?? '$';
        return [
            'payment_id' => $this->payment->id,
            'invoice_id' => $this->payment->invoice_id,
            'patient_name' => $patientName,
            'amount' => $amount,
            'message' => __('file.notification_appointment_paid', ['currency' => $currency, 'amount' => $amount, 'patient' => $patientName]),
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'credit-card',
            'color' => 'green',
            'link' => route('payments.index'),
        ];
    }
}
