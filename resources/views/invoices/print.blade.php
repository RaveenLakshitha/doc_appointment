<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            color: #2c3e50;
            line-height: 1.6;
            padding: 40px;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
        }
        
        /* Header Section */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid #3498db;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .clinic-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .clinic-tagline {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .clinic-address {
            font-size: 11px;
            color: #7f8c8d;
            line-height: 1.8;
        }
        
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .invoice-meta strong {
            color: #2c3e50;
            display: inline-block;
            width: 80px;
        }
        
        /* Patient & Details Section */
        .details-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .detail-box {
            display: table-cell;
            width: 50%;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .detail-box:first-child {
            margin-right: 20px;
        }
        
        .detail-box h3 {
            font-size: 11px;
            color: #3498db;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .detail-box p {
            font-size: 11px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .detail-box strong {
            display: inline-block;
            width: 85px;
            color: #7f8c8d;
            font-weight: normal;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .items-table thead {
            background: #3498db;
            color: white;
        }
        
        .items-table th {
            padding: 14px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 12.5px;
            vertical-align: middle;
        }
        
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .item-description {
            color: #2c3e50;
            font-weight: 500;
            font-size: 13px;
        }
        
        .item-type {
            display: inline-block;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 3px;
            margin-left: 8px;
            background: #ecf0f1;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .item-type.service {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        .item-type.medicine {
            background: #f3e5f5;
            color: #9c27b0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Totals Section */
        .totals-section {
            width: 380px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            font-size: 13px;
        }
        
        .total-row .label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            color: #7f8c8d;
        }
        
        .total-row .value {
            display: table-cell;
            text-align: right;
            width: 120px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .total-row.subtotal {
            border-top: 1px solid #ecf0f1;
            padding-top: 12px;
        }
        
        .total-row.grand-total {
            border-top: 2px solid #3498db;
            padding-top: 12px;
            margin-top: 8px;
            font-size: 16px;
        }
        
        .total-row.grand-total .label,
        .total-row.grand-total .value {
            color: #2c3e50;
            font-weight: bold;
        }
        
        .total-row.discount .value {
            color: #e74c3c;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
        }
        
        .thank-you {
            font-size: 15px;
            color: #3498db;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .footer-note {
            font-size: 10px;
            color: #95a5a6;
            line-height: 1.6;
        }
        
        .print-info {
            font-size: 9px;
            color: #bdc3c7;
            margin-top: 12px;
        }
        
        /* Print Styles */
        @media print {
            body { padding: 0; }
            .invoice-container { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="header-left">
                <div class="clinic-name">{{ config('app.name') }}</div>
                <div class="clinic-tagline">Medical Center & Pharmacy</div>
                <div class="clinic-address">
                    123 Healthcare Boulevard<br>
                    Medical District, City 12345<br>
                    Phone: (555) 123-4567 | Email: info@medicalcenter.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}<br>
                    <strong>Status:</strong> PAID
                </div>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="details-section">
            <div class="detail-box">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}</p>
                <p><strong>MRN:</strong> {{ $invoice->patient->medical_record_number }}</p>
                <p><strong>Patient ID:</strong> {{ $invoice->patient->id }}</p>
            </div>
            <div class="detail-box">
                <h3>Payment Information</h3>
                <p><strong>Payment Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                <p><strong>Payment Method:</strong> Cash/Card</p>
                <p><strong>Receipt Type:</strong> Original</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Description</th>
                    <th class="text-center" style="width: 80px;">Quantity</th>
                    <th class="text-right" style="width: 100px;">Unit Price</th>
                    <th class="text-right" style="width: 120px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="item-description">{{ $item->description }}</span>
                        @if($item->itemable_type === 'App\Models\Service')
                            <span class="item-type service">Service</span>
                        @else
                            <span class="item-type medicine">Medicine</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row subtotal">
                <div class="label">Subtotal:</div>
                <div class="value">${{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            
            @if($invoice->tax_amount > 0)
            <div class="total-row">
                <div class="label">Tax:</div>
                <div class="value">${{ number_format($invoice->tax_amount, 2) }}</div>
            </div>
            @endif
            
            @if($invoice->discount_amount > 0)
            <div class="total-row discount">
                <div class="label">Discount:</div>
                <div class="value">-${{ number_format($invoice->discount_amount, 2) }}</div>
            </div>
            @endif
            
            <div class="total-row grand-total">
                <div class="label">Total Paid:</div>
                <div class="value">${{ number_format($invoice->total, 2) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="thank-you">Thank you for choosing our medical center!</div>
            <div class="footer-note">
                This is a computer-generated invoice and is valid without signature.<br>
                For queries, please contact our billing department.
            </div>
            <div class="print-info">
                Printed on {{ now()->format('d M Y H:i') }} | Powered by {{ config('app.name') }}
            </div>
        </div>
    </div>
</body>
</html>