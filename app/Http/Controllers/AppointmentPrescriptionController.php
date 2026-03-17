<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\MedicineTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentPrescriptionController extends Controller
{
    public function create(Appointment $appointment)
    {
        if (!in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_COMPLETED])) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Prescription can only be created for approved or completed appointments.');
        }

        if ($appointment->prescriptions()->exists()) {
            $existing = $appointment->prescriptions()->latest()->first();
            return redirect()->route('prescriptions.edit', $existing)
                ->with('info', 'A prescription already exists for this appointment.');
        }

        $templates = MedicineTemplate::orderBy('name')->get();

        return view('prescriptions.create', compact('appointment', 'templates'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Invalid appointment status.');
        }

        $user = auth()->user();

        $hasDoctorRole = $user->hasRole('doctor');
        if (!$hasDoctorRole) {
            return redirect()->back()
                ->withErrors(['error' => 'Only doctors can create prescriptions.'])
                ->withInput();
        }

        $doctor = $user->doctor;

        if (!$doctor || $appointment->doctor_id !== $doctor->id) {
            return redirect()->back()
                ->withErrors(['error' => 'You are not authorized to prescribe for this appointment.'])
                ->withInput();
        }

        $validated = $request->validate([
            'prescription_date' => 'required|date',
            'type' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'medicine_template_id' => 'nullable|exists:medicine_templates,id',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required_if:medications present|string|max:255',
        ]);

        DB::transaction(function () use ($request, $appointment, $doctor) {
            $prescription = $appointment->prescriptions()->create([
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $doctor->id,
                'prescription_date' => $request->prescription_date,
                'type' => $request->type,
                'diagnosis' => $request->diagnosis,
                'notes' => $request->notes,
            ]);

            // Template medications
            if ($request->filled('medicine_template_id')) {
                $template = MedicineTemplate::with('medications')->findOrFail($request->medicine_template_id);
                foreach ($template->medications as $med) {
                    $prescription->medications()->create($med->only([
                        'name',
                        'dosage',
                        'route',
                        'frequency',
                        'instructions'
                    ]));
                }
            }

            // Manual medications from form
            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $med) {
                    if (!empty($med['name'])) {
                        $prescription->medications()->create([
                            'name' => $med['name'],
                            'inventory_item_id' => $med['inventory_item_id'] ?? null,
                            'dosage' => $med['dosage'] ?? null,
                            'route' => $med['route'] ?? 'Oral',
                            'frequency' => $med['frequency'] ?? null,
                            'per_day' => $med['per_day'] ?? 1,
                            'duration_days' => $med['duration_days'] ?? null,
                            'instructions' => $med['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Prescription created successfully.');
    }
}