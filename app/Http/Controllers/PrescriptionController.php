<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicineTemplate;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        // Pass a dummy or null patient if needed, or just load with relations
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
        $patientId = $request->input('patient_id'); // Optional filter by patient if needed

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

        $totalRecords = Prescription::count();
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
            return [
                'id'                 => $prescription->id,
                'prescription_date'  => $prescription->prescription_date->format('M d, Y'),
                'type'               => $prescription->type,
                'diagnosis'          => $prescription->diagnosis ? Str::limit($prescription->diagnosis, 50) : '-',
                'patient_name'       => $prescription->patient?->getFullNameAttribute() ?? '-',
                'medications_count'  => $prescription->medications_count,
                'doctor_name'        => $prescription->doctor?->getFullNameAttribute() ?? '-',
                'show_url'           => route('prescriptions.show', $prescription),
                'edit_url'           => route('prescriptions.edit', $prescription),
                'delete_url'         => route('prescriptions.destroy', $prescription),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        $templates = MedicineTemplate::orderBy('name')->get();
        $patients = Patient::active()->orderBy('first_name')->get();
        return view('prescriptions.create', compact('templates', 'patients'));
    }

    public function store(Request $request)
{
    $request->validate([
        'patient_id'           => 'required|exists:patients,id',
        'prescription_date'    => 'required|date',
        'type'                 => 'required|string|max:255',
        'diagnosis'            => 'nullable|string',
        'notes'                => 'nullable|string',
        'medicine_template_id' => 'nullable|exists:medicine_templates,id',
    ]);

    $user = auth()->user();

    // === DEBUG LOGS START ===
    Log::info('Prescription store attempt', [
        'user_id'   => $user?->id,
        'user_name' => $user?->name,
        'user_email'=> $user?->email,
    ]);

    // Check if user has 'doctor' role
    $hasDoctorRole = $user->hasRole('doctor');
    Log::info('User role check', [
        'user_id'       => $user->id,
        'has_doctor_role'=> $hasDoctorRole,
        'user_roles'    => $user->roles->pluck('name')->toArray(),
    ]);

    if (! $hasDoctorRole) {
        Log::warning('Prescription creation blocked: User missing doctor role', [
            'user_id' => $user->id,
        ]);

        return redirect()->back()
            ->withErrors(['error' => 'Access denied. Only doctors can create prescriptions.'])
            ->withInput();
    }

    // Check linked doctor profile
    $doctor = $user->doctor;

    Log::info('Doctor profile check', [
        'user_id'     => $user->id,
        'doctor_found'=> $doctor ? true : false,
        'doctor_id'   => $doctor?->id,
        'doctor_name' => $doctor?->getFullNameAttribute(),
    ]);

    if (! $doctor) {
        Log::warning('Prescription creation blocked: No doctor profile linked', [
            'user_id' => $user->id,
        ]);

        return redirect()->back()
            ->withErrors(['error' => 'No doctor profile linked to your account.'])
            ->withInput();
    }
    // === DEBUG LOGS END ===

    DB::transaction(function () use ($request, $doctor, $user) {
        Log::info('Creating prescription', [
            'patient_id'    => $request->patient_id,
            'doctor_id'     => $doctor->id,
            'prescription_date' => $request->prescription_date,
            'type'          => $request->type,
        ]);

        $prescription = Prescription::create([
            'patient_id'        => $request->patient_id,
            'doctor_id'         => $doctor->id,
            'prescription_date' => $request->prescription_date,
            'type'              => $request->type,
            'diagnosis'         => $request->diagnosis,
            'notes'             => $request->notes,
        ]);

        Log::info('Prescription created successfully', [
            'prescription_id' => $prescription->id,
        ]);

        if ($request->filled('medicine_template_id')) {
            $template = MedicineTemplate::with('medications')->findOrFail($request->medicine_template_id);

            foreach ($template->medications as $med) {
                $prescription->medications()->create($med->only([
                    'name', 'dosage', 'route', 'frequency', 'instructions'
                ]));
            }

            Log::info('Medications copied from template', [
                'template_id' => $request->medicine_template_id,
                'medication_count' => $template->medications->count(),
            ]);
        }
    });

    Log::info('Prescription store completed successfully', [
        'user_id' => $user->id,
        'doctor_id' => $doctor->id,
    ]);

    return redirect()->route('prescriptions.index')
        ->with('success', 'Prescription created successfully.');
}

    public function show(Prescription $prescription)
    {
        $prescription->load('medications', 'doctor', 'patient');
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $templates = MedicineTemplate::orderBy('name')->get();
        $patients = Patient::active()->orderBy('first_name')->get();
        $prescription->load('medications');
        return view('prescriptions.edit', compact('prescription', 'templates', 'patients'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'prescription_date' => 'required|date',
            'type'              => 'required|string|max:255',
            'diagnosis'         => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $prescription->update($request->only([
            'patient_id', 'prescription_date', 'type', 'diagnosis', 'notes'
        ]));

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return back()->with('success', 'Prescription deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) $ids = array_filter(explode(',', $ids));

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:prescriptions,id',
        ]);

        Prescription::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected prescriptions deleted.');
    }
}