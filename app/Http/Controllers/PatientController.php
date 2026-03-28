<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PatientsExport;
use DB;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('patients.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

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
        $ageFrom = $request->age_from;
        $ageTo = $request->age_to;

        $gender = $request->gender;
        $status = $request->status;
        $from = $request->from;
        $to = $request->to;

        $query = Patient::query()
            ->leftJoin(
                'appointments',
                fn($join) => $join
                    ->on('appointments.patient_id', '=', 'patients.id')
                    ->whereRaw('appointments.id = (
                    SELECT MAX(a2.id)
                    FROM appointments a2
                    WHERE a2.patient_id = patients.id
                    AND a2.deleted_at IS NULL
                )')
            )
            ->select(
                'patients.*',
                DB::raw('appointments.scheduled_start as last_appointment_date')
            )
            ->when(
                $search !== '',
                fn($q) => $q
                    ->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('medical_record_number', 'like', "%{$search}%")
            )
            ->when($gender, fn($q) => $q->where('patients.gender', $gender))
            ->when($status !== null && $status !== '', fn($q) => $q->where('patients.is_active', $status))
            ->when($ageFrom || $ageTo, fn($q) => $q->whereRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?", [
                $ageFrom ?? 0,
                $ageTo ?? 200
            ]))
            ->when($from || $to, fn($q) => $q->havingRaw(
                'last_appointment_date ' .
                ($from && $to ? 'BETWEEN ? AND ?' :
                    ($from ? '>= ?' :
                        ($to ? '<= ?' : ''))),
                collect([])
                    ->when($from, fn($c) => $c->push($from . ' 00:00:00'))
                    ->when($to, fn($c) => $c->push($to . ' 23:59:59'))
                    ->toArray()
            ))
            ->active();

        $totalRecords = Patient::active()->count();
        $filteredRecords = (clone $query)->count();

        if ($orderColumnIndex == 0) {
            $query->orderBy('patients.id', $orderDir);
        } elseif ($orderColumnIndex == 1) {
            $query->orderBy('medical_record_number', $orderDir);
        } elseif ($orderColumnIndex == 2) {
            $query->orderBy('first_name', $orderDir)->orderBy('last_name', $orderDir);
        } elseif ($orderColumnIndex == 3) {
            $query->orderBy('date_of_birth', $orderDir);
        } elseif ($orderColumnIndex == 4) {
            $query->orderByRaw("FIELD(LOWER(patients.gender), 'male', 'female', 'other', NULL) {$orderDir}");
        } elseif ($orderColumnIndex == 5) {
            $query->orderBy('last_appointment_date', $orderDir);
        } elseif ($orderColumnIndex == 6) {
            $query->orderBy('is_active', $orderDir);
        } else {
            $query->orderBy('patients.id', 'desc');
        }

        $patients = $query->offset($start)->limit($length)->get();
        $now = now();

        $data = $patients->map(function ($p) use ($now) {
            $lastVisit = $p->last_appointment_date
                ? \Carbon\Carbon::parse($p->last_appointment_date)->format('M d, Y')
                : null;

            $age = $p->age !== null ? $p->age : ($p->date_of_birth ? $p->date_of_birth->diffInYears($now) : null);

            $genderBadge = match (strtolower($p->gender ?? '')) {
                'male' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Male</span>',
                'female' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Female</span>',
                default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Other</span>'
            };

            $edit_url = Auth::user()->can('patients.edit') ? route('patients.edit', $p) : null;
            $delete_url = Auth::user()->can('patients.delete') ? route('patients.destroy', $p) : null;

            return [
                'id' => $p->id,
                'medical_record_number' => $p->medical_record_number ?? '',
                'full_name' => $p->getFullNameAttribute(),
                'age' => $age !== null ? (int) $age : null,
                'gender' => $genderBadge,
                'phone' => $p->phone ?? '-',
                'last_visit' => $lastVisit,
                'status_html' => $p->is_active
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactive</span>',
                'show_url' => route('patients.show', $p),
                'edit_url' => $edit_url,
                'delete_url' => $delete_url,
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('patients.create')) {
            return redirect()->route('patients.index')
                ->with('error', __('file.patients_create_denied'));
        }

        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $languages = \App\Models\OptionList::getOptions('language');
        $cfdiOptions = Patient::getCfdiUsageOptions();

        return view('patients.create', compact('ageGroups', 'languages', 'cfdiOptions'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('patients.create')) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer|min:0',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            'middle_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'alternative_phone' => 'nullable|string',
            'attended_psychotherapy' => 'nullable|boolean',
            'preferred_session_time' => 'nullable|string',
            'recommended_by' => 'nullable|string',
            'recommended_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'blood_type' => 'nullable|string',
            'height_cm' => 'nullable|integer',
            'weight_kg' => 'nullable|integer',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'past_surgeries' => 'nullable|string',
            'previous_hospitalizations' => 'nullable|string',
            'smoking_status' => 'nullable|string',
            'alcohol_consumption' => 'nullable|string',
            'exercise_frequency' => 'nullable|string',
            'dietary_habits' => 'nullable|string',
            'preferred_contact_method' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_email' => 'nullable|email',
            'age_group_id' => 'nullable|exists:age_groups,id',
            'preferred_language_id' => 'nullable|exists:option_lists,id',
            'tax_id' => 'nullable|string|max:255',
            'tax_full_name' => 'nullable|string|max:255',
            'tax_postal_code' => 'nullable|string|max:255',
            'tax_regime' => 'nullable|string|max:255',
            'tax_invoice_usage' => 'nullable|string|max:255',
            'tax_cfdi_upload' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        do {
            $medicalRecordNumber = 'MRN' . mt_rand(1000000, 9999999);
        } while (\App\Models\Patient::where('medical_record_number', $medicalRecordNumber)->exists());
        $data = $request->only([
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth',
            'age',
            'gender',
            'marital_status',
            'address',
            'city',
            'state',
            'zip_code',
            'phone',
            'alternative_phone',
            'attended_psychotherapy',
            'preferred_session_time',
            'recommended_by',
            'blood_type',
            'height_cm',
            'weight_kg',
            'allergies',
            'current_medications',
            'chronic_conditions',
            'past_surgeries',
            'previous_hospitalizations',
            'smoking_status',
            'alcohol_consumption',
            'exercise_frequency',
            'dietary_habits',
            'preferred_contact_method',
            'emergency_contact_name',
            'emergency_contact_relationship',
            'emergency_contact_phone',
            'emergency_contact_email',
            'age_group_id',
            'preferred_language_id',
            'tax_id',
            'tax_full_name',
            'tax_postal_code',
            'tax_regime',
            'tax_invoice_usage',
        ]);

        $commaSeparated = ['allergies', 'current_medications', 'chronic_conditions'];
        foreach ($commaSeparated as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_filter(array_map('trim', explode(',', $data[$field])));
            } elseif (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $newlineSeparated = ['past_surgeries', 'previous_hospitalizations'];
        foreach ($newlineSeparated as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_filter(array_map('trim', explode("\n", $data[$field])));
            } elseif (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $newPatient = Patient::create($data + [
            'medical_record_number' => $medicalRecordNumber,
            'is_active' => true,
            'is_deleted' => false,
        ]);

        if ($request->hasFile('recommended_document')) {
            $file2 = $request->file('recommended_document');
            $filename2 = time() . '_rec_' . $file2->getClientOriginalName();
            $file2->move(public_path('patient_documents'), $filename2);
            $newPatient->update(['recommended_document' => 'patient_documents/' . $filename2]);
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_doc_' . $file->getClientOriginalName();
            $file->move(public_path('patient_documents'), $filename);
            $newPatient->update(['document' => 'patient_documents/' . $filename]);
        }

        if ($request->hasFile('tax_cfdi_upload')) {
            $file = $request->file('tax_cfdi_upload');
            $filename = time() . '_tax_' . $file->getClientOriginalName();
            $file->move(public_path('patient_documents'), $filename);
            $newPatient->update(['tax_cfdi_path' => 'patient_documents/' . $filename]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $newPatient->id,
                    'full_name' => $newPatient->full_name,
                    'medical_record_number' => $newPatient->medical_record_number
                ],
                'message' => __('file.patient_added_successfully')
            ]);
        }

        return redirect()->route('patients.index')
            ->with('success', __('file.patients_created_successfully'));
    }

    public function show(Patient $patient)
    {
        if (!Auth::user()->can('patients.index')) {
            return redirect()->route('patients.index')
                ->with('error', __('file.patients_show_denied'));
        }

        $patient->load(['appointments', 'prescriptions', 'invoices.payments']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        if (!Auth::user()->can('patients.edit')) {
            return redirect()->route('patients.index')
                ->with('error', __('file.patients_edit_denied'));
        }

        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $languages = \App\Models\OptionList::getOptions('language');
        $cfdiOptions = Patient::getCfdiUsageOptions();

        return view('patients.edit', compact('patient', 'ageGroups', 'languages', 'cfdiOptions'));
    }

    public function update(Request $request, Patient $patient)
    {
        if (!Auth::user()->can('patients.edit')) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer|min:0',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            'middle_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'alternative_phone' => 'nullable|string',
            'attended_psychotherapy' => 'nullable|boolean',
            'preferred_session_time' => 'nullable|string',
            'recommended_by' => 'nullable|string',
            'recommended_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'blood_type' => 'nullable|string',
            'height_cm' => 'nullable|integer',
            'weight_kg' => 'nullable|integer',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'past_surgeries' => 'nullable|string',
            'previous_hospitalizations' => 'nullable|string',
            'smoking_status' => 'nullable|string',
            'alcohol_consumption' => 'nullable|string',
            'exercise_frequency' => 'nullable|string',
            'dietary_habits' => 'nullable|string',
            'preferred_contact_method' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_email' => 'nullable|email',
            'age_group_id' => 'nullable|exists:age_groups,id',
            'preferred_language_id' => 'nullable|exists:option_lists,id',
            'tax_id' => 'nullable|string|max:255',
            'tax_full_name' => 'nullable|string|max:255',
            'tax_postal_code' => 'nullable|string|max:255',
            'tax_regime' => 'nullable|string|max:255',
            'tax_invoice_usage' => 'nullable|string|max:255',
            'tax_cfdi_upload' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = $request->only([
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth',
            'age',
            'gender',
            'marital_status',
            'address',
            'city',
            'state',
            'zip_code',
            'phone',
            'alternative_phone',
            'attended_psychotherapy',
            'preferred_session_time',
            'recommended_by',
            'blood_type',
            'height_cm',
            'weight_kg',
            'allergies',
            'current_medications',
            'chronic_conditions',
            'past_surgeries',
            'previous_hospitalizations',
            'smoking_status',
            'alcohol_consumption',
            'exercise_frequency',
            'dietary_habits',
            'preferred_contact_method',
            'emergency_contact_name',
            'emergency_contact_relationship',
            'emergency_contact_phone',
            'emergency_contact_email',
            'age_group_id',
            'preferred_language_id',
            'tax_id',
            'tax_full_name',
            'tax_postal_code',
            'tax_regime',
            'tax_invoice_usage',
        ]);

        $commaSeparated = ['allergies', 'current_medications', 'chronic_conditions'];
        foreach ($commaSeparated as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_filter(array_map('trim', explode(',', $data[$field])));
            } elseif (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $newlineSeparated = ['past_surgeries', 'previous_hospitalizations'];
        foreach ($newlineSeparated as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_filter(array_map('trim', explode("\n", $data[$field])));
            } elseif (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $patient->update($data);


        if ($request->hasFile('recommended_document')) {
            if ($patient->recommended_document && file_exists(public_path($patient->recommended_document))) {
                unlink(public_path($patient->recommended_document));
            }
            $file2 = $request->file('recommended_document');
            $filename2 = time() . '_rec_' . $file2->getClientOriginalName();
            $file2->move(public_path('patient_documents'), $filename2);
            $patient->update(['recommended_document' => 'patient_documents/' . $filename2]);
        }

        if ($request->hasFile('document')) {
            if ($patient->document && file_exists(public_path($patient->document))) {
                unlink(public_path($patient->document));
            }
            $file = $request->file('document');
            $filename = time() . '_doc_' . $file->getClientOriginalName();
            $file->move(public_path('patient_documents'), $filename);
            $patient->update(['document' => 'patient_documents/' . $filename]);
        }

        if ($request->hasFile('tax_cfdi_upload')) {
            if ($patient->tax_cfdi_path && file_exists(public_path($patient->tax_cfdi_path))) {
                unlink(public_path($patient->tax_cfdi_path));
            }
            $file = $request->file('tax_cfdi_upload');
            $filename = time() . '_tax_' . $file->getClientOriginalName();
            $file->move(public_path('patient_documents'), $filename);
            $patient->update(['tax_cfdi_path' => 'patient_documents/' . $filename]);
        }

        return redirect()->route('patients.index')
            ->with('success', __('file.patients_updated_successfully'));
    }

    public function destroy(Request $request, Patient $patient)
    {
        if (!Auth::user()->can('patients.delete')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.patients_delete_denied')]);
            }
            return redirect()->route('patients.index')
                ->with('error', __('file.patients_delete_denied'));
        }

        $patient->update(['is_deleted' => true, 'is_active' => false]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('file.patients_deleted_successfully')]);
        }
        return back()->with('success', __('file.patients_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('patients.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.patients_bulk_delete_denied')
            ], 403);
        }

        $ids = $request->input('ids');
        if (is_string($ids))
            $ids = array_filter(explode(',', $ids));

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,id',
        ]);

        Patient::whereIn('id', $ids)->update(['is_deleted' => true, 'is_active' => false]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('file.patients_bulk_deleted_successfully')]);
        }
        return back()->with('success', __('file.patients_bulk_deleted_successfully'));
    }

    public function filters(Request $request)
    {
        $column = (int) $request->get('column');

        return match ($column) {
            1 => $this->uniqueValues('medical_record_number'),

            2 => $this->uniqueValues(
                raw: "TRIM(CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')))",
                alias: 'full_name'
            ),

            4 => $this->uniqueValues('gender'),

            6 => $this->uniqueValues(
                raw: "CASE WHEN is_active THEN 'Active' ELSE 'Inactive' END",
                alias: 'status_label'
            ),

            default => response()->json([]),
        };
    }

    private function uniqueValues(string $field = null, ?string $raw = null, string $alias = null)
    {
        $query = Patient::query();

        if ($raw) {
            $query->selectRaw("$raw AS `$alias`");
            $orderBy = $alias;
        } else {
            $query->select($field);
            $orderBy = $field;
        }

        return $query
            ->active()
            ->distinct()
            ->orderBy($orderBy)
            ->pluck($orderBy)
            ->filter()
            ->values()
            ->toArray();
    }

    public function search(Request $request)
    {
        $term = trim($request->get('q', '') ?? '');

        $query = Patient::query()
            ->select([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'medical_record_number'
            ])
            ->when($term, function ($q) use ($term) {
                $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%$term%"])
                    ->orWhere('medical_record_number', 'LIKE', "%$term%");
            })
            ->orderBy('last_name')
            ->orderBy('first_name');

        $patients = $query->paginate(15);

        $results = $patients->getCollection()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'text' => $patient->full_name . ' (MRN: ' . ($patient->medical_record_number ?? 'N/A') . ')'
                // or if you prefer explicit:
                // 'text' => trim("{$patient->first_name} {$patient->middle_name} {$patient->last_name}") . ' (MRN: ' . ($patient->medical_record_number ?? 'N/A') . ')'
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $patients->hasMorePages()
            ]
        ]);
    }

    public function select2Single(Patient $patient)
    {
        return response()->json([
            'id' => $patient->id,
            'text' => $patient->full_name . ' (MRN: ' . ($patient->medical_record_number ?? 'N/A') . ')'
        ]);
    }

    public function downloadTemplate()
    {
        $locale = app()->getLocale();

        if ($locale === 'es') {
            $headers = [
                'Nombre',
                'Segundo Nombre',
                'Apellido',
                'Fecha de Nacimiento',
                'Edad',
                'Genero',
                'Telefono',
                'Correo Electronico',
                'Direccion',
                'Ciudad',
                'Estado',
                'Codigo Postal',
                'Estado Civil'
            ];
            $sampleRow = [
                'Juan',
                'Carlos',
                'Perez',
                '1990-05-15',
                '33',
                'masculino',
                '+525512345678',
                'juan.perez@example.com',
                'Av. Insurgentes 123',
                'Ciudad de Mexico',
                'CDMX',
                '01000',
                'soltero'
            ];
        } else {
            $headers = [
                'First Name',
                'Middle Name',
                'Last Name',
                'Date of Birth',
                'Age',
                'Gender',
                'Phone',
                'Email',
                'Address',
                'City',
                'State',
                'Zip Code',
                'Marital Status'
            ];
            $sampleRow = [
                'John',
                'Edward',
                'Doe',
                '1990-05-15',
                '33',
                'male',
                '+1234567890',
                'john.doe@example.com',
                '123 Main St',
                'New York',
                'NY',
                '10001',
                'single'
            ];
        }

        $callback = function () use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            // Adding BOM for UTF-8 to ensure Excel reads Spanish accents and characters properly if used
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patients_import_template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PatientsImport, $request->file('file'));
            return redirect()->back()->with('success', __('file.success'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('file.error') . ': ' . $e->getMessage());
        }
    }
}