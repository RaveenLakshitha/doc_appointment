<?php

namespace App\Notifications;

use App\Models\BillingInvoice;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentSent extends Notification
{
    protected $invoice;
    protected $documentType;
    protected $filePath;
    protected $label;

    public function __construct(BillingInvoice $invoice, string $documentType, ?string $filePath = null, ?string $label = null)
    {
        $this->invoice = $invoice;
        $this->documentType = $documentType;
        $this->filePath = $filePath;
        $this->label = $label ?? __('file.document');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->label . ' - ' . $this->invoice->invoice_number)
            ->line(__('file.dear') . ' ' . ($this->invoice->patient?->full_name ?? $this->invoice->customer?->full_name ?? 'Customer'))
            ->line(__('file.document_message_body') ?? 'Please find attached the requested document.');

        if ($this->documentType === 'invoice') {
            $settings = \App\Models\Setting::first();
            $pdf = Pdf::loadView('invoices.print-receipt', [
                'invoice' => $this->invoice,
                'settings' => $settings,
                'currency_code' => $settings->currency ?? '$'
            ])->setPaper([0, 0, 226.77, 841.89], 'portrait');

            $mail->attachData($pdf->output(), 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        } elseif ($this->filePath && file_exists(public_path($this->filePath))) {
            $mail->attach(public_path($this->filePath));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title_key' => 'file.document_sent',
            'invoice_id' => $this->invoice->id,
            'document_type' => $this->documentType,
            'message_key' => 'file.document_sent_message',
            'message_params' => [
                'label' => $this->label
            ],
            'created_at_human' => now()->diffForHumans(),
            'icon' => 'mail',
            'color' => 'blue',
            'link' => route('invoices.show', $this->invoice->id),
        ];
    }
}
