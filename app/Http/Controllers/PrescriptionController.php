<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicineTemplate;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:prescriptions.index', ['only' => ['index', 'show', 'datatable', 'print']]);
        $this->middleware('permission:prescriptions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:prescriptions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:prescriptions.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
        if (!Auth::user()->can('prescriptions.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('prescriptions.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = trim($request->input('search.value', ''));
        $type = $request->input('type');
        $from = $request->input('from');
        $to = $request->input('to');
        $patientId = $request->input('patient_id');

        $query = Prescription::query()
            ->with(['patient', 'doctor'])
            ->withCount('medications')
            ->when($patientId, fn($q) => $q->where('patient_id', $patientId))
            ->when($search !== '', function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn($q) => $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]));
            })
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($from, fn($q) => $q->whereDate('prescription_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('prescription_date', '<=', $to));

        // Note: soft-deletes are automatically excluded here → perfect for index view

        $totalRecords = Prescription::count();           // non-deleted only
        $filteredRecords = (clone $query)->count();

        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = ['id', 'prescription_date', 'type', 'diagnosis', 'medications_count', 'patient_id'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'prescription_date';

        $prescriptions = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $prescriptions->map(function ($prescription) {
            $edit_url = Auth::user()->can('prescriptions.edit') ? route('prescriptions.edit', $prescription) : null;
            $delete_url = Auth::user()->can('prescriptions.delete') ? route('prescriptions.destroy', $prescription) : null;

            return [
                'id' => $prescription->id,
                'prescription_date' => $prescription->prescription_date->format('M d, Y'),
                'type' => $prescription->type,
                'diagnosis' => $prescription->diagnosis ? Str::limit($prescription->diagnosis, 50) : '-',
                'patient_name' => $prescription->patient?->getFullNameAttribute() ?? '-',
                'medications_count' => $prescription->medications_count,
                'doctor_name' => $prescription->doctor?->getFullNameAttribute() ?? '-',
                'show_url' => route('prescriptions.show', $prescription),
                'print_url' => route('prescriptions.print', $prescription) . '?redirect=prescriptions',
                'edit_url' => $edit_url,
                'delete_url' => $delete_url,
                // Optional: if you add restore later → 'restore_url' => route('prescriptions.restore', $prescription)
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function create(Request $request)
    {
        if (!Auth::user()->can('prescriptions.create')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_create_denied'));
        }

        $appointment = null;
        $preselectedPatient = null;

        if ($request->has('appointment_id')) {
            $appointment = Appointment::findOrFail($request->appointment_id);

            $doctor = Auth::user()->doctor;
            if ($appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
                return redirect()->route('appointments.show', $appointment)
                    ->with('error', 'Only the assigned doctor can create prescriptions for this appointment.');
            }

            if (!in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_COMPLETED]) && !$appointment->doctor_id) {
                return redirect()->route('appointments.show', $appointment)
                    ->with('error', 'Prescriptions require a doctor to be assigned or the appointment to be approved.');
            }

            if ($appointment->prescriptions()->exists()) {
                $existing = $appointment->prescriptions()->latest()->first();
                return redirect()->route('prescriptions.edit', $existing)
                    ->with('info', 'Prescription already exists for this appointment.');
            }

            $preselectedPatient = $appointment->patient;
        }

        $templates = MedicineTemplate::orderBy('name')->get();
        $patients = Patient::active()->orderBy('first_name')->get();
        $inventoryItems = \App\Models\InventoryItem::select('id', 'name', 'generic_name')->orderBy('name')->get();

        return view('prescriptions.create', compact('templates', 'patients', 'appointment', 'preselectedPatient', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('prescriptions.create')) {
            abort(403);
        }

        $user = auth()->user();
        $doctor = $user->doctor;

        if (!$doctor) {
            return back()->with('error', __('file.no_doctor_profile_linked'))->withInput();
        }

        $rules = [
            'prescription_date' => 'required|date',
            'type' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:1500',
            'notes' => 'nullable|string|max:3000',
            'medicine_template_id' => 'nullable|exists:medicine_templates,id',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required_with:medications|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:100',
            'medications.*.route' => 'nullable|string|max:100',
            'medications.*.frequency' => 'nullable|string|max:100',
            'medications.*.per_day' => 'nullable|numeric|min:0.01',
            'medications.*.duration_days' => 'nullable|integer|min:1|max:365',
            'medications.*.instructions' => 'nullable|string|max:500',
            'medications.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
        ];

        if (!$request->has('appointment_id')) {
            $rules['patient_id'] = 'required|exists:patients,id';
        }

        $validated = $request->validate($rules);

        $appointment = null;
        $patientId = null;

        if ($request->filled('appointment_id')) {
            $appointment = Appointment::findOrFail($request->appointment_id);

            // Only allow the assigned doctor to create prescriptions
            if ($appointment->doctor_id && $appointment->doctor_id !== $doctor->id) {
                return back()->with('error', 'Only the assigned doctor can create prescriptions for this appointment.')->withInput();
            }

            // Only allow approved or completed appointments OR assigned doctor
            if (!in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_COMPLETED]) && !$appointment->doctor_id) {
                return back()->with('error', 'Prescriptions require a doctor to be assigned or the appointment to be approved.')->withInput();
            }

            $patientId = $appointment->patient_id;
        } else {
            // Standalone prescription → use selected patient
            $patientId = $validated['patient_id'];
        }

        DB::transaction(function () use ($request, $doctor, $patientId, $appointment) {
            $prescriptionData = [
                'patient_id' => $patientId,
                'doctor_id' => $doctor->id,
                'prescription_date' => $request->prescription_date,
                'type' => $request->type,
                'diagnosis' => $request->diagnosis,
                'notes' => $request->notes,
            ];

            if ($appointment) {
                $prescriptionData['appointment_id'] = $appointment->id;
            }

            $prescription = Prescription::create($prescriptionData);

            if ($request->filled('medicine_template_id')) {
                $template = MedicineTemplate::with('medications')->findOrFail($request->medicine_template_id);
                foreach ($template->medications as $med) {
                    $prescription->medications()->create([
                        'name' => $med->name,
                        'inventory_item_id' => $med->inventory_item_id,
                        'dosage' => $med->dosage,
                        'route' => $med->route,
                        'frequency' => $med->frequency,
                        'per_day' => $med->per_day ?? 1,
                        'duration_days' => $med->duration_days ?? null,
                        'instructions' => $med->instructions,
                    ]);
                }
            } elseif ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $medData) {
                    if (!empty($medData['name'])) {
                        $prescription->medications()->create([
                            'name' => $medData['name'],
                            'inventory_item_id' => $medData['inventory_item_id'] ?? null,
                            'dosage' => $medData['dosage'] ?? null,
                            'route' => $medData['route'] ?? 'Oral',
                            'frequency' => $medData['frequency'] ?? null,
                            'per_day' => $medData['per_day'] ?? 1,
                            'duration_days' => $medData['duration_days'] ?? null,
                            'instructions' => $medData['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.prescription_created_successfully'),
                'prescription_id' => $prescriptionId ?? null
            ]);
        }

        if ($appointment && !$request->filled('from')) {
            return redirect()->route('appointments.show', $appointment)
                ->with('success', __('file.prescription_created_successfully'));
        }

        if ($request->input('from') === 'doctor-panel') {
            return redirect()->route('doctor-panel.prescriptions.index')
                ->with('success', __('file.prescription_created_successfully'));
        }

        return redirect()->route('prescriptions.index')
            ->with('success', __('file.prescription_created_successfully'));
    }

    public function show(Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.index')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_show_denied'));
        }

        $prescription->load('medications', 'doctor', 'patient');

        if (request()->wantsJson()) {
            return response()->json([
                'id' => $prescription->id,
                'date' => $prescription->prescription_date->format('M d, Y'),
                'date_iso' => $prescription->prescription_date->format('Y-m-d'),
                'type' => $prescription->type,
                'diagnosis' => $prescription->diagnosis,
                'notes' => $prescription->notes,
                'medications' => $prescription->medications->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'dosage' => $m->dosage,
                    'route' => $m->route,
                    'frequency' => $m->frequency,
                    'per_day' => $m->per_day,
                    'duration_days' => $m->duration_days,
                    'instructions' => $m->instructions,
                    'inventory_item_id' => $m->inventory_item_id
                ])
            ]);
        }

        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.edit')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_edit_denied'));
        }

        $doctor = Auth::user()->doctor;
        if ($prescription->appointment_id) {
            $appointment = $prescription->appointment;
            if ($appointment && $appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
                return redirect()->route('appointments.show', $appointment)
                    ->with('error', 'Only the assigned doctor can edit this prescription.');
            }
        }

        $templates = MedicineTemplate::orderBy('name')->get();
        $patients = Patient::active()->orderBy('first_name')->get();
        $inventoryItems = \App\Models\InventoryItem::select('id', 'name', 'generic_name')->orderBy('name')->get();
        $prescription->load('medications');

        return view('prescriptions.edit', compact('prescription', 'templates', 'patients', 'inventoryItems'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.edit')) {
            abort(403);
        }

        $rules = [
            'prescription_date' => 'required|date',
            'type' => 'required|string|max:255',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required_with:medications|string|max:255',
        ];

        if (!$request->has('appointment_id') && !$prescription->appointment_id) {
            $rules['patient_id'] = 'required|exists:patients,id';
        }

        $validated = $request->validate($rules);

        $patientId = $prescription->patient_id;
        $user = auth()->user();
        $doctor = $user->doctor;

        if ($prescription->appointment_id) {
            $appointment = \App\Models\Appointment::find($prescription->appointment_id);
            if ($appointment && $appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Only the assigned doctor can update this prescription.'], 403);
                }
                return back()->with('error', 'Only the assigned doctor can update this prescription.');
            }
        }

        if ($request->filled('appointment_id')) {
            $appointment = \App\Models\Appointment::find($request->appointment_id);
            if ($appointment) {
                $patientId = $appointment->patient_id;
            }
        } elseif ($request->filled('patient_id')) {
            $patientId = $request->patient_id;
        }

        DB::transaction(function () use ($request, $prescription, $patientId) {
            $data = $request->only([
                'prescription_date',
                'type',
                'diagnosis',
                'notes'
            ]);
            $data['patient_id'] = $patientId;

            $prescription->update($data);

            // Replace medications (simplest & most common pattern)
            $prescription->medications()->delete();

            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $medData) {
                    if (!empty($medData['name'])) {
                        $prescription->medications()->create([
                            'name' => $medData['name'],
                            'inventory_item_id' => $medData['inventory_item_id'] ?? null,
                            'dosage' => $medData['dosage'] ?? null,
                            'route' => $medData['route'] ?? null,
                            'frequency' => $medData['frequency'] ?? null,
                            'per_day' => $medData['per_day'] ?? 1,
                            'duration_days' => $medData['duration_days'] ?? null,
                            'instructions' => $medData['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.prescription_updated_successfully')
            ]);
        }

        if ($request->input('from') === 'doctor-panel') {
            return redirect()->route('prescriptions.show', $prescription)
                ->with('success', __('file.prescription_updated_successfully'));
        }

        return redirect()->route('prescriptions.index')
            ->with('success', __('file.prescription_updated_successfully'));
    }

    public function destroy(Prescription $prescription)
    {
        $user = auth()->user();
        $doctor = $user->doctor;

        if ($prescription->appointment_id) {
            $appointment = \App\Models\Appointment::find($prescription->appointment_id);
            if ($appointment && $appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Only the assigned doctor can delete this prescription.'], 403);
                }
                return back()->with('error', 'Only the assigned doctor can delete this prescription.');
            }
        }

        $prescription->delete(); // now soft-deletes

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.prescription_deleted_successfully')]);
        }

        return back()->with('success', __('file.prescription_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('prescriptions.delete')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.prescriptions_bulk_delete_denied')], 403);
            }
            return back()->with('error', __('file.prescriptions_bulk_delete_denied'));
        }

        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids ?? '')));
        }

        if (!is_array($ids) || empty($ids)) {
            $msg = __('file.no_items_selected');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        $validator = Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:prescriptions,id'
        ]);

        if ($validator->fails()) {
            $msg = 'Validation failed';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $validator->errors()], 422);
            }
            return back()->with('error', $msg);
        }

        Prescription::whereIn('id', $ids)->delete(); // soft-deletes all

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.selected_prescriptions_deleted')]);
        }

        return back()->with('success', __('file.selected_prescriptions_deleted'));
    }

    public function print(Request $request, Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.index')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_show_denied'));
        }

        $prescription->load(['medications', 'doctor', 'patient', 'appointment']);
        $redirect = $request->query('redirect', 'prescriptions');
        
        $settings = \App\Models\Setting::first();
        $paperSize = $settings->prescription_paper_size ?? 'A4';
        $viewName = $paperSize === '80mm' ? 'prescriptions.print-html-80mm' : 'prescriptions.print-html-a4';

        return view($viewName, compact('prescription', 'redirect'));
    }
}