<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\DoctorSessionQueue;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\NewAppointmentCreated;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Treatment;

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
            $scheduledDisplay = 'Not set';
            $scheduledClass = 'text-gray-500 dark:text-gray-400 italic';

            if ($appt->scheduled_start) {
                $start = Carbon::parse($appt->scheduled_start);
                $scheduledDisplay = $start->format('M d, Y') . ' at ' . $start->format('h:i A');

                if ($appt->scheduled_end) {
                    $end = Carbon::parse($appt->scheduled_end);
                    if ($end->greaterThan($start)) {
                        $scheduledDisplay .= ' – ' . $end->format('h:i A');
                    }
                }

                $scheduledClass = '';
            }

            $statusBadge = match ($appt->status) {
                Appointment::STATUS_PENDING   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                Appointment::STATUS_APPROVED  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>',
                Appointment::STATUS_REJECTED  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Rejected</span>',
                Appointment::STATUS_CANCELLED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>',
                default                      => '<span class="text-gray-500">-</span>'
            };

            return [
                'id'                  => $appt->id,
                'appointment_number'  => $appt->appointment_number ?? '—',
                'patient_name'        => $appt->patient?->getFullNameAttribute() ?? '-',
                'doctor_name'         => $appt->doctor?->getFullNameAttribute() ?? '(Any Doctor)',
                'scheduled_datetime'  => $scheduledDisplay,
                'scheduled_class'     => $scheduledClass,
                'status'              => $appt->status,
                'status_badge'        => $statusBadge,
                'queue_info'          => $appt->status === Appointment::STATUS_APPROVED && $appt->queue_number
                    ? strtoupper($appt->session_key) . ' #' . $appt->queue_number
                    : '-',
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
            'patient_id'          => 'required|exists:patients,id',
            'appointment_type'    => ['required', Rule::in([
                Appointment::TYPE_SPECIFIC,
                Appointment::TYPE_ANY,
                Appointment::TYPE_PRIMARY_PROVIDER
            ])],
            'reason_for_visit'    => 'required|string|max:1000',
            'patient_notes'       => 'nullable|string|max:2000',
            'scheduled_start'     => 'nullable|date|after_or_equal:today',
        ];

        if ($request->input('appointment_type') === Appointment::TYPE_SPECIFIC) {
            $rules['doctor_id'] = 'required|exists:doctors,id';
            $rules['specialization_id'] = 'nullable|exists:specializations,id';
        } elseif ($request->input('appointment_type') === Appointment::TYPE_ANY) {
            $rules['specialization_id'] = 'required|exists:specializations,id';
            $rules['doctor_id'] = 'nullable|exists:doctors,id';
        } else {
            $rules['doctor_id'] = 'nullable|exists:doctors,id';
            $rules['specialization_id'] = 'nullable|exists:specializations,id';
        }

        $validated = $request->validate($rules);

        $appointmentData = [
            'patient_id'          => $validated['patient_id'],
            'appointment_type'    => $validated['appointment_type'],
            'reason_for_visit'    => $validated['reason_for_visit'],
            'patient_notes'       => $validated['patient_notes'] ?? null,
            'status'              => Appointment::STATUS_PENDING,
            'specialization_id'   => $validated['specialization_id'] ?? null,
        ];

        if ($validated['appointment_type'] === Appointment::TYPE_SPECIFIC) {
            $appointmentData['doctor_id'] = $validated['doctor_id'] ?? null;
        } else {
            $appointmentData['doctor_id'] = null;
        }

        if (!empty($validated['scheduled_start'])) {
            $start = Carbon::parse($validated['scheduled_start']);
            $durationMinutes = 30;
            $end = $start->copy()->addMinutes($durationMinutes);

            $appointmentData['scheduled_start'] = $start;
            $appointmentData['scheduled_end']   = $end;
        }

        $appointment = Appointment::create($appointmentData);

        $recipients = User::role(['admin', 'receptionist'])->get();

        Notification::send(
            $recipients,
            new NewAppointmentCreated($appointment)
        );

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment request created successfully.'
                . (empty($validated['scheduled_start']) ? ' Time slot will be assigned later.' : ''));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient' => fn($q) => $q->select([
                'id', 'first_name', 'middle_name', 'last_name',
                'phone', 'email', 'date_of_birth', 'gender',
                'medical_record_number'
            ]),
            'doctor' => fn($q) => $q->select([
                'id', 'first_name', 'middle_name', 'last_name',
                'primary_specialization_id'
            ])->with('primarySpecialization:id,name'),
            'room:id,name,location',
            'canceller:id,name',
            'specialization:id,name',
            'treatments' => fn($q) => $q->select(
                'treatments.id',
                'treatments.name',
                'treatments.code'
            ),
        ]);

        $appointment->duration_minutes = $appointment->scheduled_start && $appointment->scheduled_end
            ? $appointment->scheduled_start->diffInMinutes($appointment->scheduled_end)
            : null;

        // Load only treatments that this doctor has configured
        $treatments = collect();
        if ($appointment->doctor) {
            $treatments = $appointment->doctor
                ->treatments()
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        $specializations = Specialization::orderBy('name')->get();

        // Currency fallback to USD
        $currency_code = config('app.currency', 'USD');

        return view('appointments.show', compact(
            'appointment',
            'specializations',
            'treatments',
            'currency_code'
        ));
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

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
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

        return response()->json($doctors->map(fn($d) => [
            'value' => $d->id,
            'text'  => $d->getFullNameAttribute(),
        ]));
    }

    public function availableSlots(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = Carbon::parse($request->date);
        $dayName = $date->format('l');

        $schedules = $doctor->schedules()
            ->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $date);
            })
            ->whereHas('days', function ($q) use ($dayName) {
                $q->where('day_of_week', $dayName);
            })
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            $start = $date->copy()->setTimeFromTimeString($schedule->start_time->format('H:i'));
            $end   = $date->copy()->setTimeFromTimeString($schedule->end_time->format('H:i'));

            $slotLabel = $start->format('g:i A') . ' - ' . $end->format('g:i A');

            $slots[] = [
                'start' => $start->format('H:i'),
                'end'   => $end->format('H:i'),
                'label' => $slotLabel
            ];
        }

        usort($slots, function ($a, $b) {
            return strcmp($a['start'], $b['start']);
        });

        return response()->json([
            'slots' => $slots,
            'message' => $slots ? null : 'No schedule found for this date'
        ]);
    }

    private function isSlotAvailable($doctorId, Carbon $start, Carbon $end, $excludeId = null)
    {
        return true;
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

    public function ticket(Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_APPROVED) {
            abort(403, 'Appointment not approved yet.');
        }
        
        return view('appointments.ticket', compact('appointment'));
    }

    public function pending(Request $request)
    {
        $appointments = Appointment::query()
            ->with(['patient' => fn($q) => $q->select('id', 'first_name', 'middle_name', 'last_name')])
            ->where('status', Appointment::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('appointments.pending', [
            'appointments' => $appointments,
            'TYPE_SPECIFIC' => Appointment::TYPE_SPECIFIC,
            'TYPE_ANY'      => Appointment::TYPE_ANY,
            'TYPE_PRIMARY'  => Appointment::TYPE_PRIMARY_PROVIDER,
        ]);
    }

    public function approve(Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'This appointment cannot be approved anymore.');
        }

        if (!$appointment->doctor_id || !$appointment->scheduled_start) {
            return back()->with('error', 'Doctor and time required.');
        }

        $date = $appointment->scheduled_start->startOfDay();
        $sessionKey = $this->generateSessionKey($appointment);

        if (!$sessionKey) {
            return back()->with('error', 'Cannot determine session time.');
        }

        try {
            $queueNumber = DB::transaction(function () use ($appointment, $date, $sessionKey) {
                $queue = DoctorSessionQueue::lockForUpdate()
                    ->firstOrCreate(
                        [
                            'doctor_id'   => $appointment->doctor_id,
                            'queue_date'  => $date,
                            'session_key' => $sessionKey,
                        ],
                        ['last_number' => 0]
                    );

                $next = $queue->last_number + 1;
                $queue->update(['last_number' => $next]);

                return $next;
            });

            $year = now()->format('y');
            $nextNum = Appointment::whereYear('created_at', now()->year)
                ->whereNotNull('appointment_number')
                ->count() + 1;
            $appointmentNumber = sprintf("VN-%s-%06d", $year, $nextNum);

            $appointment->update([
                'status'             => Appointment::STATUS_APPROVED,
                'approved_at'        => now(),
                'approved_by'        => auth()->id(),
                'session_key'        => $sessionKey,
                'queue_number'       => $queueNumber,
                'appointment_number' => $appointmentNumber,
            ]);

            $appointment->refresh();

            return back()->with('success', "Approved! Queue: {$sessionKey} #{$queueNumber} | No: {$appointmentNumber}");

        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed. Please contact support.');
        }
    }

    public function reject(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'This appointment cannot be rejected anymore.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $appointment->update([
            'status' => Appointment::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return back()->with('success', 'Appointment rejected.');
    }

    public function assignDoctor(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Only pending appointments can be assigned.');
        }

        $validated = $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
            'doctor_id'         => 'required|exists:doctors,id',
            'date'              => 'required|date|after_or_equal:today',
            'slot'              => 'required|string',
        ]);

        [$startTime, $endTime] = explode('|', $validated['slot']);

        $start = Carbon::parse($validated['date'] . ' ' . $startTime);
        $end   = Carbon::parse($validated['date'] . ' ' . $endTime);

        $appointment->update([
            'doctor_id'         => $validated['doctor_id'],
            'specialization_id' => $validated['specialization_id'],
            'scheduled_start'   => $start,
            'scheduled_end'     => $end,
        ]);

        return back()->with('success', 'Doctor and time slot assigned successfully.');
    }

    private function generateSessionKey(Appointment $appointment): ?string
    {
        if (!$appointment->doctor || !$appointment->scheduled_start) {
            return null;
        }

        $doctor = $appointment->doctor;
        $date = $appointment->scheduled_start;

        $dayShort = strtolower($date->format('D'));

        $sessionOrder = $this->getSessionOrderForTime($appointment);

        if ($sessionOrder === null) {
            return null;
        }

        $lastName = trim(strtolower($doctor->last_name ?? ''));
        $lastName = preg_replace('/[^a-z]+/i', '', $lastName);

        if (empty($lastName)) {
            $lastName = 'doc' . $doctor->id;
        }

        return $lastName . '-' . $dayShort . '-' . str_pad($sessionOrder, 2, '0', STR_PAD_LEFT);
    }

    private function getSessionOrderForTime(Appointment $appointment): ?int
    {
        $apptTime = $appointment->scheduled_start->format('H:i:s');
        $apptDate = $appointment->scheduled_start->startOfDay();
        $apptDay  = $appointment->scheduled_start->englishDayOfWeek;

        $schedules = $appointment->doctor
            ->schedules()
            ->where('is_active', true)
            ->where(function ($q) use ($apptDate) {
                $q->whereDate('valid_from', '<=', $apptDate)
                  ->orWhereNull('valid_from');
            })
            ->where(function ($q) use ($apptDate) {
                $q->whereDate('valid_until', '>=', $apptDate)
                  ->orWhereNull('valid_until');
            })
            ->whereHas('days', function ($q) use ($apptDay) {
                $q->where('day_of_week', $apptDay);
            })
            ->get();

        if ($schedules->isEmpty()) {
            return null;
        }

        $sorted = $schedules->sortBy(function ($s) {
            return $s->start_time->format('H:i:s');
        });

        $index = 0;
        foreach ($sorted as $schedule) {
            $startTime = $schedule->start_time->format('H:i:s');
            $endTime   = $schedule->end_time->format('H:i:s');

            if ($apptTime >= $startTime && $apptTime <= $endTime) {
                return $index + 1;
            }

            $index++;
        }

        return null;
    }

    public function complete(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_APPROVED) {
            return back()->with('error', 'Only approved appointments can be marked as completed.');
        }

        if ($appointment->scheduled_start && $appointment->scheduled_start->isFuture()) {
            return back()->with('warning', 'This appointment is scheduled for the future.');
        }

        DB::transaction(function () use ($request, $appointment) {

            $appointment->update([
                'status'       => Appointment::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);

            if ($request->filled('diagnosis') || $request->filled('medications')) {
                $prescription = $appointment->prescriptions()->create([
                    'patient_id'        => $appointment->patient_id,
                    'doctor_id'         => $appointment->doctor_id,
                    'prescription_date' => now(),
                    'diagnosis'         => $request->input('diagnosis'),
                    'notes'             => $request->input('prescription_notes'),
                ]);

                if ($meds = $request->input('medications', [])) {
                    foreach ($meds as $med) {
                        if (!empty($med['name'])) {
                            $prescription->medications()->create([
                                'name'           => $med['name'],
                                'dosage'         => $med['dosage'] ?? null,
                                'frequency'      => $med['frequency'] ?? null,
                                'duration_days'  => $med['duration'] ?? null,
                                'instructions'   => $med['instructions'] ?? null,
                            ]);
                        }
                    }
                }
            }

            if ($request->filled('total') || $request->filled('services')) {
                $invoice = $appointment->invoice()->create([
                    'patient_id'     => $appointment->patient_id,
                    'invoice_number' => 'INV-' . now()->format('ymd') . '-' . str_pad($appointment->id, 5, '0', STR_PAD_LEFT),
                    'invoice_date'   => now(),
                    'due_date'       => now()->addDays(7),
                    'subtotal'       => $request->input('subtotal', 0),
                    'tax_amount'     => $request->input('tax', 0),
                    'discount_amount'=> $request->input('discount', 0),
                    'total'          => $request->input('total', 0),
                    'status'         => 'sent',
                    'notes'          => $request->input('invoice_notes'),
                ]);

                if ($services = $request->input('services', [])) {
                    foreach ($services as $service) {
                        if (!empty($service['description'])) {
                            $invoice->items()->create([
                                'description' => $service['description'],
                                'quantity'    => $service['quantity'] ?? 1,
                                'unit_price'  => $service['price'] ?? 0,
                                'amount'      => ($service['quantity'] ?? 1) * ($service['price'] ?? 0),
                            ]);
                        }
                    }
                }
            }
        });

        return back()->with('success', 'Appointment completed successfully.');
    }

    public function getDoctorSpecialization(Doctor $doctor)
    {
        $spec = $doctor->primarySpecialization;
        return response()->json($spec ? [
            'id'   => $spec->id,
            'name' => $spec->name
        ] : null);
    }

    public function editTreatments(Appointment $appointment)
    {
        $appointment->load('treatments');

        $treatments = collect();
        if ($appointment->doctor) {
            $treatments = $appointment->doctor
                ->treatments()
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        return view('appointments.treatments.edit', compact('appointment', 'treatments'));
    }

    public function updateTreatments(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'treatment_ids'   => 'nullable|array',
            'treatment_ids.*' => 'exists:treatments,id',
        ]);

        $submittedIds = $request->input('treatment_ids', []);

        $syncData = [];

        if (!empty($submittedIds) && $appointment->doctor) {
            $doctorTreatments = $appointment->doctor
                ->treatments()
                ->whereIn('treatments.id', $submittedIds)
                ->get()
                ->keyBy('id');

            foreach ($submittedIds as $id) {
                if (isset($doctorTreatments[$id])) {
                    $syncData[$id] = [
                        'quantity'      => 1,
                        'price_at_time' => $doctorTreatments[$id]->pivot->price ?? 0,
                        'notes'         => null,
                    ];
                }
            }
        }

        $appointment->treatments()->sync($syncData);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Treatments updated successfully.');
    }

    public function getTreatmentPrice(Treatment $treatment)
    {
        return response()->json([
            'price' => $treatment->price ?? 0,
            'name'  => $treatment->name,
            'code'  => $treatment->code ?? null,
        ]);
    }
}