<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('file.payroll_receipt') }} #{{ $payroll->id }}</title>

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

        .total-line.grand {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #3498db;
            padding-top: 12px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 12px;
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
            <h1>CAPED MAZATLAN</h1>
            <div class="tagline">{{ __('file.med_center_pharmacy') ?? 'Medical Center & Pharmacy' }}</div>
            <div class="address">
                {!! nl2br(e($clinic_address)) !!}<br>
                {{ __('file.phone') ?? 'Phone' }}: {{ $clinic_phone }}<br>
                {{ __('file.email') ?? 'Email' }}: {{ $clinic_email }}
            </div>
        </div>

        <div class="invoice-title-box">
            <div class="invoice-title">{{ __('file.payroll_receipt') }}</div>
            <div class="meta-line">
                <strong>{{ __('file.receipt_no') }}:</strong> #{{ $payroll->id }}<br>
                <strong>{{ __('file.date') }}:</strong> {{ $payroll->date->format('d M Y') }}<br>
                <strong>{{ __('file.status') }}:</strong> <span style="text-transform: uppercase;">{{ __('file.' . strtolower($payroll->status)) }}</span>
            </div>
        </div>
    </div>

    <!-- Recipient & Payment Info -->
    <div class="two-columns">
        <div class="info-box">
            <h3>{{ __('file.recipient') }}</h3>
            <p><strong>{{ __('file.name') }}:</strong> 
                @if($payroll->payable)
                    {{ $payroll->payable->full_name ?: ($payroll->payable->first_name ?: 'Unknown') }}
                @else
                    {{ __('file.deleted_user') ?? 'Deleted User' }} (#{{ $payroll->payable_id }})
                @endif
            </p>
                <p><strong>{{ __('file.type') }}:</strong> {{ $payroll->payable_type === 'App\Models\Doctor' ? __('file.therapist') : __('file.employee') }}</p>
                
        </div>

        <div class="info-box">
            <h3>{{ __('file.payment_details') }}</h3>
            <p><strong>{{ __('file.method') }}:</strong> {{ __('file.' . strtolower($payroll->payment_method)) }}</p>
            <p><strong>{{ __('file.processed_by') }}:</strong> {{ $payroll->creator ? $payroll->creator->name : __('file.system') }}</p>
        </div>
    </div>

    @if($payroll->payable_type === 'App\Models\Doctor' && $payroll->appointments->count() > 0)
    <!-- Items -->
    <table class="items">
        <thead>
            <tr>
                <th style="width: 30px">#</th>
                <th>{{ __('file.appointment_number') }}</th>
                <th>{{ __('file.patient_name') }}</th>
                <th>{{ __('file.date') }}</th>
                <th class="right">{{ __('file.amount') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payroll->appointments as $i => $appointment)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $appointment->appointment_number }}</td>
                <td>{{ $appointment->patient ? $appointment->patient->full_name : '-' }}</td>
                <td>{{ $appointment->scheduled_start ? $appointment->scheduled_start->format('d M Y H:i') : '-' }}</td>
                <td class="right">{{ $currency_code }}{{ number_format($appointment->pivot->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    @if($payroll->notes)
    <div style="margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db;">
        <h3 style="color: #3498db; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; font-weight: 600;">
            {{ __('file.notes') }}
        </h3>
        <p style="font-size: 13px; margin: 0; color: #2c3e50;">
            {!! nl2br(e($payroll->notes)) !!}
        </p>
    </div>
    @endif

    <!-- Totals -->
    <div class="totals">
        <div class="total-line grand">
            

            <div>{{ __('file.total_amount') }}</div>
            <div>{{ $currency_code }}{{ number_format($payroll->amount, 2) }}</div>
        </div>


        <div class="total-line grand">

        @if($payroll->payable_type === 'App\Models\Doctor')
                    <div>{{ __('file.corresponds_to_therapist') }}</div>   
                    <div>{{ $currency_code }}{{ number_format($payroll->therapist_amount, 2) }}</div>
          @endif
        </div>
        <div class="total-line grand">

        @if($payroll->payable_type === 'App\Models\Doctor')
                    <div>{{ __('file.corresponds_to_caped') }}</div>
                    
            <div>{{ $currency_code }}{{ number_format($payroll->caped_amount, 2) }}</div>
                    @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="font-size:11px; color:#95a5a6;">
            {{ __('file.printed_on') }} {{ now()->translatedFormat('d M Y h:i A') }} | {{ __('file.powered_by') }} {{ $clinic_name }}
        </div>
    </div>

</div>

</body>
</html>
