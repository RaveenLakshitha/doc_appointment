<?php

namespace App\Notifications;

use App\Models\Payroll;
use App\Models\Setting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollSent extends Notification
{
    protected $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('file.payroll_receipt', [], 'en') . ' - #' . $this->payroll->id)
            ->line(__('file.dear', [], 'en') . ' ' . ($this->payroll->payable->full_name ?? ''))
            ->line(__('file.document_message_body', [], 'en') ?? 'Please find attached the requested document.');

        $clinic_name = Setting::getSetting('clinic_name', config('app.name', 'Clinic'));
        $clinic_address = Setting::getSetting('clinic_address', '123 Clinic Ave');
        $clinic_phone = Setting::getSetting('clinic_phone', '+1 234 567 8900');
        $clinic_email = Setting::getSetting('clinic_email', 'contact@clinic.com');
        $currency_code = Setting::getCurrencySymbol();

        $payroll = $this->payroll;
        $pdf = Pdf::loadView('hr.payrolls.print', compact(
            'payroll',
            'clinic_name',
            'clinic_address',
            'clinic_phone',
            'clinic_email',
            'currency_code'
        ))->setPaper('a4', 'portrait');

        $mail->attachData($pdf->output(), 'Payroll-Receipt-' . $this->payroll->id . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }
}
