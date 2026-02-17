<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicineTemplate;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
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
            $edit_url   = Auth::user()->can('prescriptions.edit') ? route('prescriptions.edit', $prescription) : null;
            $delete_url = Auth::user()->can('prescriptions.delete') ? route('prescriptions.destroy', $prescription) : null;

            return [
                'id'                 => $prescription->id,
                'prescription_date'  => $prescription->prescription_date->format('M d, Y'),
                'type'               => $prescription->type,
                'diagnosis'          => $prescription->diagnosis ? Str::limit($prescription->diagnosis, 50) : '-',
                'patient_name'       => $prescription->patient?->getFullNameAttribute() ?? '-',
                'medications_count'  => $prescription->medications_count,
                'doctor_name'        => $prescription->doctor?->getFullNameAttribute() ?? '-',
                'show_url'           => route('prescriptions.show', $prescription),
                'edit_url'           => $edit_url,
                'delete_url'         => $delete_url,
                // Optional: if you add restore later → 'restore_url' => route('prescriptions.restore', $prescription)
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
        if (!Auth::user()->can('prescriptions.create')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_create_denied'));
        }

        $templates = MedicineTemplate::orderBy('name')->get();
        $patients  = Patient::active()->orderBy('first_name')->get();

        return view('prescriptions.create', compact('templates', 'patients'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('prescriptions.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'patient_id'           => 'required|exists:patients,id',
            'prescription_date'    => 'required|date',
            'type'                 => 'required|string|max:255',
            'diagnosis'            => 'nullable|string',
            'notes'                => 'nullable|string',
            'medicine_template_id' => 'nullable|exists:medicine_templates,id',
            'medications'          => 'nullable|array',
            'medications.*.name'   => 'required_with:medications|string|max:255',
            // ... add more per-medication rules if needed
        ]);

        $user = auth()->user();

        if (!$user->hasRole('doctor')) {
            return back()->with('error', __('file.only_doctors_can_create_prescriptions'))->withInput();
        }

        $doctor = $user->doctor;

        if (!$doctor) {
            return back()->with('error', __('file.no_doctor_profile_linked'))->withInput();
        }

        DB::transaction(function () use ($request, $doctor) {
            $prescription = Prescription::create([
                'patient_id'        => $request->patient_id,
                'doctor_id'         => $doctor->id,
                'prescription_date' => $request->prescription_date,
                'type'              => $request->type,
                'diagnosis'         => $request->diagnosis,
                'notes'             => $request->notes,
            ]);

            // Handle manual medications (from form) or template
            if ($request->filled('medicine_template_id')) {
                $template = MedicineTemplate::with('medications')->findOrFail($request->medicine_template_id);
                foreach ($template->medications as $med) {
                    $prescription->medications()->create($med->only([
                        'name', 'dosage', 'route', 'frequency', 'instructions'
                    ]));
                }
            } elseif ($request->has('medications')) {
                foreach ($request->medications as $medData) {
                    if (!empty($medData['name'])) {
                        $prescription->medications()->create([
                            'name'          => $medData['name'],
                            'dosage'        => $medData['dosage']        ?? null,
                            'route'         => $medData['route']         ?? null,
                            'frequency'     => $medData['frequency']     ?? null,
                            'duration_days' => $medData['duration_days'] ?? null,
                            'instructions'  => $medData['instructions']  ?? null,
                        ]);
                    }
                }
            }
        });

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

        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.edit')) {
            return redirect()->route('prescriptions.index')
                ->with('error', __('file.prescriptions_edit_denied'));
        }

        $templates = MedicineTemplate::orderBy('name')->get();
        $patients  = Patient::active()->orderBy('first_name')->get();
        $prescription->load('medications');

        return view('prescriptions.edit', compact('prescription', 'templates', 'patients'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'prescription_date' => 'required|date',
            'type'              => 'required|string|max:255',
            'diagnosis'         => 'nullable|string',
            'notes'             => 'nullable|string',
            'medications'       => 'nullable|array',
            'medications.*.name'=> 'required_with:medications|string|max:255',
        ]);

        DB::transaction(function () use ($request, $prescription) {
            $prescription->update($request->only([
                'patient_id', 'prescription_date', 'type', 'diagnosis', 'notes'
            ]));

            // Replace medications (simplest & most common pattern)
            $prescription->medications()->delete();

            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $medData) {
                    if (!empty($medData['name'])) {
                        $prescription->medications()->create([
                            'name'          => $medData['name'],
                            'dosage'        => $medData['dosage']        ?? null,
                            'route'         => $medData['route']         ?? null,
                            'frequency'     => $medData['frequency']     ?? null,
                            'duration_days' => $medData['duration_days'] ?? null,
                            'instructions'  => $medData['instructions']  ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('prescriptions.index')
            ->with('success', __('file.prescription_updated_successfully'));
    }

    public function destroy(Prescription $prescription)
    {
        if (!Auth::user()->can('prescriptions.delete')) {
            return back()->with('error', __('file.prescriptions_delete_denied'));
        }

        $prescription->delete(); // now soft-deletes

        return back()->with('success', __('file.prescription_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('prescriptions.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.prescriptions_bulk_delete_denied')
            ], 403);
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:prescriptions,id',
        ]);

        Prescription::whereIn('id', $ids)->delete(); // soft-deletes all

        return back()->with('success', __('file.selected_prescriptions_deleted'));
    }
}