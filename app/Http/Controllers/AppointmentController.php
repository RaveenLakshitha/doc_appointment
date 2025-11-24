<?php
// app/Http/Controllers/AppointmentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $sort       = $request->get('sort', 'appointment_datetime');
        $direction  = $request->get('direction', 'desc');

        $appointments = Appointment::query()
            ->with(['patient', 'doctor'])
            ->when($search, fn($q) => $q
                ->whereHas('patient', fn($sq) => $sq
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('medical_record_number', 'like', "%{$search}%")
                )
                ->orWhereHas('doctor', fn($sq) => $sq
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                )
                ->orWhere('status', 'like', "%{$search}%")
            )
            ->when(in_array($sort, ['appointment_datetime', 'status', 'duration_minutes']), fn($q) => $q->orderBy($sort, $direction))
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('appointments.index', compact('appointments', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $doctors = Doctor::active()
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get();

        $patients = Patient::active()
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get();

        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'         => 'required|exists:patients,id',
            'doctor_id'          => 'required|exists:doctors,id',
            'appointment_type'   => 'required|in:consultation,follow_up,procedure,checkup',
            'date'               => 'required|date|after_or_equal:today',
            'time'               => 'required|date_format:H:i',
            'duration_minutes'   => 'required|in:30,45,60,90',
            'reason_for_visit'   => 'required|string|max:1000',
            'status'             => 'required|in:scheduled,tentative,waitlist',
            'notes'              => 'nullable|string|max:2000',
        ]);

        // Combine date + time
        $datetime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['date'] . ' ' . $validated['time']
        );

        // Optional: Prevent double-booking
        $conflict = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_datetime', '<', $datetime->copy()->addMinutes($validated['duration_minutes']))
            ->where('appointment_datetime', '>', $datetime->copy()->subMinutes($validated['duration_minutes']))
            ->exists();

        if ($conflict) {
            return back()->withErrors(['time' => 'This time slot is already booked for this doctor.'])
                         ->withInput();
        }

        Appointment::create([
            'patient_id'         => $validated['patient_id'],
            'doctor_id'          => $validated['doctor_id'],
            'appointment_datetime'=> $datetime,
            'duration_minutes'   => $validated['duration_minutes'],
            'status'             => $validated['status'],
            'appointment_type'   => $validated['appointment_type'],
            'reason_for_visit'   => $validated['reason_for_visit'],
            'notes'              => $validated['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment scheduled successfully!');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        return view('appointments.show', compact('appointment'));
    }

    public function calendar(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $doctors  = Doctor::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('appointments.calendar', compact('doctors', 'doctorId'));
    }

    public function calendarEvents(Request $request)
    {
        $start    = $request->query('start');
        $end      = $request->query('end');
        $doctorId = $request->query('doctor_id');

        $query = Appointment::with(['patient', 'doctor'])
            ->where('appointment_datetime', '>=', $start)
            ->where('appointment_datetime', '<', $end);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($apt) {
            $start   = $apt->appointment_datetime;
            $end     = $start->clone()->addMinutes($apt->duration_minutes);

            $doctor  = $apt->doctor?->full_name ?? $apt->doctor?->name ?? 'Unknown';
            $patient = $apt->patient?->full_name ?? $apt->patient?->name ?? 'Unknown';
            $time    = $start->format('g:i A');
            $duration = $apt->duration_minutes . ' min';

            $color = match ($apt->status) {
                'completed'          => '#10b981',
                'cancelled'          => '#ef4444',
                'tentative', 'waitlist' => '#f59e0b',
                default              => '#3b82f6',
            };

            return [
                'id'    => $apt->id,
                'title' => "$patient\nDr. $doctor\n$time ($duration)",   // ← This is what appears in Day/Week
                'start' => $start->toDateTimeString(),                  // ← Correct local time (no UTC shift)
                'end'   => $end->toDateTimeString(),
                'url'   => route('appointments.show', $apt),

                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'displayEventTime' => false,  // We show time inside title → cleaner

                'extendedProps' => [
                    'patient'  => $patient,
                    'doctor'   => $doctor,
                    'time'     => $time,
                    'duration' => $duration,
                    'status'   => ucfirst($apt->status),
                ]
            ];
        });

        return response()->json($events);
    }

    public function edit(Appointment $appointment)
    {
        $patients   = Patient::active()->orderBy('name')->get();
        $doctors = Doctor::active()->orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'doctor_id'          => 'required|exists:doctors,id',
            'appointment_datetime'  => 'required|date',
            'duration_minutes'      => 'required|integer|min:15|max:300',
            'status'                => 'required|in:pending,confirmed,completed,cancelled',
            'notes'                 => 'nullable|string',
        ]);

        $appointment->update($request->only([
            'patient_id', 'doctor_id', 'appointment_datetime',
            'duration_minutes', 'status', 'notes',
        ]));

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete(); // soft delete
        return back()->with('success', 'Appointment deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:appointments,id'
        ]);

        $count = Appointment::whereIn('id', $request->ids)->delete();

        return back()->with('success', "$count appointment(s) deleted successfully.");
    }
}