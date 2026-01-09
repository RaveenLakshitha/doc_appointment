<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with(['patient', 'doctor', 'room'])
            ->orderByDesc('scheduled_start')
            ->paginate(15)
            ->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    public function datatable(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'desc');
        $searchValue = trim($request->input('search.value', ''));

        $statusFilter = $request->input('status');
        $doctorFilter = $request->input('doctor_id');

        $query = Appointment::query()
            ->with(['patient', 'doctor', 'room'])
            ->select('appointments.*');

        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('patient', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                  ->orWhereHas('doctor', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                  ->orWhere('reason_for_visit', 'like', "%{$searchValue}%")
                  ->orWhere('appointment_type', 'like', "%{$searchValue}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($doctorFilter) {
            $query->where('doctor_id', $doctorFilter);
        }

        $totalRecords = Appointment::count();
        $filteredRecords = (clone $query)->count();

        $columns = ['patient_name', 'doctor_name', 'scheduled_start', 'appointment_type', 'status', 'actions'];

        if (isset($columns[$orderIdx])) {
            switch ($columns[$orderIdx]) {
                case 'patient_name':
                    $query->join('patients', 'appointments.patient_id', '=', 'patients.id')
                          ->orderByRaw("CONCAT(patients.first_name, ' ', COALESCE(patients.middle_name,''), ' ', patients.last_name) {$orderDir}");
                    break;
                case 'doctor_name':
                    $query->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                          ->orderByRaw("CONCAT(doctors.first_name, ' ', COALESCE(doctors.middle_name,''), ' ', doctors.last_name) {$orderDir}");
                    break;
                case 'scheduled_start':
                    $query->orderBy('scheduled_start', $orderDir);
                    break;
                case 'appointment_type':
                    $query->orderBy('appointment_type', $orderDir);
                    break;
                case 'status':
                    $query->orderBy('status', $orderDir);
                    break;
                default:
                    $query->orderByDesc('scheduled_start');
            }
        } else {
            $query->orderByDesc('scheduled_start');
        }

        $appointments = $query->offset($start)->limit($length)->get();

        $data = $appointments->map(function ($appt) {
            $start = Carbon::parse($appt->scheduled_start);
            $dateTime = $start->format('M d, Y') . ' at ' . $start->format('h:i A');

            $statusBadge = match ($appt->status) {
                Appointment::STATUS_PENDING   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                Appointment::STATUS_APPROVED  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>',
                Appointment::STATUS_REJECTED  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Rejected</span>',
                Appointment::STATUS_CANCELLED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>',
                default                      => '<span class="text-gray-500">-</span>'
            };

            $typeLabel = match ($appt->appointment_type) {
                Appointment::TYPE_SPECIFIC         => 'Specific Doctor',
                Appointment::TYPE_ANY              => 'Any Doctor',
                Appointment::TYPE_PRIMARY_PROVIDER => 'Primary Provider',
                default                           => '-'
            };

            return [
                'id'                  => $appt->id,
                'patient_name'        => $appt->patient?->getFullNameAttribute() ?? '-',
                'doctor_name'         => $appt->doctor?->getFullNameAttribute() ?? '(Any Doctor)',
                'scheduled_datetime'  => $dateTime,
                'appointment_type'    => $typeLabel,
                'status'              => $appt->status,
                'status_badge'        => $statusBadge,
                'show_url'            => route('appointments.show', $appt),
                'edit_url'            => route('appointments.edit', $appt),
                'cancel_url'          => $appt->status === Appointment::STATUS_PENDING ? route('appointments.cancel', $appt) : null,
            ];
        });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        $specializations = Specialization::orderBy('name')->get();

        return view('appointments.create', compact('patients', 'specializations'));
    }

    public function store(Request $request)
    {
        $rules = [
            'patient_id'       => 'required|exists:patients,id',
            'scheduled_start'  => 'required|date|after_or_equal:today',
            'appointment_type' => ['required', Rule::in([
                Appointment::TYPE_SPECIFIC,
                Appointment::TYPE_ANY,
                Appointment::TYPE_PRIMARY_PROVIDER
            ])],
            'reason_for_visit' => 'required|string|max:1000',
            'patient_notes'    => 'nullable|string|max:2000',
        ];

        if ($request->input('appointment_type') === Appointment::TYPE_SPECIFIC) {
            $rules['doctor_id'] = 'required|exists:doctors,id';
        } else {
            $rules['doctor_id'] = 'nullable|exists:doctors,id';
        }

        $validated = $request->validate($rules);

        $start = Carbon::parse($validated['scheduled_start']);
        $durationMinutes = 30;
        $end = $start->copy()->addMinutes($durationMinutes);

        if ($validated['appointment_type'] === Appointment::TYPE_SPECIFIC) {
            $available = $this->isSlotAvailable($validated['doctor_id'], $start, $end);
            if (!$available) {
                return back()
                    ->withErrors(['scheduled_start' => 'This time slot is not available for the selected doctor.'])
                    ->withInput();
            }
        }

        Appointment::create([
            'patient_id'       => $validated['patient_id'],
            'doctor_id'        => $validated['doctor_id'] ?? null,
            'scheduled_start'  => $start,
            'scheduled_end'    => $end,
            'appointment_type' => $validated['appointment_type'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'patient_notes'    => $validated['patient_notes'],
            'status'           => Appointment::STATUS_PENDING,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment scheduled successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'room', 'canceller']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('last_name')->get();
        $specializations = Specialization::orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'specializations'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'nullable|exists:doctors,id',
            'scheduled_start'  => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'status'           => ['required', Rule::in([Appointment::STATUS_PENDING, Appointment::STATUS_APPROVED, Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])],
            'appointment_type' => ['required', Rule::in([Appointment::TYPE_SPECIFIC, Appointment::TYPE_ANY, Appointment::TYPE_PRIMARY_PROVIDER])],
            'reason_for_visit' => 'required|string|max:1000',
            'patient_notes'    => 'nullable|string|max:2000',
            'doctor_notes'     => 'nullable|string|max:2000',
            'admin_notes'      => 'nullable|string|max:2000',
        ]);

        $start = Carbon::parse($validated['scheduled_start']);
        $end = $start->copy()->addMinutes($validated['duration_minutes']);

        $doctorChanged = $appointment->doctor_id != $validated['doctor_id'];
        $timeChanged = $appointment->scheduled_start->toDateTimeString() !== $start->toDateTimeString();

        if (($doctorChanged || $timeChanged) && $validated['doctor_id']) {
            $available = $this->isSlotAvailable($validated['doctor_id'], $start, $end, $appointment->id);
            if (!$available) {
                return back()->withErrors(['scheduled_start' => 'This time slot is no longer available for the selected doctor.'])->withInput();
            }
        }

        $appointment->update([
            'patient_id'       => $validated['patient_id'],
            'doctor_id'        => $validated['doctor_id'],
            'scheduled_start'  => $start,
            'scheduled_end'    => $end,
            'status'           => $validated['status'],
            'appointment_type' => $validated['appointment_type'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'patient_notes'    => $validated['patient_notes'],
            'doctor_notes'     => $validated['doctor_notes'],
            'admin_notes'      => $validated['admin_notes'],
        ]);

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment updated successfully.');
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Only pending appointments can be cancelled.');
        }

        $appointment->update([
            'status'       => Appointment::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Appointment cancelled successfully.');
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

        $startDate = Carbon::parse($start);
        $endDate   = Carbon::parse($end);

        $query = Appointment::with(['patient', 'doctor'])
            ->where('scheduled_start', '<', $endDate)
            ->where('scheduled_end', '>', $startDate);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $query->whereIn('status', [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_APPROVED,
        ]);

        $appointments = $query->get();

        $events = $appointments->map(function ($apt) {
            $start    = Carbon::parse($apt->scheduled_start);
            $end      = Carbon::parse($apt->scheduled_end);
            $duration = $start->diffInMinutes($end);

            $doctor  = $apt->doctor?->getFullNameAttribute() ?? 'Unknown Doctor';
            $patient = $apt->patient?->getFullNameAttribute() ?? 'Unknown Patient';

            $color = match ($apt->status) {
                Appointment::STATUS_APPROVED  => '#10b981',
                Appointment::STATUS_CANCELLED => '#ef4444',
                Appointment::STATUS_REJECTED  => '#f59e0b',
                default                       => '#3b82f6',
            };

            return [
                'id'              => $apt->id,
                'title'           => $patient . ' - Dr. ' . $doctor,
                'start'           => $start->toDateTimeString(),
                'end'             => $end->toDateTimeString(),
                'url'             => route('appointments.show', $apt),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps' => [
                    'patient'  => $patient,
                    'doctor'   => $doctor,
                    'duration' => $duration . ' min',
                    'status'   => ucfirst(str_replace('_', ' ', $apt->status)),
                ]
            ];
        });

        return response()->json($events);
    }

    public function getDoctorsBySpecialization($specialization_id)
    {
        $doctors = Doctor::active()
            ->where('primary_specialization_id', $specialization_id)
            ->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name']);

        return response()->json($doctors->map(function ($doctor) {
            return [
                'value' => $doctor->id,
                'text'  => $doctor->getFullNameAttribute(),
            ];
        }));
    }

    public function availableSlots(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $dayName = Carbon::parse($request->date)->format('l');

        $schedules = $doctor->schedules()
            ->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $date);
            })
            ->with(['days' => function ($q) use ($dayName) {
                $q->where('day_of_week', $dayName);
            }])
            ->get()
            ->filter(function ($schedule) {
                return $schedule->days->isNotEmpty();
            });

        $slots = collect();

        foreach ($schedules as $schedule) {
            $start = Carbon::parse("$date {$schedule->start_time->format('H:i')}");
            $end   = Carbon::parse("$date {$schedule->end_time->format('H:i')}");

            $current = $start->copy();
            while ($current->copy()->addMinutes(30)->lte($end)) {
                $slots->push($current->format('H:i'));
                $current->addMinutes(30);
            }
        }

        $booked = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_start', $date)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_APPROVED])
            ->pluck('scheduled_start')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'));

        $availableSlots = $slots->unique()->diff($booked)->sort()->values()->toArray();

        return response()->json(['slots' => $availableSlots]);
    }

    private function isSlotAvailable($doctorId, Carbon $start, Carbon $end, $excludeId = null)
    {
        return !Appointment::where('doctor_id', $doctorId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('scheduled_start', [$start, $end])
                  ->orWhereBetween('scheduled_end', [$start, $end])
                  ->orWhereRaw('? BETWEEN scheduled_start AND scheduled_end', [$start])
                  ->orWhereRaw('? BETWEEN scheduled_start AND scheduled_end', [$end]);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        return match ($column) {
            'doctor' => Doctor::active()
                ->orderByFullName()
                ->get()
                ->mapWithKeys(function ($doctor) {
                    return [$doctor->id => $doctor->getFullNameAttribute()];
                })
                ->toArray(),
            'status' => [
                Appointment::STATUS_PENDING   => 'Pending',
                Appointment::STATUS_APPROVED  => 'Approved',
                Appointment::STATUS_REJECTED  => 'Rejected',
                Appointment::STATUS_CANCELLED => 'Cancelled',
            ],
            default => [],
        };
    }
}