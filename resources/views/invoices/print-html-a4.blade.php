<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('file.invoice_label') }} {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: 80mm auto;
            /* width = 80mm, height = auto (continuous roll) */
            margin: 4mm 4mm 6mm 4mm;
            /* small margins — adjust if printer crops edges */
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            /* fixed-width font → best for thermal */
            font-size: 12px;
            /* 10–13px is sweet spot for 80mm */
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: white;
        }

        .container {
            width: 72mm;
            /* safe printable area — most 80mm printers */
            margin: 0 auto;
            padding: 0 2mm;
        }

        h1,
        h2,
        h3 {
            margin: 4px 0;
            padding: 0;
            font-weight: bold;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .clinic h1 {
            font-size: 16px;
            margin: 0;
        }

        .meta {
            text-align: center;
            margin: 6px 0;
        }

        .meta strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        th,
        td {
            padding: 2px 0;
            text-align: left;
            font-size: 11px;
        }

        th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .item-row td {
            padding: 1px 0;
        }

        .totals {
            margin-top: 8px;
            font-size: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding: 2px 0;
        }

        .grand {
            font-size: 13px;
            border-top: 1px double #000;
            margin-top: 4px;
            padding-top: 4px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px dashed #000;
        }

        .no-print {
            display: none !important;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <div class="clinic">
                <h1>{{ $clinic_name }}</h1>
                <div>{{ __('file.med_center_pharmacy') }}</div>
                <div>{{ $clinic_address }}</div>
                <div>{{ __('file.phone') }}: {{ $clinic_phone }}</div>
                <div>{{ __('file.email') }}: {{ $clinic_email }}</div>
            </div>
        </div>

        <div class="meta">
            <h2>{{ __('file.invoice_label') }}</h2>
            <div><strong>{{ __('file.invoice_no') }}:</strong> {{ $invoice->invoice_number }}</div>
            <div><strong>{{ __('file.date') }}:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
            <div><strong>{{ __('file.status') }}:</strong> {{ __('file.status_' . strtolower($invoice->status)) }}</div>
        </div>

        <div style="margin: 8px 0;">
            <strong>{{ __('file.bill_to') }}:</strong><br>
            {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}<br>
            {{ $invoice->patient->phone ?? '' }}<br>
            {{ __('file.medical_record_number') }}: {{ $invoice->patient->medical_record_number ?? '—' }}
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center">#</th>
                    <th>{{ __('file.item') }}</th>
                    <th class="center">{{ __('file.qty') }}</th>
                    <th class="right">{{ __('file.price') }}</th>
                    <th class="right">{{ __('file.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr class="item-row">
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">{{ $currency_code }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="right">{{ $currency_code }}{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>{{ __('file.subtotal') }}</span>
                <span>{{ $currency_code }}{{ number_format($invoice->subtotal, 2) }}</span>
            </div>

            @if($invoice->tax_amount > 0)
                <div class="total-row">
                    <span>{{ __('file.tax') }}</span>
                    <span>{{ $currency_code }}{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
            @endif

            @if($invoice->discount_amount > 0)
                <div class="total-row">
                    <span>{{ __('file.discount') }}</span>
                    <span>-{{ $currency_code }}{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
            @endif

            <div class="total-row grand">
                <span>{{ __('file.total') }}</span>
                <span>{{ $currency_code }}{{ number_format($invoice->total, 2) }}</span>
            </div>

            @if($invoice->balance_due > 0)
                <div class="total-row" style="margin-top:6px;">
                    <span>{{ __('file.balance_due_label') }}</span>
                    <span>{{ $currency_code }}{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="footer">
            {{ __('file.thank_you_choosing') }}<br>
            {{ __('file.printed_on') }} {{ now()->format('d/m/Y h:i A') }}
        </div>

    </div>

    <!-- Remove or comment out the button if auto-print is enough -->
    <!--
    <div class="no-print" style="position:fixed; bottom:10px; right:10px;">
        <button onclick="window.print()">Print</button>
    </div>
    -->

    <script>
        // Auto print (most POS thermal printers work best with small delay)
        window.onload = function () {
            setTimeout(function () {
                window.print();

                // Optional redirect after printing
                setTimeout(function () {
                    @if($redirect === 'pos')
                        window.location.href = "{{ route('invoices.pos') }}";
                    @else
                        window.location.href = "{{ route('invoices.index') }}";
                    @endif
                }, 1200);
            }, 600);
        };
    </script>

</body>

</html>