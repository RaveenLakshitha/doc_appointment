<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('file.expense') }} {{ $expense->reference_no }}</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 4mm 4mm 6mm 4mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: white;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .container {
            width: 70mm;
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
            font-size: 11px;
            margin-top: 10px;
        }

        .right {
            text-align: right;
        }

        .expense-details {
            margin: 10px 0;
            padding: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .expense-details table {
            width: 100%;
        }

        .expense-details td {
            padding: 2px 0;
        }
        
        .amount {
            font-size: 14px;
            font-weight: bold;
        }

        .notes {
            margin: 10px 0;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 12px;
            padding-top: 5px;
            border-top: 1px dashed #000;
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

        @media print {
            body { margin: 0; padding: 0; }
            * { color: black !important; }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <div class="clinic">
                <h1>{{ $clinic_name }}</h1>
                <div>{{ $clinic_address }}</div>
                <div>Tel: {{ $clinic_phone }}</div>
                <div>Email: {{ $clinic_email }}</div>
            </div>
        </div>

        <div class="meta">
            <h2 style="text-align: left;">{{ __('file.expense') }}</h2>
            <table class="meta">
                <tr>
                    <td style="width: 35%;">{{ __('file.reference_no') }}:</td>
                    <td><strong>{{ $expense->reference_no }}</strong></td>
                </tr>
                <tr>
                    <td>{{ __('file.date') }}:</td>
                    <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : $expense->created_at->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>{{ __('file.cash_register') }}:</td>
                    <td>{{ $expense->cashRegister ? 'CR-' . str_pad($expense->cashRegister->id, 4, '0', STR_PAD_LEFT) : '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="expense-details">
            <table class="meta">
                <tr>
                    <td style="width: 35%;">{{ __('file.category') }}:</td>
                    <td>{{ $expense->expenseCategory?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>{{ __('file.added_by') }}:</td>
                    <td>{{ $expense->user?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="border-top: 1px dashed #000; margin: 4px 0;"></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px;"><strong>{{ __('file.amount') }}</strong></td>
                    <td class="amount" style="padding-top: 5px;">{{ $currency_code }} {{ number_format($expense->amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($expense->note)
            <div class="notes">
                <strong>{{ __('file.note') }}:</strong><br>
                <div style="padding-left: 2mm;">{!! nl2br(e($expense->note)) !!}</div>
            </div>
        @endif


        <div class="footer">
            {{ __('file.printed_on') }} {{ now()->format('d/m/Y h:i A') }}
        </div>

    </div>

    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
                setTimeout(function () {
                    window.location.href = "{{ route('expenses.index') }}";
                }, 1000);
            }, 500);
        };
    </script>

</body>

</html>
