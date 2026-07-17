<?php

namespace App\Notifications;

use App\Models\BillingInvoice;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceSent extends Notification
{

    protected $invoice;

    public function __construct(BillingInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = \App\Models\Setting::first();
        $pdf = Pdf::loadView('invoices.print-receipt', [
            'invoice' => $this->invoice,
            'settings' => $settings,
            'currency_code' => $settings->currency ?? '$'
        ])->setPaper([0, 0, 226.77, 841.89], 'portrait');

        return (new MailMessage)
            ->subject(__('file.invoice') . ' #' . $this->invoice->invoice_number)
            ->line(__('file.dear') . ' ' . ($this->invoice->patient?->full_name ?? $this->invoice->customer?->full_name ?? 'Customer'))
            ->line(__('file.invoice_message_body') ?? 'Please find attached your invoice.')
            ->attachData($pdf->output(), 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title_key' => 'file.invoice_sent',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'message_key' => 'file.invoice_sent_message',
            'message_params' => [
                'number' => $this->invoice->invoice_number
            ],
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'mail',
            'color' => 'blue',
            'link' => route('invoices.show', $this->invoice->id),
        ];
    }
}
