<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;      
use App\Exports\PatientsExport;   

class PatientController extends Controller
{
     public function index(Request $request)
    {
        $patients = Patient::active()
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $search = trim($request->input('search.value', ''));

        $query = Patient::query()
            ->leftJoin('appointments', fn($join) =>
                $join->on('appointments.patient_id', '=', 'patients.id')
                    ->whereRaw('appointments.id = (SELECT MAX(id) FROM appointments a2 WHERE a2.patient_id = patients.id)')
            )
            ->select('patients.*', 'appointments.appointment_datetime as last_appointment_date')
            ->when($search !== '', fn($q) => $q
                ->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('medical_record_number', 'like', "%{$search}%")
            )
            ->active();

        $totalRecords = Patient::active()->count();
        $filteredRecords = $search !== '' ? (clone $query)->count() : $totalRecords;

        // Sorting
        if ($orderColumnIndex == 1) {
            $query->orderBy('medical_record_number', $orderDir);
        } elseif ($orderColumnIndex == 2) {
            $query->orderBy('first_name', $orderDir)->orderBy('last_name', $orderDir);
        } elseif ($orderColumnIndex == 3) {
            $query->orderBy('date_of_birth', $orderDir);
        } elseif ($orderColumnIndex == 4) {
            $query->orderByRaw("FIELD(LOWER(gender), 'male', 'female', 'other', NULL) {$orderDir}");
        } elseif ($orderColumnIndex == 5) {
            $query->orderBy('last_appointment_date', $orderDir);
        } elseif ($orderColumnIndex == 6) {
            $query->orderBy('is_active', $orderDir);
        } else {
            $query->orderBy('first_name', 'asc');
        }

        $patients = $query->offset($start)->limit($length)->get();
        $now = now();

        $data = $patients->map(function ($p) use ($now) {
            $lastVisit = $p->last_appointment_date 
                ? \Carbon\Carbon::parse($p->last_appointment_date)->format('M d, Y') 
                : null;

            $age = $p->date_of_birth 
                ? $p->date_of_birth->diffInYears($now) 
                : null;

            $genderBadge = match(strtolower($p->gender ?? '')) {
                'male'   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Male</span>',
                'female' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Female</span>',
                default  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Other</span>'
            };

            return [
                'id'                    => $p->id,
                'medical_record_number' => $p->medical_record_number ?? '',
                'full_name'             => $p->getFullNameAttribute(),
                'age'                   => $age !== null ? (int)$age : 0,
                'gender'                => $genderBadge,
                'last_visit'            => $lastVisit,
                'status_html'           => $p->is_active
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactive</span>',
                'show_url'              => route('patients.show', $p),
                'edit_url'              => route('patients.edit', $p),
                'delete_url'            => route('patients.destroy', $p),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create() { return view('patients.create'); }
    public function store(Request $request) {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:patients,email',
            'phone'                 => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:patients,phone',
            'medical_record_number' => 'required|string|unique:patients,medical_record_number',
            'date_of_birth'         => 'nullable|date',
            'gender'                => 'nullable|in:male,female,other',
            'address'               => 'nullable|string',
            'emergency_contact_name'=> 'nullable|string',
            'emergency_contact_phone'=> 'nullable|string',
        ]);

        Patient::create($request->only([
            'name','email','phone','medical_record_number',
            'date_of_birth','gender','address',
            'emergency_contact_name','emergency_contact_phone'
        ]) + ['is_active' => true, 'is_deleted' => false]);

        return redirect()->route('patients.index')->with('success', 'Patient created.');
    }

    public function show(Patient $patient) { return view('patients.show', compact('patient')); }
    public function edit(Patient $patient) { return view('patients.edit', compact('patient')); }

    public function update(Request $request, Patient $patient) {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:patients,email,'.$patient->id,
            'phone'                 => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:patients,phone,'.$patient->id,
            'medical_record_number' => 'required|string|unique:patients,medical_record_number,'.$patient->id,
            'date_of_birth'         => 'nullable|date',
            'gender'                => 'nullable|in:male,female,other',
            'address'               => 'nullable|string',
            'emergency_contact_name'=> 'nullable|string',
            'emergency_contact_phone'=> 'nullable|string',
            'is_active'             => 'sometimes|boolean',
        ]);

        $patient->update($request->only([
            'name','email','phone','medical_record_number',
            'date_of_birth','gender','address',
            'emergency_contact_name','emergency_contact_phone','is_active'
        ]));

        return redirect()->route('patients.index')->with('success', 'Patient updated.');
    }

    public function destroy(Patient $patient) {
        $patient->update(['is_deleted' => true, 'is_active' => false]);
        return back()->with('success', 'Patient deleted.');
    }

    public function bulkDelete(Request $request) {
        $ids = $request->input('ids');
        if (is_string($ids)) $ids = array_filter(explode(',', $ids));

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:patients,id',
        ]);

        Patient::whereIn('id', $ids)->update(['is_deleted' => true, 'is_active' => false]);
        return back()->with('success', 'Patients deleted.');
    }

    public function exportExcel()
    {
        return Excel::download(new PatientsExport, 'patients-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new PatientsExport, 'patients-' . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $patients = Patient::active()
            ->with(['lastAppointment' => fn($q) => $q->select('patient_id', 'appointment_datetime')])
            ->get();

        $now = now();

        $patients = $patients->map(function ($p) use ($now) {
            $lastVisit = $p->lastAppointment?->appointment_datetime
                ? \Carbon\Carbon::parse($p->lastAppointment->appointment_datetime)->format('M d, Y')
                : null;

            $age = $p->date_of_birth
                ? $p->date_of_birth->diffInYears($now)
                : null;

            return (object)[
                'medical_record_number' => $p->medical_record_number,
                'full_name'             => $p->getFullNameAttribute(),
                'age'                   => $age ? (int)$age : '-',
                'gender'                => ucfirst($p->gender ?? 'Other'),
                'last_visit'            => $lastVisit ?? 'Never',
                'status'                => $p->is_active ? 'Active' : 'Inactive',
            ];
        });

        $pdf = Pdf::loadView('patients.exports.pdf', compact('patients'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('patients-list-' . now()->format('Y-m-d') . '.pdf');
    }
}