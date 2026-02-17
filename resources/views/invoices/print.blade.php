<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            color: #2c3e50;
            line-height: 1.5;
            background: #fff;
            padding: 30px 20px;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        .header {
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .clinic-info h1 {
            font-size: 26px;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .clinic-info .tagline {
            font-size: 13px;
            color: #7f8c8d;
        }

        .clinic-info .address {
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 12px;
            line-height: 1.6;
        }

        .invoice-title-box {
            text-align: right;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 6px;
        }

        .meta-line {
            font-size: 12px;
            color: #7f8c8d;
        }

        .meta-line strong {
            color: #2c3e50;
            min-width: 90px;
            display: inline-block;
        }

        .two-columns {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            background: #f8f9fa;
            border-radius: 6px;
            padding: 16px;
        }

        .info-box h3 {
            color: #3498db;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-box p {
            font-size: 12px;
            margin: 6px 0;
        }

        .info-box strong {
            display: inline-block;
            width: 100px;
            color: #7f8c8d;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.items th {
            background: #3498db;
            color: white;
            padding: 12px 10px;
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        table.items th.center { text-align: center; }
        table.items th.right  { text-align: right; }

        table.items td {
            padding: 12px 10px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 12.5px;
            vertical-align: middle;
        }

        .item-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 8px;
        }

        .badge.service   { background:#e3f2fd; color:#1e88e5; }
        .badge.medicine  { background:#f3e5f5; color:#8e24aa; }
        .badge.treatment { background:#e8f5e9; color:#2e7d32; }

        .totals {
            width: 360px;
            margin-left: auto;
            margin-bottom: 40px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: 13px;
        }

        .total-line.border-top {
            border-top: 1px solid #ecf0f1;
            padding-top: 12px;
            margin-top: 8px;
        }

        .total-line.grand {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #3498db;
            padding-top: 12px;
            margin-top: 10px;
        }

        .total-line.discount .value {
            color: #e74c3c;
        }

        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
        }

        .thank-you {
            color: #3498db;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 10px;
        }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .invoice-container { max-width: none; }
        }
    </style>
</head>
<body>

<div class="invoice-container">

    <!-- Header -->
    <div class="header">
        <div class="clinic-info">
            <h1>{{ config('app.name', 'Medical Center') }}</h1>
            <div class="tagline">Quality Healthcare & Pharmacy Services</div>
            <div class="address">
                123 Healthcare Avenue<br>
                Colombo 05, Sri Lanka<br>
                Phone: +94 11 234 5678<br>
                Email: info@yourclinic.lk
            </div>
        </div>

        <div class="invoice-title-box">
            <div class="invoice-title">INVOICE</div>
            <div class="meta-line">
                <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}<br>
                <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
            </div>
        </div>
    </div>

    <!-- Patient & Payment Info -->
    <div class="two-columns">
        <div class="info-box">
            <h3>Bill To</h3>
            <p><strong>Name:</strong> {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}</p>
            <p><strong>MRN:</strong> {{ $invoice->patient->medical_record_number ?? '—' }}</p>
            <p><strong>Contact:</strong> {{ $invoice->patient->phone ?? '—' }}</p>
        </div>

        <div class="info-box">
            <h3>Invoice Details</h3>
            <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
            @if($invoice->payments->isNotEmpty())
            <p><strong>Paid Via:</strong> {{ ucfirst($invoice->payments->last()->method ?? '—') }}</p>
            @endif
        </div>
    </div>

    <!-- Items -->
    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width:45px">#</th>
                <th>Description</th>
                <th class="center">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>
                    <span class="item-name">{{ $item->description }}</span>
                    @if(str_contains($item->itemable_type ?? '', 'Service'))
                        <span class="badge service">Service</span>
                    @elseif(str_contains($item->itemable_type ?? '', 'Treatment'))
                        <span class="badge treatment">Treatment</span>
                    @else
                        <span class="badge medicine">Medicine</span>
                    @endif
                </td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">${{ number_format($item->unit_price, 2) }}</td>
                <td class="right">${{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="total-line">
            <div>Subtotal</div>
            <div>${{ number_format($invoice->subtotal, 2) }}</div>
        </div>

        @if($invoice->tax_amount > 0)
        <div class="total-line">
            <div>Tax</div>
            <div>${{ number_format($invoice->tax_amount, 2) }}</div>
        </div>
        @endif

        @if($invoice->discount_amount > 0)
        <div class="total-line discount">
            <div>Discount</div>
            <div>-${{ number_format($invoice->discount_amount, 2) }}</div>
        </div>
        @endif

        <div class="total-line grand border-top">
            <div>Total Amount</div>
            <div>${{ number_format($invoice->total, 2) }}</div>
        </div>

        @if($invoice->balance_due > 0)
        <div class="total-line" style="margin-top:10px; font-weight:bold; color:#e67e22;">
            <div>Balance Due</div>
            <div>${{ number_format($invoice->balance_due, 2) }}</div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="thank-you">Thank you for choosing us!</div>
        <div>Wishing you a speedy recovery and good health.</div>
        <div style="margin-top:16px; font-size:11px; color:#95a5a6;">
            This is a computer-generated document – no signature required.<br>
            Printed on {{ now()->format('d M Y h:i A') }} | Powered by {{ config('app.name') }}
        </div>
    </div>

</div>

<div class="no-print" style="position:fixed; bottom:30px; right:30px; display:flex; gap:15px;">
    <button onclick="window.print(); setTimeout(()=>window.location.href='{{ route('invoices.pos') }}', 1200);" 
            style="padding:14px 28px; background:#27ae60; color:white; border:none; border-radius:8px; font-size:15px; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        Print & Return to POS
    </button>
    
    <button onclick="window.location.href='{{ route('invoices.pos') }}'" 
            style="padding:14px 28px; background:#7f8c8d; color:white; border:none; border-radius:8px; font-size:15px; cursor:pointer;">
        Back to POS
    </button>
</div>

<script>
    // Optional: auto-trigger print dialog after 800ms (uncomment if desired)
    // setTimeout(() => window.print(), 800);
</script>

</body>
</html>