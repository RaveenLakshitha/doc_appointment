<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patients List</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>Patients List</h1>
    <p class="text-center">Generated on: {{ now()->format('d M Y \a\t H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>MRN</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Last Visit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $p)
            <tr>
                <td>{{ $p->medical_record_number }}</td>
                <td>{{ $p->full_name }}</td>
                <td class="text-center">{{ $p->age }}</td>
                <td>{{ $p->gender }}</td>
                <td>{{ $p->last_visit }}</td>
                <td>{{ $p->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Patients: {{ $patients->count() }} | Exported by {{ auth()->user()?->name ?? 'System' }}
    </div>
</body>
</html>