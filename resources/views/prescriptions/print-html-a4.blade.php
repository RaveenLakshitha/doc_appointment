<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('file.prescription_label') }} {{ $prescription->id }}</title>

    <style>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 15mm 10mm;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
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

        .diagnosis-box {
            margin-bottom: 24px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
        }

        .diagnosis-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
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
            text-align: left;
        }

        table.items th.center {
            text-align: center;
        }

        table.items th.right {
            text-align: right;
        }

        table.items td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        
        .notes-box {
            margin-top: 24px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        
        .notes-box h3 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
            padding-right: 20px;
        }

        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            text-align: center;
            color: #475569;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
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
                <div class="text-sm text-gray-500 italic">
                    {{ $clinic_address }}<br>
                    {{ __('file.phone') }}: {{ $clinic_phone }}<br>
                    {{ __('file.email') }}: {{ $clinic_email }}
                </div>
            </div>

            <div class="meta">
                <h2>{{ __('file.prescription_label') }}</h2>
                <table class="meta">
                    <tr>
                        <td><strong>{{ __('file.date') }}:</strong></td>
                        <td>{{ $prescription->prescription_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('file.type') }}:</strong></td>
                        <td><span class="font-medium text-gray-900">{{ __('file.' . strtolower($prescription->type)) }}</span></td>
                    </tr>
                    @if($prescription->appointment)
                    <tr>
                        <td><strong>{{ __('file.appointment_number') }}:</strong></td>
                        <td>#{{ str_pad($prescription->appointment->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="two-col">
            <div class="info-box">
                <h3>{{ __('file.patient_details') }}</h3>
                <strong>{{ $prescription->patient->getFullNameAttribute() ?? '—' }}</strong><br>
                {{ __('file.Age') }}: @if($prescription->patient->date_of_birth) <span style="background-color: #e0e7ff; color: #3730a3; padding: 2px 6px; border-radius: 4px; font-weight: bold; -webkit-print-color-adjust: exact; print-color-adjust: exact;">{{ $prescription->patient->date_of_birth->age }} {{ __('file.yrs') }}</span> @else — @endif | {{ __('file.gender') }}: {{ $prescription->patient->gender ? __('file.' . strtolower($prescription->patient->gender)) : '—' }}<br>
                {{ $prescription->patient->phone ?? '' }}<br>
                {{ __('file.medical_record_number') }}: {{ $prescription->patient->medical_record_number ?? '—' }}
            </div>

            <div class="info-box">
                <h3>{{ __('file.doctor_details') }}</h3>
                <strong>{{ $prescription->doctor->getFullNameAttribute() ?? '—' }}</strong><br>
                {{ __('file.medical_professional') }}: <span class="font-medium text-gray-900">{{ $prescription->doctor->full_name ?? '—' }}</span>
            </div>
        </div>

        @if($prescription->diagnosis)
        <div class="diagnosis-box">
            <h3>{{ __('file.diagnosis') }}</h3>
            <p style="margin:0;">{{ $prescription->diagnosis }}</p>
        </div>
        @endif

        <h3 style="color:#1e40af; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-bottom:16px;">{{ __('file.medications_rx') }}</h3>

        <table class="items">
            <thead>
                <tr>
                    <th class="center" style="width:40px">#</th>
                    <th>{{ __('file.medicine_name_label') }}</th>
                    <th>{{ __('file.dosage_route') }}</th>
                    <th>{{ __('file.frequency') }}</th>
                    <th class="center">{{ __('file.duration') }}</th>
                    <th>{{ __('file.instructions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->medications as $index => $med)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td><strong>{{ $med->name }}</strong></td>
                        <td>{{ $med->dosage ?? '—' }} <br> <span style="font-size:11px; color:#64748b;">{{ $med->route ?? 'Oral' }}</span></td>
                        <td>{{ $med->frequency ?? '—' }} <br> <span style="font-size:11px; color:#64748b;">{{ $med->per_day ? $med->per_day . ' / ' . __('file.per_day') : '' }}</span></td>
                        <td class="center">{{ $med->duration_days ? $med->duration_days . ' ' . __('file.days') : '—' }}</td>
                        <td>{{ $med->instructions ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($prescription->notes)
        <div class="notes-box">
            <h3>{{ __('file.additional_notes_advice') }}</h3>
            <p style="margin:0; font-size:13px;">{!! nl2br(e($prescription->notes)) !!}</p>
        </div>
        @endif

        <div class="signature">
            <div class="signature-line">
                {{ __('file.doctor') }}. {{ $prescription->doctor->getFullNameAttribute() ?? '—' }}<br>
                <span style="font-size:11px;">{{ __('file.doctors_signature') }}</span>
            </div>
        </div>

        <div class="footer">
            <div style="font-weight:600; color:#2563eb; font-size:15px; margin-bottom:8px;">
                {{ __('file.wishing_recovery') }}
            </div>
            <div>{{ __('file.printed_on') }} {{ now()->format('d M Y h:i A') }}</div>
        </div>

    </div>

    <div class="no-print" style="position:fixed; bottom:24px; right:32px;">
        <button onclick="doPrintAndRedirect()"
            style="padding:12px 28px; background:#2563eb; color:white; border:none; border-radius:6px; font-size:15px; cursor:pointer; box-shadow:0 4px 12px rgba(37,99,235,0.3);">
            {{ __('file.print_return') }}
        </button>
    </div>

    <script type="text/javascript">
        function doPrintAndRedirect() {
            window.print();

            // Only redirect if NOT in an iframe
            if (window.self === window.top) {
                setTimeout(function () {
                    @if($redirect === 'pos')
                        window.location.href = "{{ route('invoices.pos') }}";
                    @else
                        window.location.href = "{{ route('prescriptions.index') }}";
                    @endif
                }, 1800);
            }
        }

        // Auto print after page loads
        window.onload = function () {
            setTimeout(doPrintAndRedirect, 900); // slight delay for better UX
        };
    </script>

</body>

</html>
