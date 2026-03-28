<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\DoctorSessionQueue;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\NewAppointmentCreated;
use App\Notifications\AppointmentCompleted;
use App\Notifications\AppointmentApproved;
use App\Notifications\AppointmentAssigned;
use App\Notifications\AppointmentRejected;
use App\Services\NotificationService;
use App\Models\NotificationSetting;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Treatment;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:appointments.index', ['only' => ['index', 'show', 'datatable', 'manager', 'myAppointments', 'calendar', 'calendarEvents']]);
        $this->middleware('permission:appointments.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:appointments.edit', ['only' => ['edit', 'update', 'assignDoctor', 'cancel']]);
        $this->middleware('permission:appointments.delete', ['only' => ['destroy']]);
    }
    public function manager(Request $request)
    {
        $user = auth()->user();
        $isRestrictedDoctor = $user->hasRole('doctor') && !$user->hasRole('admin');

        $queryDoctors = Doctor::active()
            ->with(['primarySpecialization'])
            ->orderByFullName();

        if ($isRestrictedDoctor && $user->doctor) {
            $queryDoctors->where('id', $user->doctor->id);
        }

        $doctors = $queryDoctors->get();

        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        // We fetch ALL appointments for the date because the JS component handles filtering
        // However, if it's a restricted doctor, we only fetch THEIR appointments
        $query = Appointment::with(['patient', 'doctor', 'room', 'prescriptions.medications', 'treatments'])
            ->whereDate('scheduled_start', $selectedDate)
            ->orderBy('scheduled_start');

        if ($isRestrictedDoctor && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        }

        $appointments = $query->get();

        // Format doctors for JS (DOCTORS object)
        $doctorsJs = $doctors->mapWithKeys(function ($doc) {
            /** @var \App\Models\Doctor $doc */
            $colors = ['bg-status-blue', 'bg-status-teal', 'bg-status-purple', 'bg-status-rose', 'bg-status-sky', 'bg-status-amber'];
            $color = $colors[$doc->id % count($colors)];
            return [
                $doc->id => [
                    'name' => $doc->full_name,
                    'spec' => $doc->primarySpecialization?->name ?? 'General Medicine',
                    'spec_id' => $doc->primary_specialization_id,
                    'dotClass' => $color
                ]
            ];
        });

        // Format appointments for JS (appointments array)
        $appointmentsJs = $appointments->map(fn(Appointment $appt) => $this->mapAppointmentForJs($appt));

        $treatments = Treatment::active()->orderBy('name')->get();
        $templates = \App\Models\MedicineTemplate::orderBy('name')->get();
        $inventoryItems = \App\Models\InventoryItem::active()->select('id', 'name', 'generic_name')->orderBy('name')->get();

        // Get all doctor treatment prices for the manager JS
        $doctorTreatmentPrices = DB::table('doctor_treatment')
            ->select('doctor_id', 'treatment_id', 'price')
            ->get()
            ->groupBy('doctor_id')
            ->map(function ($items) {
                return collect($items)->pluck('price', 'treatment_id');
            });

        if ($request->wantsJson()) {
            return response()->json([
                'appointmentsJs' => $appointmentsJs,
                'selectedDate' => $selectedDate
            ]);
        }

        $specializations = Specialization::orderBy('name')->get();
        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $languages = \App\Models\OptionList::getOptions('language');
        $preferredTimeOptions = Appointment::getPreferredTimeOptions();

        return view('appointments.manager', compact(
            'doctors',
            'appointments',
            'selectedDate',
            'doctorsJs',
            'appointmentsJs',
            'treatments',
            'doctorTreatmentPrices',
            'templates',
            'inventoryItems',
            'specializations',
            'isRestrictedDoctor',
            'ageGroups',
            'languages',
            'preferredTimeOptions'
        ))->with([
                    'currentUser' => [
                        'id' => auth()->id(),
                        'roles' => auth()->user()->getRoleNames()->toArray(),
                        'doctor_id' => auth()->user()->doctor?->id
                    ]
                ]);
    }

    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'room'])
            ->orderByDesc('created_at');

        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            $query->where('status', '!=', Appointment::STATUS_PENDING);
        }

        $appointments = $query->paginate(15)
            ->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    public function myAppointments(Request $request)
    {
        return view('appointments.my-appointments');
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
            ->with(['patient', 'doctor', 'room', 'specialization'])
            ->select('appointments.*');

        // Handle "My Appointments" scoping
        if ($request->has('my_appointments')) {
            $user = auth()->user();
            $doctor = $user->doctor;

            if (!$doctor) {
                $doctor = Doctor::where('email', $user->email)->active()->first();
            }

            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            } else {
                // If the user is not a doctor or no profile found, they should see no appointments in "My Appointments"
                $query->whereRaw('1=0');
            }
        }

        // Restriction: Only admin, doctor, and primary care provider can see pending appointments
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            $query->where('status', '!=', Appointment::STATUS_PENDING);
        }

        if ($doctorFilter) {
            $query->where('doctor_id', $doctorFilter);
        }

        $totalRecords = (clone $query)->count();

        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('patient', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                    ->orWhereHas('doctor', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                    ->orWhere('reason_for_visit', 'like', "%{$searchValue}%")
                    ->orWhere('appointment_type', 'like', "%{$searchValue}%");
            });
        }

        if ($statusFilter) {
            $statuses = is_array($statusFilter) ? $statusFilter : [$statusFilter];
            $query->whereIn('status', $statuses);
        }

        $filteredRecords = (clone $query)->count();

        $columns = ['id', 'appointment_number', 'patient_name', 'doctor_name', 'scheduled_start', 'status', 'actions'];

        if (isset($columns[$orderIdx])) {
            switch ($columns[$orderIdx]) {
                case 'id':
                    $query->orderBy('id', $orderDir);
                    break;
                case 'appointment_number':
                    $query->orderBy('appointment_number', $orderDir);
                    break;
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
                case 'status':
                    $query->orderBy('status', $orderDir);
                    break;
                default:
                    $query->orderByDesc('id');
            }
        } else {
            $query->orderByDesc('id');
        }

        $appointments = $query->offset($start)->limit($length)->get();

        $data = $appointments->map(function ($appt) {
            $scheduledDisplay = 'Not set';
            $scheduledClass = 'text-gray-500 dark:text-gray-400 italic';

            if ($appt->scheduled_start) {
                $start = Carbon::parse($appt->scheduled_start);
                $timeDisplay = $start->format('h:i A');

                if ($appt->scheduled_end) {
                    $end = Carbon::parse($appt->scheduled_end);
                    if ($end->greaterThan($start)) {
                        $timeDisplay .= ' – ' . $end->format('h:i A');
                    }
                }

                $duration = $appt->duration_minutes ?? ($appt->scheduled_end ? $start->diffInMinutes(Carbon::parse($appt->scheduled_end)) : null);
                $scheduledDisplay = $start->format('M d, Y') . '<br><span class="text-xs font-semibold text-indigo-500">Time: ' . $start->format('h:i A') . '</span>';
                if ($duration) {
                    $scheduledDisplay .= '<br><span class="text-xs font-medium text-indigo-600">Duration: ' . $duration . ' min</span>';
                }
                if ($appt->room) {
                    $scheduledDisplay .= '<br><span class="text-xs font-medium text-indigo-600">Room: ' . $appt->room->name . ' (' . $appt->room->room_number . ')</span>';
                }

                $scheduledClass = '';
            }

            $statusBadge = match ($appt->status) {
                Appointment::STATUS_PENDING => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                Appointment::STATUS_APPROVED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>',
                Appointment::STATUS_REJECTED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Rejected</span>',
                Appointment::STATUS_CANCELLED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>',
                Appointment::STATUS_COMPLETED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Completed</span>',
                Appointment::STATUS_PAID => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Paid</span>',
                Appointment::STATUS_RUNNING => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Running</span>',
                default => '<span class="text-gray-500">-</span>'
            };

            $canEdit = auth()->user()->can('appointments.edit');

            return [
                'id' => $appt->id,
                'appointment_number' => $appt->appointment_number ?? '—',
                'patient_name' => $appt->patient?->getFullNameAttribute() ?? '-',
                'doctor_name' => $appt->doctor
                    ? $appt->doctor->getFullNameAttribute()
                    : (__('file.any_therapist') ?? 'First Time Appointment') . ($appt->specialization ? ' — ' . $appt->specialization->name : ''),
                'appointment_type' => $appt->appointment_type,
                'specialization_name' => $appt->specialization?->name ?? null,
                'scheduled_datetime' => $scheduledDisplay,
                'room_number' => $appt->room?->room_number ?? '—',
                'scheduled_class' => $scheduledClass,
                'status' => $appt->status,
                'status_badge' => $statusBadge,
                'queue_info' => $appt->status === Appointment::STATUS_APPROVED && $appt->queue_number
                    ? strtoupper($appt->session_key) . ' #' . $appt->queue_number
                    : '-',
                'show_url' => route('appointments.show', $appt),
                'edit_url' => $canEdit ? route('appointments.edit', $appt) : null,
                'delete_url' => auth()->user()->can('appointments.delete') ? route('appointments.destroy', $appt) : null,
                'cancel_url' => ($canEdit && $appt->status === Appointment::STATUS_PENDING) ? route('appointments.cancel', $appt) : null,
            ];

        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        $specializations = Specialization::orderBy('name')->get();
        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $languages = \App\Models\OptionList::getOptions('language');
        $rooms = \App\Models\Room::active()->get();

        $preferredTimeOptions = Appointment::getPreferredTimeOptions();
        return view('appointments.create', compact('patients', 'specializations', 'ageGroups', 'languages', 'preferredTimeOptions', 'rooms'));
    }

    public function store(Request $request)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'appointment_type' => [
                'required',
                Rule::in([
                    Appointment::TYPE_SPECIFIC,
                    Appointment::TYPE_ANY
                ])
            ],
            'reason_for_visit' => 'required|string|max:1000',
            'patient_notes' => 'nullable|string|max:2000',
            'scheduled_start' => 'nullable|date|after_or_equal:today',
            'date' => 'required_without:scheduled_start|date|after_or_equal:today',
            'appointment_time' => 'required_without:scheduled_start|date_format:H:i',
            'room_id' => 'nullable|exists:rooms,id',
            'duration_minutes' => 'nullable|integer|in:15,30,45,60',
        ];

        if ($request->input('appointment_type') === Appointment::TYPE_SPECIFIC) {
            $rules['doctor_id'] = 'required|exists:doctors,id';
            $rules['slot'] = 'nullable|string';
        } elseif ($request->input('appointment_type') === Appointment::TYPE_ANY) {
            $rules['specialization_id'] = 'required|exists:specializations,id';
        }

        $validated = $request->validate($rules);

        $start = null;
        $end = null;

        if (!empty($validated['date']) && !empty($validated['appointment_time'])) {
            $start = Carbon::parse("{$validated['date']} {$validated['appointment_time']}");

            $duration = (int) $request->input('duration_minutes', 15);
            $end = $start->copy()->addMinutes($duration);

            if ($request->input('appointment_type') === Appointment::TYPE_SPECIFIC && !empty($validated['slot'])) {
                [$startTime, $endTime] = explode('|', $validated['slot']);
                $slotStart = Carbon::parse("{$validated['date']} {$startTime}");
                $slotEnd = Carbon::parse("{$validated['date']} {$endTime}");

                if ($start->lt($slotStart) || $start->gt($slotEnd)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => __('file.appointment_time_out_of_slot') ?? 'Appointment time must be within the selected slot.'], 422);
                    }
                    return back()->withErrors(['appointment_time' => __('file.appointment_time_out_of_slot') ?? 'Appointment time must be within the selected slot.'])->withInput();
                }

                if ($end->gt($slotEnd)) {
                    $msg = __('file.appointment_duration_exceeds_slot') ?? 'The appointment duration exceeds the end of the selected time slot.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withErrors(['duration_minutes' => $msg])->withInput();
                }
            }
        } elseif (!empty($validated['scheduled_start'])) {
            $start = Carbon::parse($validated['scheduled_start']);
            $duration = (int) $request->input('duration_minutes', 15);
            $end = $start->copy()->addMinutes($duration);
        }

        // Room conflict check
        if (!empty($validated['room_id']) && $start && $end) {
            $busyRooms = $this->getBusyRooms($start, $end);
            if (in_array($validated['room_id'], $busyRooms)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.'], 422);
                }
                return back()->withErrors(['room_id' => __('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.'])->withInput();
            }
        }

        if ($start) {
            if (\App\Models\Holiday::isHoliday($start->format('Y-m-d'))) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('file.cannot_schedule_on_holiday') ?? 'Cannot schedule appointment on a holiday.'], 422);
                }
                $errorField = (!empty($validated['date']) && !empty($validated['slot'])) ? 'date' : 'scheduled_start';
                return back()->withErrors([$errorField => __('file.cannot_schedule_on_holiday') ?? 'Cannot schedule appointment on a holiday.'])->withInput();
            }
        }

        $isFirstTime = !Appointment::where('patient_id', $validated['patient_id'])
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->where('status', '!=', Appointment::STATUS_REJECTED)
            ->exists();

        $status = Appointment::STATUS_PENDING;
        if ($validated['appointment_type'] === Appointment::TYPE_SPECIFIC && !$isFirstTime) {
            $status = Appointment::STATUS_APPROVED;
        }

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'appointment_type' => $validated['appointment_type'],
            'reason_for_visit' => $validated['reason_for_visit'],
            'patient_notes' => $validated['patient_notes'] ?? null,
            'status' => $status,
            'specialization_id' => $validated['specialization_id'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? 15,
        ]);

        if ($start && $end) {
            $roomId = $validated['room_id'] ?? null;
            if (!$roomId && isset($validated['doctor_id'])) {
                $roomId = $this->getRoomIdForAppointment($validated['doctor_id'], $start);
            }

            $appointment->update([
                'scheduled_start' => $start,
                'scheduled_end' => $end,
                'room_id' => $roomId,
            ]);

            if ($status === Appointment::STATUS_APPROVED) {
                if (!$this->processAppointmentApproval($appointment)) {
                    // Fallback if approval fails (e.g. session key generation issues)
                    $appointment->update(['status' => Appointment::STATUS_PENDING]);
                }
            }
        }

        $recipients = NotificationSetting::getRecipients('appointment_created');
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewAppointmentCreated($appointment));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.appointment_created_successfully'),
                'appointment' => $appointment->load(['patient', 'doctor', 'specialization'])
            ]);
        }

        $returnTo = $request->input('return_to');
        $redirectRoute = 'appointments.index';

        if ($returnTo === 'my_appointments') {
            $redirectRoute = 'doctor-panel.my-appointments';
        } elseif ($returnTo === 'calendar') {
            $redirectRoute = 'appointments.calendar';
        }

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('file.appointment_created_successfully'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient' => fn($q) => $q->select([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'phone',
                'email',
                'date_of_birth',
                'gender',
                'medical_record_number'
            ]),
            'doctor' => fn($q) => $q->select([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'primary_specialization_id'
            ])->with('primarySpecialization:id,name'),
            'room:id,name,room_number',
            'canceller:id,name',
            'specialization:id,name',
            'ageGroup:id,name',
            'preferredLanguage:id,name',
            'treatments' => fn($q) => $q->select('treatments.id', 'treatments.name', 'treatments.code'),
        ]);

        $treatments = collect();
        if ($appointment->doctor) {
            $treatments = $appointment->doctor
                ->treatments()
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        // Fetch the doctor's schedule slot for the appointment date
        $doctorSlot = null;
        if ($appointment->doctor && $appointment->scheduled_start) {
            $apptDate = $appointment->scheduled_start->copy();
            $dayName = strtolower($apptDate->format('l'));

            $schedule = $appointment->doctor->schedules()
                ->where('is_active', true)
                ->where(function ($q) use ($apptDate) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', $apptDate);
                })
                ->where(function ($q) use ($apptDate) {
                    $q->whereNull('valid_until')->orWhere('valid_until', '>=', $apptDate);
                })
                ->whereHas('days', fn($q) => $q->where('day_of_week', $dayName))
                ->with(['days' => fn($q) => $q->where('day_of_week', $dayName)])
                ->first();

            if ($schedule) {
                $daySchedule = $schedule->days->first();
                if ($daySchedule && $daySchedule->start_time && $daySchedule->end_time) {
                    $doctorSlot = [
                        'start' => $apptDate->copy()->setTimeFromTimeString($daySchedule->start_time->format('H:i')),
                        'end' => $apptDate->copy()->setTimeFromTimeString($daySchedule->end_time->format('H:i')),
                    ];
                }
            }
        }

        $specializations = Specialization::orderBy('name')->get();
        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $currency_code = config('app.currency', 'USD');

        return view('appointments.show', compact(
            'appointment',
            'specializations',
            'ageGroups',
            'treatments',
            'currency_code',
            'doctorSlot'
        ));
    }

    public function edit(Appointment $appointment)
    {
        $specializations = Specialization::orderBy('name')->get();
        $treatments = collect();
        $ageGroups = \App\Models\AgeGroup::orderBy('name')->get();
        $languages = \App\Models\OptionList::getOptions('language');

        if ($appointment->doctor) {
            $treatments = $appointment->doctor
                ->treatments()
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        $appointment->load(['ageGroup', 'preferredLanguage']);

        $preferredTimeOptions = Appointment::getPreferredTimeOptions();
        return view('appointments.edit', compact(
            'appointment',
            'specializations',
            'treatments',
            'ageGroups',
            'languages',
            'preferredTimeOptions'
        ));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'date' => 'nullable|date',
            'slot' => 'nullable|string',
            'appointment_time' => 'nullable|date_format:H:i',
            'appointment_type' => [
                'required',
                Rule::in([
                    Appointment::TYPE_SPECIFIC,
                    Appointment::TYPE_ANY
                ])
            ],
            'reason_for_visit' => 'nullable|string|max:1000',
            'patient_notes' => 'nullable|string|max:2000',
            'admin_notes' => 'nullable|string|max:2000',
            'doctor_notes' => 'nullable|string|max:2000',
            'status' => [
                'sometimes',
                Rule::in([
                    Appointment::STATUS_PENDING,
                    Appointment::STATUS_APPROVED,
                    Appointment::STATUS_REJECTED,
                    Appointment::STATUS_CANCELLED,
                    Appointment::STATUS_COMPLETED,
                    Appointment::STATUS_PAID,
                    Appointment::STATUS_RUNNING
                ])
            ],
            'treatment_ids' => 'nullable|array',
            'treatment_ids.*' => 'exists:treatments,id',
            'room_id' => 'nullable|exists:rooms,id',
            'duration_minutes' => 'nullable|integer|in:15,30,45,60',
        ];

        $status = $request->input('status', $appointment->status);
        $isApproved = $status === Appointment::STATUS_APPROVED;

        if ($isApproved) {
            $rules['specialization_id'] = 'required|exists:specializations,id';
            $rules['doctor_id'] = 'required|exists:doctors,id';
            $rules['room_id'] = 'required|exists:rooms,id';
            $rules['appointment_type'] = 'sometimes|string';
        } else {
            if ($request->input('appointment_type') === Appointment::TYPE_SPECIFIC) {
                $rules['doctor_id'] = 'required|exists:doctors,id';
                $rules['specialization_id'] = 'nullable|exists:specializations,id';
            } else {
                $rules['specialization_id'] = 'required|exists:specializations,id';
                $rules['doctor_id'] = 'nullable|exists:doctors,id';
            }
        }

        $validated = $request->validate($rules);

        $start = $appointment->scheduled_start;
        $end = $appointment->scheduled_end;

        $providedDate = $validated['date'] ?? null;
        $providedTime = $validated['appointment_time'] ?? null;
        $providedSlot = $validated['slot'] ?? null;
        $type = $validated['appointment_type'] ?? $appointment->appointment_type;

        $duration = (int) $request->input('duration_minutes', $appointment->duration_minutes ?? 15);

        if (!empty($providedDate) && !empty($providedTime)) {
            $start = Carbon::parse("{$providedDate} {$providedTime}");
            $end = $start->copy()->addMinutes($duration);

            if ($type === Appointment::TYPE_SPECIFIC && !empty($providedSlot) && str_contains($providedSlot, '|')) {
                [$startTime, $endTime] = explode('|', $providedSlot);
                $slotStart = Carbon::parse("{$providedDate} {$startTime}");
                $slotEnd = Carbon::parse("{$providedDate} {$endTime}");

                if ($start->lt($slotStart) || $start->gt($slotEnd)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => __('file.appointment_time_out_of_slot') ?? 'Appointment time must be within the selected slot.'], 422);
                    }
                    return back()->withErrors(['appointment_time' => __('file.appointment_time_out_of_slot') ?? 'Appointment time must be within the selected slot.'])->withInput();
                }

                if ($end->gt($slotEnd)) {
                    $msg = __('file.appointment_duration_exceeds_slot') ?? 'The appointment duration exceeds the end of the selected time slot.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withErrors(['duration_minutes' => $msg])->withInput();
                }
            }
        } elseif ($start) {
            // Recalculate end if start exists but only duration was changed
            $end = $start->copy()->addMinutes($duration);
        }

        $checkDate = $start ? $start->format('Y-m-d') : null;
        if ($checkDate && \App\Models\Holiday::isHoliday($checkDate)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_schedule_on_holiday') ?? 'Cannot assign/approve appointment on a holiday.'], 422);
            }
            return back()->withErrors(['date' => __('file.cannot_schedule_on_holiday') ?? 'Cannot assign/approve appointment on a holiday.'])->withInput();
        }

        $oldStatus = $appointment->status;

        $roomId = $validated['room_id'] ?? $appointment->room_id;

        if ($roomId && $start && $end) {
            $busyRooms = $this->getBusyRooms($start, $end, $appointment->id);
            if (in_array($roomId, $busyRooms)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.'], 422);
                }
                return back()->withErrors(['room_id' => __('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.'])->withInput();
            }
        }

        $appointment->update([
            'specialization_id' => $validated['specialization_id'],
            'doctor_id' => $validated['doctor_id'],
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'room_id' => $roomId,
            'appointment_type' => $validated['appointment_type'] ?? $appointment->appointment_type,
            'reason_for_visit' => $validated['reason_for_visit'] ?? $appointment->reason_for_visit,
            'patient_notes' => $validated['patient_notes'] ?? $appointment->patient_notes,
            'admin_notes' => $validated['admin_notes'] ?? $appointment->admin_notes,
            'doctor_notes' => $validated['doctor_notes'] ?? $appointment->doctor_notes,
            'duration_minutes' => $duration,
            'status' => $validated['status'] ?? $appointment->status,
        ]);

        // Notifications for status/doctor changes
        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            if ($validated['status'] === Appointment::STATUS_APPROVED) {
                NotificationService::send('appointment_approved', new AppointmentApproved($appointment), array_filter([$appointment->doctor?->user]));
            } elseif ($validated['status'] === Appointment::STATUS_REJECTED) {
                NotificationService::send('appointment_rejected', new AppointmentRejected($appointment));
            } elseif ($validated['status'] === Appointment::STATUS_COMPLETED) {
                NotificationService::send('appointment_completed', new AppointmentCompleted($appointment), array_filter([$appointment->doctor?->user]));
            }
        }

        if (isset($validated['doctor_id']) && $validated['doctor_id'] != $appointment->getOriginal('doctor_id')) {
            NotificationService::send('appointment_assigned', new AppointmentAssigned($appointment), array_filter([$appointment->doctor?->user]));
        }

        if ($request->has('treatment_ids')) {
            $user = auth()->user();
            $doctor = $user->doctor;

            $canUpdateTreatments = true;
            if ($appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
                $canUpdateTreatments = false;
            }

            if ($canUpdateTreatments) {
                $treatmentIds = $request->input('treatment_ids', []);

                $syncData = [];

                if (!empty($treatmentIds) && $appointment->doctor) {
                    $doctorTreatments = $appointment->doctor
                        ->treatments()
                        ->whereIn('treatments.id', $treatmentIds)
                        ->get()
                        ->keyBy('id');

                    foreach ($treatmentIds as $id) {
                        if (isset($doctorTreatments[$id])) {
                            $syncData[$id] = [
                                'quantity' => 1,
                                'price_at_time' => $doctorTreatments[$id]->pivot->price ?? $doctorTreatments[$id]->price ?? 0,
                                'notes' => null,
                            ];
                        }
                    }
                }

                $appointment->treatments()->sync($syncData);
            }
        }

        if (isset($validated['status']) && $validated['status'] === Appointment::STATUS_APPROVED && $oldStatus !== Appointment::STATUS_APPROVED) {
            if (!$this->processAppointmentApproval($appointment)) {
                $appointment->update(['status' => $oldStatus]);
                return back()->withErrors(['status' => 'Approval process failed (check doctor schedule/time)'])->withInput();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.appointment_updated_successfully'),
                'appointment' => $this->mapAppointmentForJs($appointment->load(['patient', 'doctor', 'specialization', 'room', 'treatments', 'prescriptions']))
            ]);
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', __('file.appointment_updated_successfully'));
    }

    public function getTreatments(Doctor $doctor)
    {
        $treatments = $doctor->treatments()
            ->where('treatments.active', true)
            ->orderBy('treatments.name')
            ->select(
                'treatments.id',
                'treatments.name',
                'treatments.code',
                'doctor_treatment.price as price'
            )
            ->get();

        return response()->json(['treatments' => $treatments]);
    }


    public function cancel(Appointment $appointment)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            abort(403);
        }

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', __('file.only_pending_cancelled'));
        }

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        return back()->with('success', __('file.appointment_cancelled_successfully'));
    }

    public function calendar(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $doctors = Doctor::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('appointments.calendar', compact('doctors', 'doctorId'));
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $doctorId = $request->query('doctor_id');

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $query = Appointment::with(['patient', 'doctor'])
            ->where('scheduled_start', '<', $endDate)
            ->where('scheduled_end', '>', $startDate);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            $query->where('status', '!=', Appointment::STATUS_PENDING);
        }

        $query->whereIn('status', [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_APPROVED,
        ]);

        $appointments = $query->get();

        $events = $appointments->map(function ($apt) {
            $start = Carbon::parse($apt->scheduled_start);
            $end = Carbon::parse($apt->scheduled_end);
            $duration = $start->diffInMinutes($end);

            $doctor = $apt->doctor?->getFullNameAttribute() ?? 'Unknown Doctor';
            $patient = $apt->patient?->getFullNameAttribute() ?? 'Unknown Patient';

            $color = match ($apt->status) {
                Appointment::STATUS_APPROVED => '#10b981',
                Appointment::STATUS_CANCELLED => '#ef4444',
                Appointment::STATUS_REJECTED => '#f59e0b',
                default => '#3b82f6',
            };

            return [
                'id' => $apt->id,
                'title' => $patient . ' - ' . $doctor,
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
                'url' => route('appointments.show', $apt),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'patient' => $patient,
                    'doctor' => $doctor,
                    'duration' => $duration . ' min',
                    'status' => ucfirst(str_replace('_', ' ', $apt->status)),
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
            'text' => $d->getFullNameAttribute(),
        ]));
    }

    public function getSpecializationAvailability(Request $request)
    {
        $specId = $request->query('specialization_id');
        $dateStr = $request->query('date');

        if (!$specId || !$dateStr) {
            return response()->json(['doctors' => []]);
        }

        $date = Carbon::parse($dateStr);
        $dayName = strtolower($date->format('l'));

        $doctors = Doctor::active()
            ->where(function ($q) use ($specId) {
                $q->where('primary_specialization_id', $specId)
                    ->orWhereHas('specializations', function ($sq) use ($specId) {
                        $sq->where('specializations.id', $specId);
                    });
            })
            ->get();

        $results = [];
        foreach ($doctors as $doctor) {
            $slots = [];

            // Check leave
            $employee = \App\Models\Employee::where('user_id', $doctor->user_id)->first();
            $isOnLeave = false;
            if ($employee) {
                $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->exists();
            }

            if (!$isOnLeave && !\App\Models\Holiday::isHoliday($date->format('Y-m-d'))) {
                $schedules = $doctor->schedules()
                    ->where('is_active', true)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
                    })
                    ->where(function ($q) use ($date) {
                        $q->whereNull('valid_until')->orWhere('valid_until', '>=', $date);
                    })
                    ->with([
                        'days' => function ($q) use ($dayName) {
                            $q->where('day_of_week', $dayName);
                        }
                    ])
                    ->get();

                foreach ($schedules as $schedule) {
                    $daySchedule = $schedule->days->first();
                    if (!$daySchedule || !$daySchedule->start_time || !$daySchedule->end_time)
                        continue;

                    $start = $date->copy()->setTimeFromTimeString($daySchedule->start_time->format('H:i'));
                    $end = $date->copy()->setTimeFromTimeString($daySchedule->end_time->format('H:i'));

                    $slots[] = [
                        'start' => $start->format('H:i'),
                        'end' => $end->format('H:i'),
                        'label' => $start->format('g:i A') . ' - ' . $end->format('g:i A')
                    ];
                }
                usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));
            }

            $results[] = [
                'id' => $doctor->id,
                'name' => 'Dr. ' . $doctor->full_name,
                'slots' => $slots,
                'on_leave' => $isOnLeave
            ];
        }

        return response()->json([
            'doctors' => $results,
            'day_name' => $date->translatedFormat('l'),
            'date_label' => $date->translatedFormat('d M Y')
        ]);
    }

    public function getAllDoctors()
    {
        $doctors = Doctor::active()
            ->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name']);

        return response()->json($doctors->map(fn($d) => [
            'value' => $d->id,
            'text' => $d->getFullNameAttribute(),
        ]));
    }

    public function getFilteredDoctors(Request $request)
    {
        $specializationId = $request->query('specialization_id');
        $ageGroupId = $request->query('age_group_id');
        $languageId = $request->query('preferred_language_id');
        $date = $request->query('date');

        $query = Doctor::active();

        if ($specializationId) {
            $query->where(function ($q) use ($specializationId) {
                $q->where('primary_specialization_id', $specializationId)
                    ->orWhereHas('specializations', function ($sq) use ($specializationId) {
                        $sq->where('specializations.id', $specializationId);
                    });
            });
        }

        if ($ageGroupId) {
            $query->whereHas('ageGroups', function ($q) use ($ageGroupId) {
                $q->where('age_groups.id', $ageGroupId);
            });
        }

        if ($languageId) {
            $query->whereHas('languages', function ($q) use ($languageId) {
                $q->where('option_lists.id', $languageId);
            });
        }

        // We don't filter the doctor list by date/schedule here anymore to ensure
        // therapists load even if their specific availability isn't yet active for the selected date.
        // The modal's 'available days' buttons will handle the next step of selection.

        $doctors = $query->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name']);

        return response()->json($doctors->map(fn($d) => [
            'value' => $d->id,
            'text' => $d->getFullNameAttribute(),
        ]));
    }

    public function getDoctorAttributes(Doctor $doctor)
    {
        return response()->json([
            'age_groups' => $doctor->ageGroups->pluck('id'),
            'languages' => $doctor->languages->pluck('id'),
            'specialization_id' => $doctor->primary_specialization_id,
        ]);
    }

    public function availableDays(Doctor $doctor)
    {
        $employee = \App\Models\Employee::where('user_id', $doctor->user_id)->first();
        $leaves = [];
        if ($employee) {
            $leaves = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('end_date', '>=', Carbon::today())
                ->get();
        }

        $schedules = $doctor->schedules()
            ->where('is_active', true)
            ->with('days')
            ->get();

        $availableDays = [];
        $currentDate = Carbon::today();
        $daysFound = 0;

        for ($i = 0; $i < 60; $i++) {
            $date = $currentDate->copy()->addDays($i);
            $dayName = strtolower($date->format('l'));

            $isOnLeave = false;
            foreach ($leaves as $leave) {
                if ($date->between(Carbon::parse($leave->start_date)->startOfDay(), Carbon::parse($leave->end_date)->endOfDay())) {
                    $isOnLeave = true;
                    break;
                }
            }

            if ($isOnLeave || \App\Models\Holiday::isHoliday($date->format('Y-m-d'))) {
                continue;
            }

            $hasSchedule = false;
            foreach ($schedules as $schedule) {
                if ($schedule->valid_from && $date->lt(Carbon::parse($schedule->valid_from)->startOfDay()))
                    continue;
                if ($schedule->valid_until && $date->gt(Carbon::parse($schedule->valid_until)->endOfDay()))
                    continue;

                $dayRecord = $schedule->days->where('day_of_week', $dayName)->first();
                if ($dayRecord && $dayRecord->start_time && $dayRecord->end_time) {
                    $hasSchedule = true;
                    break;
                }
            }

            if ($hasSchedule) {
                $availableDays[] = [
                    'date' => $date->format('Y-m-d'),
                    'label' => $date->isToday() ? __('file.today') . ' (' . $date->translatedFormat('d M') . ')' : $date->translatedFormat('D, d M')
                ];
                $daysFound++;
                if ($daysFound >= 5)
                    break;
            }
        }

        return response()->json([
            'days' => $availableDays
        ]);
    }

    public function checkAvailability(Doctor $doctor, Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['status' => 'available']);
        }

        $carbonDate = Carbon::parse($date);
        $dateString = $carbonDate->format('Y-m-d');

        // Check if it's a holiday
        if (\App\Models\Holiday::isHoliday($dateString)) {
            return response()->json([
                'status' => 'holiday',
                'message' => __('file.cannot_schedule_on_holiday') ?? 'The selected date is a holiday.'
            ]);
        }

        // Check if the doctor is on leave
        $employee = \App\Models\Employee::where('user_id', $doctor->user_id)->first();
        if ($employee) {
            $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $dateString)
                ->where('end_date', '>=', $dateString)
                ->exists();

            if ($isOnLeave) {
                return response()->json([
                    'status' => 'leave',
                    'message' => __('file.doctor_on_leave') ?? 'The doctor is on leave on the selected date.'
                ]);
            }
        }

        return response()->json(['status' => 'available']);
    }

    public function availableSlots(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $dayName = strtolower($date->format('l'));

        $employee = \App\Models\Employee::where('user_id', $doctor->user_id)->first();
        if ($employee) {
            $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->exists();

            if ($isOnLeave) {
                return response()->json([
                    'slots' => [],
                    'message' => __('file.doctor_on_leave')
                ]);
            }
        }

        if (\App\Models\Holiday::isHoliday($date->format('Y-m-d'))) {
            return response()->json([
                'slots' => [],
                'message' => __('file.cannot_schedule_on_holiday') ?? 'Date falls on a holiday.'
            ]);
        }

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
            $daySchedule = $schedule->days->where('day_of_week', $dayName)->first();
            if (!$daySchedule || !$daySchedule->start_time || !$daySchedule->end_time)
                continue;

            $start = $date->copy()->setTimeFromTimeString($daySchedule->start_time->format('H:i'));
            $end = $date->copy()->setTimeFromTimeString($daySchedule->end_time->format('H:i'));

            $slots[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'label' => $start->format('g:i A') . ' - ' . $end->format('g:i A')
            ];
        }

        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));

        $appointments = \App\Models\Appointment::with([])->where('doctor_id', $doctor->id)
            ->whereDate('scheduled_start', $date)
            ->whereIn('status', [\App\Models\Appointment::STATUS_APPROVED, \App\Models\Appointment::STATUS_RUNNING, \App\Models\Appointment::STATUS_COMPLETED])
            ->get(['scheduled_start', 'scheduled_end', 'status', 'duration_minutes']);

        return response()->json([
            'slots' => $slots,
            'appointments' => $appointments->map(fn($a) => [
                'start' => $a->scheduled_start->format('H:i'),
                'end' => $a->duration_minutes 
                    ? $a->scheduled_start->copy()->addMinutes($a->duration_minutes)->format('H:i')
                    : ($a->scheduled_end ? $a->scheduled_end->format('H:i') : null),
                'status' => $a->status,
            ]),
            'message' => $slots ? null : 'No schedule found for this date'
        ]);
    }

    public function assignDoctor(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Only pending appointments can be assigned.');
        }

        $validated = $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        [$startTime, $endTime] = explode('|', $validated['slot']);

        $durationMinutes = (int) $request->input('duration_minutes', $appointment->duration_minutes ?? 15);
        $start = Carbon::parse("{$validated['date']} {$startTime}");
        $slotEnd = Carbon::parse("{$validated['date']} {$endTime}");
        $end = $start->copy()->addMinutes($durationMinutes);

        if ($end->gt($slotEnd)) {
            return back()->withErrors(['duration_minutes' => __('file.appointment_duration_exceeds_slot') ?? 'The appointment duration exceeds the end of the selected time slot.'])->withInput();
        }

        $roomId = $validated['room_id'] ?? $this->getRoomIdForAppointment($validated['doctor_id'], $start);

        if ($roomId) {
            $busyRooms = $this->getBusyRooms($start, $end, $appointment->id);
            if (in_array($roomId, $busyRooms)) {
                return back()->with('error', __('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.');
            }
        }

        if (\App\Models\Holiday::isHoliday($start->format('Y-m-d'))) {
            return back()->with('error', __('file.cannot_schedule_on_holiday') ?? 'Cannot assign appointment on a holiday.');
        }

        $employee = \App\Models\Employee::where('user_id', $appointment->doctor->user_id ?? \App\Models\Doctor::find($validated['doctor_id'])->user_id)->first();
        if ($employee) {
            $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $start)
                ->where('end_date', '>=', $start)
                ->exists();

            if ($isOnLeave) {
                return back()->with('error', __('file.doctor_on_leave') ?? 'Doctor is on leave on this date.');
            }
        }

        $appointment->update([
            'doctor_id' => $validated['doctor_id'],
            'specialization_id' => $validated['specialization_id'],
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'duration_minutes' => $durationMinutes,
            'room_id' => $roomId,
        ]);

        NotificationService::send('appointment_assigned', new AppointmentAssigned($appointment), array_filter([$appointment->doctor?->user]));

        return redirect()->route('appointments.index')->with('success', __('file.doctor_time_slot_assigned'));
    }

    public function assignAndApprove(Request $request, Appointment $appointment)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            abort(403);
        }

        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Only pending appointments can be assigned and approved.');
        }

        $validated = $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'slot' => 'required|string',
            'age_group_id' => 'nullable|exists:age_groups,id',
            'room_id' => 'nullable|exists:rooms,id',
            'appointment_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|in:15,30,45,60',
        ]);

        $durationMinutes = (int) ($validated['duration_minutes'] ?? $appointment->duration_minutes ?? 15);

        // Use the specific appointment time provided, or fall back to slot start if somehow missing
        $startTime = $validated['appointment_time'] ?? explode('|', $validated['slot'])[0];
        $slotEnd = Carbon::parse("{$validated['date']} " . explode('|', $validated['slot'])[1]);
        $start = Carbon::parse("{$validated['date']} {$startTime}");
        $end = $start->copy()->addMinutes($durationMinutes);

        if ($end->gt($slotEnd)) {
            return back()->with('error', __('file.appointment_duration_exceeds_slot') ?? 'The appointment duration exceeds the end of the selected time slot.');
        }

        if (\App\Models\Holiday::isHoliday($start->format('Y-m-d'))) {
            return back()->with('error', __('file.cannot_schedule_on_holiday') ?? 'Cannot assign and approve appointment on a holiday.');
        }

        $employee = \App\Models\Employee::where('user_id', $appointment->doctor->user_id ?? \App\Models\Doctor::find($validated['doctor_id'])->user_id)->first();
        if ($employee) {
            $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $start)
                ->where('end_date', '>=', $start)
                ->exists();

            if ($isOnLeave) {
                return back()->with('error', __('file.doctor_on_leave') ?? 'Doctor is on leave on this date.');
            }
        }

        DB::beginTransaction();
        try {
            $roomId = $request->filled('room_id') ? $validated['room_id'] : $this->getRoomIdForAppointment($validated['doctor_id'], $start);

            if ($roomId) {
                $busyRooms = $this->getBusyRooms($start, $end, $appointment->id);
                if (in_array($roomId, $busyRooms)) {
                    throw new \Exception(__('file.room_occupied_at_this_time') ?? 'Selected room is already occupied during this time.');
                }
            }

            $updateData = [
                'doctor_id' => $validated['doctor_id'],
                'specialization_id' => $validated['specialization_id'],
                'scheduled_start' => $start,
                'scheduled_end' => $end,
                'room_id' => $roomId,
                'duration_minutes' => $durationMinutes,
            ];

            if (isset($validated['age_group_id'])) {
                $updateData['age_group_id'] = $validated['age_group_id'];
            }

            $appointment->update($updateData);

            // Reload doctor + schedules — the relation is stale after update() when no doctor was assigned before
            $appointment->load(['doctor.schedules.days']);

            $date = $appointment->scheduled_start->startOfDay();
            $sessionKey = $this->generateSessionKey($appointment);

            if (!$sessionKey) {
                throw new \Exception('Cannot determine session time.');
            }

            $queue = DoctorSessionQueue::lockForUpdate()
                ->firstOrCreate(
                    [
                        'doctor_id' => $appointment->doctor_id,
                        'queue_date' => $date,
                        'session_key' => $sessionKey,
                    ],
                    ['last_number' => 0]
                );

            $queueNumber = $queue->last_number + 1;
            $queue->update(['last_number' => $queueNumber]);

            $appointmentNumber = Appointment::generateNextAppointmentNumber();

            $appointment->update([
                'status' => Appointment::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'session_key' => $sessionKey,
                'queue_number' => $queueNumber,
                'appointment_number' => $appointmentNumber,
            ]);

            DB::commit();

            NotificationService::send('appointment_assigned', new AppointmentAssigned($appointment), array_filter([$appointment->doctor?->user]));
            NotificationService::send('appointment_approved', new AppointmentApproved($appointment), array_filter([$appointment->doctor?->user]));

            return redirect()->route('appointments.index')->with('success', __('file.appointment_assigned_and_approved'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: __('file.appointment_approval_failed'));
        }
    }

    public function approve(Appointment $appointment)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            abort(403);
        }

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
                            'doctor_id' => $appointment->doctor_id,
                            'queue_date' => $date,
                            'session_key' => $sessionKey,
                        ],
                        ['last_number' => 0]
                    );

                $next = $queue->last_number + 1;
                $queue->update(['last_number' => $next]);

                return $next;
            });

            $appointmentNumber = Appointment::generateNextAppointmentNumber();

            $appointment->update([
                'status' => Appointment::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'session_key' => $sessionKey,
                'queue_number' => $queueNumber,
                'appointment_number' => $appointmentNumber,
            ]);

            NotificationService::send('appointment_approved', new AppointmentApproved($appointment), array_filter([$appointment->doctor?->user]));

            return redirect()->route('appointments.index')->with('success', __('file.appointment_approved'));
        } catch (\Exception $e) {
            return back()->with('error', __('file.appointment_approval_failed'));
        }
    }

    public function reject(Request $request, Appointment $appointment)
    {
        if (!auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider'])) {
            abort(403);
        }

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

        NotificationService::send('appointment_rejected', new AppointmentRejected($appointment));

        return back()->with('success', __('file.appointment_rejected'));
    }

    public function complete(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        $isDoctor = $user->hasRole('doctor');
        $isAdmin = $user->hasRole('admin');

        // Check if the user is the assigned doctor or an admin
        if ($isDoctor) {
            $userDoctor = $user->doctor;
            if (!$userDoctor || $userDoctor->id !== $appointment->doctor_id) {
                return back()->with('error', 'You can only complete appointments assigned to you.');
            }
        } elseif (!$isAdmin && !$user->hasRole('primary_care_provider')) {
            abort(403);
        }

        if ($appointment->status !== Appointment::STATUS_APPROVED && $appointment->status !== Appointment::STATUS_RUNNING) {
            return back()->with('error', 'Only approved or in-progress appointments can be marked as completed.');
        }

        if ($appointment->scheduled_start && $appointment->scheduled_start->isFuture()) {
            return back()->with('warning', __('file.future_appointment_warning'));
        }

        DB::transaction(function () use ($request, $appointment) {
            $appointment->update([
                'status' => Appointment::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);

            if ($request->filled('diagnosis') || $request->filled('medications')) {
                $prescription = $appointment->prescriptions()->create([
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'prescription_date' => now(),
                    'diagnosis' => $request->input('diagnosis'),
                    'notes' => $request->input('prescription_notes'),
                ]);

                if ($meds = $request->input('medications', [])) {
                    foreach ($meds as $med) {
                        if (!empty($med['name'])) {
                            $prescription->medications()->create([
                                'name' => $med['name'],
                                'dosage' => $med['dosage'] ?? null,
                                'frequency' => $med['frequency'] ?? null,
                                'duration_days' => $med['duration'] ?? null,
                                'instructions' => $med['instructions'] ?? null,
                            ]);
                        }
                    }
                }
            }
        });

        // Send Notification
        $recipients = NotificationSetting::getRecipients('appointment_completed');
        if ($appointment->patient && $appointment->patient->user) {
            $recipients->push($appointment->patient->user);
        }
        if ($appointment->doctor && $appointment->doctor->user) {
            $recipients->push($appointment->doctor->user);
        }
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients->unique('id'), new AppointmentCompleted($appointment));
        }

        return back()->with('success', __('file.appointment_completed_successfully'));
    }

    public function updateTreatments(Request $request, Appointment $appointment)
    {
        $user = auth()->user();
        $doctor = $user->doctor;

        if ($appointment->doctor_id && (!$doctor || $appointment->doctor_id !== $doctor->id)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only the assigned doctor can update treatments.'], 403);
            }
            return back()->with('error', 'Only the assigned doctor can update treatments.');
        }

        $validated = $request->validate([
            'treatment_ids' => 'nullable|array',
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
                        'quantity' => 1,
                        'price_at_time' => $doctorTreatments[$id]->pivot->price ?? 0,
                        'notes' => null,
                    ];
                }
            }
        }

        $appointment->treatments()->sync($syncData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.treatments_updated_successfully')
            ]);
        }

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', __('file.treatments_updated_successfully'));
    }
    private function processAppointmentApproval(Appointment $appointment): bool
    {
        if (!$appointment->doctor_id || !$appointment->scheduled_start) {
            return false;
        }

        $date = $appointment->scheduled_start->startOfDay();
        $sessionKey = $this->generateSessionKey($appointment);

        if (!$sessionKey) {
            return false;
        }

        try {
            $queueNumber = DB::transaction(function () use ($appointment, $date, $sessionKey) {
                $queue = \App\Models\DoctorSessionQueue::lockForUpdate()
                    ->firstOrCreate(
                        [
                            'doctor_id' => $appointment->doctor_id,
                            'queue_date' => $date,
                            'session_key' => $sessionKey,
                        ],
                        ['last_number' => 0]
                    );

                $next = $queue->last_number + 1;
                $queue->update(['last_number' => $next]);

                return $next;
            });

            $appointmentNumber = Appointment::generateNextAppointmentNumber();

            $appointment->update([
                'status' => Appointment::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'session_key' => $sessionKey,
                'queue_number' => $queueNumber,
                'appointment_number' => $appointmentNumber,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Appointment Approval Error: " . $e->getMessage());
            return false;
        }
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

    private function getRoomIdForAppointment(int $doctorId, Carbon $start): ?int
    {
        // room_id was removed from doctor_schedule_days; no automatic room can be derived from schedule.
        return null;
    }

    private function getSessionOrderForTime(Appointment $appointment): ?int
    {
        $apptTime = $appointment->scheduled_start->format('H:i:s');
        $apptDate = $appointment->scheduled_start->startOfDay();
        $apptDay = strtolower($appointment->scheduled_start->englishDayOfWeek); // stored lowercase in DB

        $schedules = $appointment->doctor
            ->schedules()
            ->where('is_active', true)
            ->where(function ($q) use ($apptDate) {
                $q->whereDate('valid_from', '<=', $apptDate)->orWhereNull('valid_from');
            })
            ->where(function ($q) use ($apptDate) {
                $q->whereDate('valid_until', '>=', $apptDate)->orWhereNull('valid_until');
            })
            ->whereHas('days', function ($q) use ($apptDay) {
                $q->where('day_of_week', $apptDay);
            })
            ->get();

        if ($schedules->isEmpty()) {
            return null;
        }

        $sorted = $schedules->sortBy(function ($s) use ($apptDay) {
            $day = $s->days->where('day_of_week', $apptDay)->first();
            return $day && $day->start_time ? $day->start_time->format('H:i:s') : '23:59:59';
        });

        $index = 0;
        foreach ($sorted as $schedule) {
            $day = $schedule->days->where('day_of_week', $apptDay)->first();
            if (!$day || !$day->start_time || !$day->end_time)
                continue;

            $startTime = $day->start_time->format('H:i:s');
            $endTime = $day->end_time->format('H:i:s');

            if ($apptTime >= $startTime && $apptTime <= $endTime) {
                return $index + 1;
            }
            $index++;
        }

        return null;
    }

    public function getDoctorSpecialization(Doctor $doctor)
    {
        $spec = $doctor->primarySpecialization;
        return response()->json($spec ? [
            'id' => $spec->id,
            'name' => $spec->name
        ] : null);
    }

    private function mapAppointmentForJs(Appointment $appt)
    {
        /** @var \App\Models\Patient|null $patient */
        $patient = $appt->patient;
        /** @var \App\Models\Doctor|null $doctor */
        $doctor = $appt->doctor;

        $prescription = $appt->prescriptions->sortByDesc('created_at')->first();

        return [
            'id' => $appt->id,
            'patient' => $patient?->full_name ?? 'Unknown Patient',
            'patient_id' => $appt->patient_id,
            'patient_mrn' => $patient?->medical_record_number ?? '—',
            'patient_dob' => $patient?->date_of_birth ? Carbon::parse($patient->date_of_birth)->format('d M Y') : '—',
            'patient_age' => $patient?->age !== null ? (int) $patient->age : ($patient?->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : '—'),
            'contact' => $patient?->phone ?? '',
            'date' => $appt->scheduled_start?->toDateString() ?? '',
            'time' => $appt->scheduled_start?->format('H:i') ?? '',
            'start_time' => $appt->scheduled_start?->format('H:i') ?? '',
            'end_time' => $appt->scheduled_end?->format('H:i') ?? '',
            'appt_time_label' => (function () use ($appt): string{
                if (!$appt->scheduled_start)
                    return '—';
                $start = $appt->scheduled_start;
                $dur = $appt->duration_minutes ?? null;
                if ($dur) {
                    return $start->format('g:i A') . ' – ' . $start->copy()->addMinutes($dur)->format('g:i A');
                }
                return $start->format('g:i A');
            })(),
            'slot_val' => $appt->scheduled_start && $appt->scheduled_end ? $appt->scheduled_start->format('H:i') . '|' . $appt->scheduled_end->format('H:i') : '',
            'slot_label' => (function () use ($appt): string{
                if (!$appt->doctor || !$appt->scheduled_start) {
                    return $appt->scheduled_start && $appt->scheduled_end
                        ? $appt->scheduled_start->format('g:i A') . ' – ' . $appt->scheduled_end->format('g:i A')
                        : ($appt->scheduled_start?->format('g:i A') ?? '—');
                }
                $apptDate = $appt->scheduled_start->copy();
                $dayName = strtolower($apptDate->format('l'));
                $schedule = $appt->doctor->schedules()
                    ->where('is_active', true)
                    ->where(fn($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', $apptDate))
                    ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', $apptDate))
                    ->whereHas('days', fn($q) => $q->where('day_of_week', $dayName))
                    ->with(['days' => fn($q) => $q->where('day_of_week', $dayName)])
                    ->first();
                if ($schedule) {
                    $day = $schedule->days->first();
                    if ($day && $day->start_time && $day->end_time) {
                        $slotStart = $apptDate->copy()->setTimeFromTimeString($day->start_time->format('H:i'));
                        $slotEnd = $apptDate->copy()->setTimeFromTimeString($day->end_time->format('H:i'));
                        return $slotStart->format('g:i A') . ' – ' . $slotEnd->format('g:i A');
                    }
                }
                return $appt->scheduled_start && $appt->scheduled_end
                    ? $appt->scheduled_start->format('g:i A') . ' – ' . $appt->scheduled_end->format('g:i A')
                    : ($appt->scheduled_start?->format('g:i A') ?? '—');
            })(),
            'attended_psychotherapy' => (bool) $patient?->attended_psychotherapy,
            'preferred_session_time' => $patient?->preferred_session_time ? date('h:i A', strtotime($patient->preferred_session_time)) : null,
            'recommended_by' => $patient?->recommended_by,
            'patient_document' => $patient?->document,
            'duration' => $appt->duration_minutes ?? ($appt->scheduled_start && $appt->scheduled_end ? $appt->scheduled_start->diffInMinutes($appt->scheduled_end) : 15),
            'doctor_id' => $appt->doctor_id,
            'doctor_name' => $doctor ? $doctor->full_name : 'Not Assigned',
            'doctor_email' => $appt->doctor?->email ?? '—',
            'doctor_spec' => $appt->doctor?->primarySpecialization?->name ?? '—',
            'room' => $appt->room?->room_number ? 'Room ' . $appt->room->room_number : 'Room —',
            'room_id' => $appt->room_id,
            'visit_type' => $appt->appointment_type ?? 'specific',
            'complaint' => $appt->reason_for_visit ?? '',
            'doctor_notes' => $appt->doctor_notes ?? '',
            'admin_notes' => $appt->admin_notes ?? '',
            'patient_notes' => $appt->patient_notes ?? '',
            'appointment_type' => $appt->appointment_type ?? 'specific',
            'specialization_id' => $appt->specialization_id,
            'age_group_id' => $appt->age_group_id,
            'age_group_name' => $appt->ageGroup?->name ?? '—',
            'preferred_language_id' => $appt->preferred_language_id,
            'preferred_language_name' => $appt->preferredLanguage?->name ?? '—',
            'duration_minutes' => $appt->duration_minutes ?? 15,
            'queue_number' => $appt->queue_number,
            'status' => $appt->status,
            'fee' => 0,
            'paid' => 0,
            'pay_method' => '',
            'created_at' => $appt->created_at->format('d M Y, g:i A'),
            'approved_by' => $appt->approvedBy?->name ?? '—',
            'treatments' => $appt->treatments->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'code' => $t->code,
                'price' => (float) ($t->pivot->price_at_time ?? 0),
                'qty' => (int) ($t->pivot->quantity ?? 1),
                'total' => (float) (($t->pivot->quantity ?? 1) * ($t->pivot->price_at_time ?? 0))
            ]),
            'total_treatments_cost' => (float) ($appt->total_treatment_price ?? 0),
            'prescription' => $prescription ? [
                'id' => $prescription->id,
                'date' => $prescription->prescription_date?->format('d M Y') ?? '—',
                'date_iso' => $prescription->prescription_date?->format('Y-m-d') ?? '',
                'diagnosis' => $prescription->diagnosis,
                'notes' => $prescription->notes,
                'type' => $prescription->type ?? 'Standard',
                'show_url' => route('prescriptions.show', $prescription),
                'edit_url' => route('prescriptions.edit', $prescription),
                'medications' => $prescription->medications->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'dosage' => $m->dosage,
                    'route' => $m->route,
                    'frequency' => $m->frequency,
                    'duration_days' => $m->duration_days,
                    'instructions' => $m->instructions,
                    'inventory_item_id' => $m->inventory_item_id
                ])
            ] : null,
            'create_prescription_url' => route('prescriptions.create') . "?appointment_id=" . $appt->id,
        ];
    }

    public function destroy(Appointment $appointment)
    {
        if (!auth()->user()->can('appointments.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.appointment_delete_denied')], 403);
            }
            return redirect()->route('appointments.index')
                ->with('error', __('file.appointment_delete_denied'));
        }

        $appointment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.appointment_deleted_successfully')]);
        }

        return back()->with('success', __('file.appointment_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->can('appointments.delete')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.appointment_bulk_delete_denied')], 403);
            }
            return back()->with('error', __('file.appointment_bulk_delete_denied'));
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'ids' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }
            return back()->with('error', $validator->errors()->first());
        }

        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        Appointment::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.appointment_bulk_deleted_successfully')]);
        }

        return back()->with('success', __('file.appointment_bulk_deleted_successfully'));
    }

    public function checkRoomAvailability(Request $request)
    {
        $date = $request->query('date');
        $time = $request->query('time');
        $duration = (int) $request->query('duration', 30);
        $excludeId = $request->query('exclude_id');

        if (!$date || !$time) {
            return response()->json(['busy_rooms' => []]);
        }

        try {
            $start = Carbon::parse("$date $time");
            $end = $start->copy()->addMinutes($duration);
            $busyRooms = $this->getBusyRooms($start, $end, $excludeId);
            
            return response()->json(['busy_rooms' => $busyRooms]);
        } catch (\Exception $e) {
            return response()->json(['busy_rooms' => [], 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get rooms that are already occupied by either a therapist's shift or another appointment.
     */
    private function getBusyRooms(Carbon $start, Carbon $end, ?int $excludeAppointmentId = null): array
    {
        $date = $start->toDateString();
        $startTimeStr = $start->format('H:i:s');
        $endTimeStr = $end->format('H:i:s');
        $dayOfWeek = strtolower($start->englishDayOfWeek);

        // room_id was dropped from doctor_schedule_days; shift-based room occupancy is no longer tracked there.
        $shiftBusyRooms = [];

        // 2. Rooms occupied by other appointments
        $appointmentBusyRooms = Appointment::whereDate('scheduled_start', $date)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_APPROVED, Appointment::STATUS_RUNNING])
            ->whereNotNull('room_id')
            ->when($excludeAppointmentId, function ($q) use ($excludeAppointmentId) {
                $q->where('id', '!=', $excludeAppointmentId);
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('scheduled_start', '<', $end)
                  ->where('scheduled_end', '>', $start);
            })
            ->pluck('room_id')
            ->toArray();

        return array_values(array_unique(array_merge($shiftBusyRooms, $appointmentBusyRooms)));
    }
}

