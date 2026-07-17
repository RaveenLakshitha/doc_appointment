<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <title>{{ __('file.bill') }} {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 5mm;
            width: 70mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }
        .header {
            margin-bottom: 10px;
        }
        .clinic-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .bill-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            border-bottom: 1px solid #000;
            padding: 4px 0;
            text-align: left;
        }
        .items-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 2px 0;
        }
        .payment-history-title {
            text-align: center;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }
        .footer {
            margin-top: 15px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="clinic-name">{{ $settings->clinic_name ?? 'Medical Center' }}</div>
        <div>{{ __('file.med_center_pharmacy') }}</div>
        <div>{!! nl2br(e($settings->address)) !!}</div>
        <div>{{ __('file.phone_number') }}: {{ $settings->phone ?? '' }}</div>
        <div>{{ __('file.email') }}: {{ $settings->email ?? '' }}</div>
    </div>

    <div class="dashed-line"></div>

    <div class="bill-title text-center">{{ __('file.bill') }}</div>

    <table class="info-table">
        <tr>
            <td class="bold" style="width: 35%;">{{ __('file.invoice_no') }}:</td>
            <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td class="bold">{{ __('file.date') }}:</td>
            <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bold">{{ __('file.status') }}:</td>
            <td>{{ __('file.status_' . strtolower($invoice->status)) }}</td>
        </tr>
    </table>

    <div class="bold">{{ __('file.bill_to') }}:</div>
    @php $client = $invoice->patient ?? $invoice->customer; @endphp
    <div>{{ $client->full_name ?? ($client->first_name . ' ' . $client->last_name) ?? '—' }}</div>
    
    <table class="items-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">{{ __('file.item') }}</th>
                <th class="text-center">{{ __('file.qty') }}</th>
                <th class="text-right">{{ __('file.price') }}</th>
                <th class="text-right">{{ __('file.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $currency_code }}{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $currency_code }}{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="bold" style="width: 60%;">{{ __('file.subtotal') }}</td>
            <td class="text-right bold">{{ $currency_code }}{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->tax_amount > 0)
        <tr>
            <td class="bold">{{ __('file.tax') }}</td>
            <td class="text-right bold">{{ $currency_code }}{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @endif
        @if($invoice->discount_amount > 0)
        <tr>
            <td class="bold">{{ __('file.discount') }}</td>
            <td class="text-right bold">-{{ $currency_code }}{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="2" class="dashed-line"></td>
        </tr>
        <tr>
            <td class="bold" style="font-size: 14px;">{{ __('file.total') }}</td>
            <td class="text-right bold" style="font-size: 14px;">{{ $currency_code }}{{ number_format($invoice->total, 2) }}</td>
        </tr>
        @if($invoice->balance_due > 0)
        <tr>
            <td class="bold">{{ __('file.balance_due_label') }}</td>
            <td class="text-right bold">{{ $currency_code }}{{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
        @endif
    </table>

    @if($invoice->notes)
        <div style="margin-top: 10px; font-size: 11px;">
            <span class="bold">{{ __('file.sale_note') }}:</span><br>
            {!! nl2br(e($invoice->notes)) !!}
        </div>
    @endif

    <div class="dashed-line"></div>

    <div class="payment-history-title">{{ __('file.payment_history') }}</div>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('file.date') }}</th>
                <th class="text-center">{{ __('file.method') }}</th>
                <th class="text-right">{{ __('file.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td class="text-center">{{ __('file.' . strtolower($payment->method)) }}</td>
                    <td class="text-right">{{ $currency_code }}{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <div class="footer text-center">
        <div class="thank-you">{!! nl2br(e(__('file.thank_you_choosing'))) !!}</div>
    </div>
</body>
</html>
