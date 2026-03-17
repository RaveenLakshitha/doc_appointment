<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('file.prescription_label') }} {{ $prescription->id }}</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 4mm 4mm 6mm 4mm;
            /* Slightly more margin → safer on most printers */
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            /* Smaller base size for better fit */
            color: #000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: white;
            word-break: break-word;
            /* Prevent long words from overflowing */
            overflow-wrap: break-word;
        }

        .container {
            width: 70mm;
            /* Tightened — most safe printable area */
            margin: 0 auto;
            padding: 0 1.5mm;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }

        .clinic h1 {
            font-size: 15px;
            margin: 3px 0;
            font-weight: bold;
        }

        .meta {
            text-align: center;
            margin: 5px 0;
        }

        .meta h2 {
            font-size: 13px;
            margin: 3px 0;
            font-weight: bold;
        }

        table.meta {
            width: 100%;
            font-size: 10px;
        }

        .patient-doctor {
            margin: 6px 0;
            font-size: 10px;
        }

        .diagnosis {
            margin: 8px 0;
            padding: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        h3 {
            font-size: 12px;
            margin: 10px 0 4px;
            font-weight: bold;
            text-align: center;
        }

        .medication {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dotted #000;
            /* Light separator between meds */
        }

        .medication:last-child {
            border-bottom: none;
        }

        .med-label {
            font-weight: bold;
            display: inline-block;
            width: 90px;
            /* Adjust if needed — keeps alignment */
        }

        .notes {
            margin: 10px 0;
            font-size: 10px;
        }

        .signature {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
        }

        .signature-line {
            width: 60%;
            border-top: 1px solid #000;
            margin-top: 35px;
            padding-top: 4px;
            text-align: center;
            display: inline-block;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 12px;
            padding-top: 5px;
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
                <div>Tel: {{ $clinic_phone }}</div>
                <div>Email: {{ $clinic_email }}</div>
            </div>
        </div>

        <div class="meta">
            <h2>{{ __('file.prescription_label') }}</h2>
            <table class="meta">
                <tr>
                    <td>{{ __('file.date') }}:</td>
                    <td class="right">{{ $prescription->prescription_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>{{ __('file.type') }}:</td>
                    <td class="right">{{ __('file.' . strtolower($prescription->type)) }}</td>
                </tr>
                @if($prescription->appointment)
                    <tr>
                        <td>{{ __('file.appointment_number') }}:</td>
                        <td class="right">#{{ str_pad($prescription->appointment->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="patient-doctor">
            <strong>{{ __('file.patient') }}:</strong> {{ $prescription->patient->getFullNameAttribute() ?? '—' }}<br>
            {{ __('file.age') }}:
            {{ $prescription->patient->date_of_birth ? $prescription->patient->date_of_birth->age . ' ' . __('file.yrs') : '—' }}
            | {{ __('file.gender') }}:
            {{ $prescription->patient->gender ? __('file.' . strtolower($prescription->patient->gender)) : '—' }}<br>
            {{ __('file.phone') }}: {{ $prescription->patient->phone ?? '—' }}<br>
            {{ __('file.medical_record_number') }}: {{ $prescription->patient->medical_record_number ?? '—' }}<br><br>

            <strong>{{ __('file.doctor') }}:</strong> {{ $prescription->doctor->getFullNameAttribute() ?? '—' }}<br>
            {{ __('file.medical_professional') }}
        </div>

        @if($prescription->diagnosis)
            <div class="diagnosis">
                <strong>{{ __('file.diagnosis') }}:</strong><br>
                {{ $prescription->diagnosis }}
            </div>
        @endif

        <h3>{{ __('file.medications_rx') }}</h3>

        @foreach($prescription->medications as $index => $med)
            <div class="medication">
                <div><span class="med-label">#</span> {{ $index + 1 }}</div>
                <div><span class="med-label">Medicine</span> <strong>{{ $med->name }}</strong></div>
                <div><span class="med-label">Dosage / Route</span> {{ $med->dosage ?? '—' }} • {{ $med->route ?? 'Oral' }}
                </div>
                <div><span class="med-label">Frequency</span> {{ $med->frequency ?? '—' }}
                    {{ $med->per_day ? '(' . $med->per_day . ' / ' . __('file.per_day') . ')' : '' }}
                </div>
                <div><span class="med-label">Duration</span>
                    {{ $med->duration_days ? $med->duration_days . ' ' . __('file.days') : '—' }}</div>
                <div><span class="med-label">Instructions</span> {{ $med->instructions ?? '—' }}</div>
            </div>
        @endforeach

        @if($prescription->notes)
            <div class="notes">
                <strong>{{ __('file.additional_notes_advice') }}:</strong><br>
                {!! nl2br(e($prescription->notes)) !!}
            </div>
        @endif

        <div class="signature">
            <div class="signature-line">
                {{ $prescription->doctor->getFullNameAttribute() ?? '—' }}<br>
                <span style="font-size:9px;">{{ __('file.doctors_signature') }}</span>
            </div>
        </div>

        <div class="footer">
            {{ __('file.wishing_recovery') }}<br>
            {{ __('file.printed_on') }} {{ now()->format('d/m/Y h:i A') }}
        </div>

    </div>

    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
                setTimeout(function () {
                    @if($redirect === 'pos')
                        window.location.href = "{{ route('invoices.pos') }}";
                    @else
                        window.location.href = "{{ route('prescriptions.index') }}";
                    @endif
                }, 1000);
            }, 500);
        };
    </script>

</body>

</html>