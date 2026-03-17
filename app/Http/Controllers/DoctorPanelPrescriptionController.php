<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DoctorPanelPrescriptionController extends Controller
{
    public function index()
    {
        return view('doctor-panel.prescriptions');
    }

    public function queue(Request $request)
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return redirect()->route('home')
                ->with('error', __('file.no_doctor_profile_found') ?? 'No doctor profile found.');
        }

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : today();

        $appointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('scheduled_start', $date)
            ->whereIn('status', [Appointment::STATUS_APPROVED, Appointment::STATUS_RUNNING, Appointment::STATUS_PAID])
            ->with(['patient' => fn($q) => $q->select('id', 'first_name', 'middle_name', 'last_name')])
            ->orderBy('session_key')
            ->orderByRaw("CASE WHEN status = 'running' THEN 0 ELSE 1 END")
            ->orderBy('queue_number')
            ->get();

        $hasRunning = $appointments->contains('status', Appointment::STATUS_RUNNING);

        $queues = $appointments->groupBy('session_key')->map(function ($group) {
            return [
                'session_key' => $group->first()->session_key,
                'patients' => $group->map(fn($appt) => [
                    'queue_number' => $appt->queue_number,
                    'patient_name' => $appt->patient?->getFullNameAttribute() ?? '-',
                    'time' => $appt->scheduled_start?->format('h:i A'),
                    'id' => $appt->id,
                    'status' => $appt->status,
                ])->sortBy('queue_number'),
            ];
        });

        return view('doctor-panel.queue', compact('queues', 'date', 'hasRunning'));
    }

    public function datatable(Request $request)
    {
        try {
            $draw = $request->input('draw');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = trim($request->input('search.value', ''));
            $type = $request->input('type');
            $from = $request->input('from');
            $to = $request->input('to');

            $doctor = Auth::user()->doctor;

            if (!$doctor) {
                return response()->json(['error' => 'No doctor profile found'], 403);
            }

            $query = Prescription::query()
                ->where('doctor_id', $doctor->id)
                ->with(['patient'])
                ->withCount('medications')
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('diagnosis', 'like', "%{$search}%")
                            ->orWhere('notes', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhereHas('patient', function ($q) use ($search) {
                                $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                            });
                    });
                })
                ->when($type, fn($q) => $q->where('type', $type))
                ->when($from, fn($q) => $q->whereDate('prescription_date', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('prescription_date', '<=', $to));

            $totalRecords = Prescription::where('doctor_id', $doctor->id)->count();
            $filteredRecords = (clone $query)->count();

            $orderColumnIndex = $request->input('order.0.column', 1);
            $orderDir = $request->input('order.0.dir', 'desc');
            $columns = ['id', 'prescription_date', 'patient_id', 'type', 'diagnosis', 'medications_count'];
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
                    'prescription_date' => $prescription->prescription_date
                        ? $prescription->prescription_date->format('M d, Y')
                        : '—',
                    'patient_name' => $prescription->patient?->getFullNameAttribute() ?? '-',
                    'type' => $prescription->type ?? 'Standard',
                    'diagnosis' => $prescription->diagnosis ? Str::limit($prescription->diagnosis, 50) : '-',
                    'medications_count' => $prescription->medications_count ?? 0,
                    'show_url' => route('doctor-panel.prescriptions.show', $prescription),
                    'print_url' => route('prescriptions.print', $prescription),
                    'edit_url' => $edit_url ? $edit_url . '?from=doctor-panel' : null,
                    'delete_url' => $delete_url,
                ];
            });

            return response()->json([
                'draw' => (int) $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data->toArray(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('DoctorPanel prescriptions datatable error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => config('app.debug') ? $e->getMessage() : 'Server error — check logs.',
            ], 500);
        }
    }

    public function show(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::user()->doctor?->id) {
            abort(403);
        }

        $prescription->load('medications', 'patient', 'doctor');

        return view('prescriptions.show', compact('prescription'));
    }
}
