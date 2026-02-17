<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 15mm 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 16px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
        }

        .clinic h1 {
            margin: 0;
            font-size: 26px;
            color: #1e40af;
        }

        .meta {
            text-align: right;
        }

        .meta h2 {
            margin: 0 0 6px;
            font-size: 28px;
            color: #2563eb;
        }

        table.meta td {
            padding: 3px 0;
            font-size: 12px;
        }

        .two-col {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
        }

        .info-box {
            flex: 1;
            background: #f8fafc;
            padding: 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .info-box h3 {
            margin: 0 0 10px;
            font-size: 12px;
            color: #2563eb;
            text-transform: uppercase;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        table.items th {
            background: #2563eb;
            color: white;
            padding: 10px 8px;
            font-size: 12px;
        }

        table.items th.center { text-align: center; }
        table.items th.right  { text-align: right; }

        table.items td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .totals {
            width: 340px;
            margin-left: auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13.5px;
        }

        .total-row.grand {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 8px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div class="clinic">
            <h1>{{ config('app.name', 'Your Clinic') }}</h1>
            <div>Medical Center & Pharmacy</div>
        </div>

        <div class="meta">
            <h2>INVOICE</h2>
            <table class="meta">
                <tr><td><strong>Invoice #:</strong></td><td>{{ $invoice->invoice_number }}</td></tr>
                <tr><td><strong>Date:</strong></td><td>{{ $invoice->invoice_date->format('d M Y') }}</td></tr>
                <tr><td><strong>Status:</strong></td><td>{{ ucfirst($invoice->status) }}</td></tr>
            </table>
        </div>
    </div>

    <div class="two-col">
        <div class="info-box">
            <h3>Bill To</h3>
            <strong>{{ $invoice->patient->getFullNameAttribute() ?? '—' }}</strong><br>
            {{ $invoice->patient->phone ?? '' }}<br>
            MRN: {{ $invoice->patient->medical_record_number ?? '—' }}
        </div>

        <div class="info-box">
            <h3>Invoice Details</h3>
            Due Date: {{ $invoice->due_date?->format('d M Y') ?? '—' }}<br>
            Created By: {{ auth()->user()->name ?? '—' }}
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width:50px">#</th>
                <th>Description</th>
                <th class="center">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <div>Subtotal</div>
            <div>{{ number_format($invoice->subtotal, 2) }}</div>
        </div>

        @if($invoice->tax_amount > 0)
        <div class="total-row">
            <div>Tax</div>
            <div>{{ number_format($invoice->tax_amount, 2) }}</div>
        </div>
        @endif

        @if($invoice->discount_amount > 0)
        <div class="total-row">
            <div>Discount</div>
            <div style="color:#dc2626">-{{ number_format($invoice->discount_amount, 2) }}</div>
        </div>
        @endif

        <div class="total-row grand">
            <div>Total</div>
            <div>{{ number_format($invoice->total, 2) }}</div>
        </div>

        @if($invoice->balance_due > 0)
        <div class="total-row" style="margin-top:12px; font-weight:bold; color:#c2410c;">
            <div>Balance Due</div>
            <div>{{ number_format($invoice->balance_due, 2) }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <div style="font-weight:600; color:#2563eb; font-size:15px; margin-bottom:8px;">
            Thank you for choosing us!
        </div>
        <div>Printed on {{ now()->format('d M Y h:i A') }}</div>
    </div>

</div>

<div class="no-print" style="position:fixed; bottom:24px; right:32px;">
    <button onclick="doPrintAndRedirect()" 
            style="padding:12px 28px; background:#2563eb; color:white; border:none; border-radius:6px; font-size:15px; cursor:pointer; box-shadow:0 4px 12px rgba(37,99,235,0.3);">
        Print & Return to POS
    </button>
</div>

<script type="text/javascript">
    function doPrintAndRedirect() {
        window.print();
        setTimeout(function() {
            window.location.href = "{{ route('invoices.pos') }}";
        }, 1800); // 1.8 seconds – enough time for print dialog
    }

    // Auto print after page loads (like your reference code)
    window.onload = function() {
        setTimeout(doPrintAndRedirect, 900); // slight delay for better UX
    };
</script>

</body>
</html>