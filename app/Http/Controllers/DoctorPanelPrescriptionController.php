<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DoctorPanelPrescriptionController extends Controller
{
    public function index()
    {
        return view('doctor-panel.prescriptions');
    }

    public function datatable(Request $request)
    {
        $draw    = $request->input('draw');
        $start   = $request->input('start', 0);
        $length  = $request->input('length', 10);
        $search  = trim($request->input('search.value', ''));

        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return response()->json(['error' => 'No doctor profile found'], 403);
        }

        $query = Prescription::query()
            ->where('doctor_id', $doctor->id) // ← Only this doctor's prescriptions
            ->with(['patient'])
            ->withCount('medications')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($q) use ($search) {
                      $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });

        $totalRecords    = $query->count();
        $filteredRecords = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderDir         = $request->input('order.0.dir', 'desc');
        $columns          = ['id', 'prescription_date', 'patient_id', 'type', 'diagnosis', 'medications_count'];
        $orderColumn      = $columns[$orderColumnIndex] ?? 'prescription_date';

        $prescriptions = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $prescriptions->map(function ($prescription) {
            return [
                'id'                => $prescription->id,
                'prescription_date' => $prescription->prescription_date->format('M d, Y'),
                'patient_name'      => $prescription->patient?->getFullNameAttribute() ?? '-',
                'type'              => $prescription->type,
                'diagnosis'         => $prescription->diagnosis ? Str::limit($prescription->diagnosis, 50) : '-',
                'medications_count' => $prescription->medications_count,
                'show_url'          => route('doctor-panel.prescriptions.show', $prescription),
                // No edit/delete for now (can add later if needed)
            ];
        });

        return response()->json([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function show(Prescription $prescription)
    {
        // Optional security: ensure it's this doctor's prescription
        if ($prescription->doctor_id !== Auth::user()->doctor?->id) {
            abort(403);
        }

        $prescription->load('medications', 'patient', 'doctor');

        return view('doctor-panel.prescriptions.show', compact('prescription'));
    }
}