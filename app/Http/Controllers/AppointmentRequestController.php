<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\Specialization;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:appointment_requests.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:appointment_requests.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:appointment_requests.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:appointment_requests.delete', ['only' => ['destroy']]);
    }
    /**
     * Display listing of appointment requests (admin/staff view)
     */
    public function index(Request $request)
    {
        // For regular paginated view (optional fallback)
        $requests = AppointmentRequest::with(['patient', 'specialization', 'doctor', 'primaryCareProvider'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('appointment-requests.index', compact('requests'));
    }

    /**
     * Server-side DataTables endpoint
     */
    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'desc');
        $searchValue = trim($request->input('search.value', ''));

        $statusFilter         = $request->status;
        $specializationFilter = $request->specialization_id;

        $query = AppointmentRequest::query()
            ->with(['patient', 'specialization', 'doctor', 'primaryCareProvider', 'assignedDoctor'])
            ->select('appointment_requests.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('patient', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                ->orWhereHas('specialization', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                ->orWhere('reason_for_visit', 'like', "%{$searchValue}%")
                ->orWhere('notes', 'like', "%{$searchValue}%");
            })
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($specializationFilter, fn($q) => $q->where('specialization_id', $specializationFilter));

        $totalRecords    = AppointmentRequest::count();
        $filteredRecords = (clone $query)->count();

        // Ordering - adjusted for current columns (no selection_mode)
        $columns = ['patient_name', 'specialization_name', 'requested_doctor_name', 'requested_datetime', 'status_badge'];
        if (isset($columns[$orderIdx])) {
            $orderColumn = $columns[$orderIdx];
            switch ($orderColumn) {
                case 'patient_name':
                    $query->join('patients', 'appointment_requests.patient_id', '=', 'patients.id')
                        ->orderByRaw("CONCAT(patients.first_name, ' ', COALESCE(patients.middle_name,''), ' ', patients.last_name) {$orderDir}");
                    break;
                case 'specialization_name':
                    $query->join('specializations', 'appointment_requests.specialization_id', '=', 'specializations.id')
                        ->orderBy('specializations.name', $orderDir);
                    break;
                case 'requested_datetime':
                    $query->orderBy('requested_date', $orderDir)->orderBy('requested_start_time', $orderDir);
                    break;
                case 'status_badge':
                    $query->orderBy('status', $orderDir);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $requests = $query->offset($start)->limit($length)->get();

        $data = $requests->map(function ($req) {
            // Status badge for display
            $statusBadge = match($req->status) {
                'pending'   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Pending</span>',
                'approved'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Approved</span>',
                'rejected'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Rejected</span>',
                'cancelled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Cancelled</span>',
                default     => '<span class="text-gray-500">-</span>'
            };

            // Formatted requested date/time
            $datetime = $req->requested_date && $req->requested_start_time
                ? $req->requested_date->format('M d, Y') . ' at ' . $req->requested_start_time->format('h:i A')
                : 'Flexible';

            return [
                'id'                     => $req->id,
                'patient_name'           => $req->patient?->getFullNameAttribute() ?? '-',
                'specialization_name'    => $req->specialization?->name ?? '-',
                'requested_doctor_name'  => $req->doctor?->getFullNameAttribute() ?? '-',
                'requested_datetime'     => $datetime,
                'status_badge'           => $statusBadge,
                'status'                 => $req->status, // Critical: used in view for conditional buttons
                'show_url'               => route('appointment_requests.show', $req),
                'edit_url'               => \Auth::user()->can('appointment_requests.edit') ? route('appointment_requests.edit', $req) : null,
                'approve_url'            => route('appointment_requests.approve', $req),
                'reject_url'             => route('appointment_requests.reject', $req),
                'cancel_url'             => \Auth::user()->can('appointment_requests.delete') ? route('appointment_requests.cancel', $req) : null,
            ];
        });

        return response()->json([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {

        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'middle_name', 'last_name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);
        $doctors = Doctor::active()->orderByFullName()->get();

        return view('appointment-requests.create', compact('patients', 'specializations', 'doctors'));
    }

    public function store(Request $request)
    {
        //$this->authorize('create', AppointmentRequest::class);

        $validated = $request->validate([
            'patient_id'                   => 'required|exists:patients,id',
            'specialization_id'            => 'required|exists:specializations,id',
            'doctor_selection_mode'        => ['required', Rule::in([
                AppointmentRequest::DOCTOR_SELECTION_SPECIFIC,
                AppointmentRequest::DOCTOR_SELECTION_ANY,
                AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER,
            ])],
            'doctor_id'                    => 'required_if:doctor_selection_mode,specific|nullable|exists:doctors,id',
            'primary_care_provider_id'     => 'nullable|exists:users,id',
            'requested_date'               => 'required|date|after_or_equal:today',
            'requested_start_time'         => 'required|date_format:H:i',
            'preferred_time_range_start'   => 'nullable|date_format:H:i|before:preferred_time_range_end',
            'preferred_time_range_end'     => 'nullable|date_format:H:i|after:preferred_time_range_start',
            'duration_minutes'             => 'required|integer|min:15|max:180',
            'reason_for_visit'             => 'required|string|max:1000',
            'notes'                        => 'nullable|string|max:2000',
        ]);

        $validated['status'] = AppointmentRequest::STATUS_PENDING;

        AppointmentRequest::create($validated);

        return redirect()->route('appointment_requests.index')
            ->with('success', 'Appointment request created successfully.');
    }

    public function show(AppointmentRequest $appointmentRequest)
    {

        $appointmentRequest->load([
            'specialization',
            'doctor',
            'primaryCareProvider',
            'assignedDoctor',
            'schedule.room',
            'appointment'
        ]);

        return view('appointment-requests.show', compact('appointmentRequest'));
    }

    public function edit(AppointmentRequest $appointmentRequest)
    {
        $patient = auth()->user()->patient;

        if (!$patient || $appointmentRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if ($appointmentRequest->status !== AppointmentRequest::STATUS_PENDING) {
            return redirect()->route('patient.appointment_requests.show', $appointmentRequest)
                ->with('error', 'Only pending requests can be edited.');
        }

        $specializations = Specialization::orderBy('name')->get(['id', 'name']);
        $doctors = Doctor::active()->orderByFullName()->get();

        $primaryCareProvider = $patient->primary_care_provider_id
            ? \App\Models\User::find($patient->primary_care_provider_id)
            : null;

        return view('appointment-requests.patient-edit', compact(
            'appointmentRequest',
            'specializations',
            'doctors',
            'primaryCareProvider'
        ));
    }

    public function update(Request $request, AppointmentRequest $appointmentRequest)
    {
        $patient = auth()->user()->patient;

        if (!$patient || $appointmentRequest->patient_id !== $patient->id) {
            abort(403);
        }

        if ($appointmentRequest->status !== AppointmentRequest::STATUS_PENDING) {
            return back()->with('error', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'specialization_id'            => 'required|exists:specializations,id',
            'doctor_selection_mode'        => ['required', Rule::in([
                AppointmentRequest::DOCTOR_SELECTION_SPECIFIC,
                AppointmentRequest::DOCTOR_SELECTION_ANY,
                AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER,
            ])],
            'doctor_id'                    => 'required_if:doctor_selection_mode,specific|nullable|exists:doctors,id',
            'requested_date'               => 'required|date|after_or_equal:today',
            'requested_start_time'         => 'required|date_format:H:i',
            'preferred_time_range_start'   => 'nullable|date_format:H:i|before:preferred_time_range_end',
            'preferred_time_range_end'     => 'nullable|date_format:H:i|after:preferred_time_range_start',
            'duration_minutes'             => 'required|integer|min:15|max:180',
            'reason_for_visit'             => 'required|string|max:1000',
            'notes'                        => 'nullable|string|max:2000',
        ]);

        if ($validated['doctor_selection_mode'] === AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER) {
            if (!$patient->primary_care_provider_id) {
                return back()->withErrors(['doctor_selection_mode' => 'You do not have a primary care provider assigned.']);
            }
            $validated['primary_care_provider_id'] = $patient->primary_care_provider_id;
        } else {
            $validated['primary_care_provider_id'] = null;
        }

        $appointmentRequest->update($validated);

        return redirect()->route('patient.appointment_requests.show', $appointmentRequest)
            ->with('success', 'Your appointment request has been updated successfully.');
    }


    /**
     * Filter options for drawer (specializations only now)
     */
    public function filters(Request $request)
    {
        $column = $request->query('column');

        if ($column === 'specialization') {
            return Specialization::orderBy('name')->pluck('name', 'id');
        }

        return response()->json([]);
    }
    
    public function getDoctorsBySpecialization($specialization_id)
    {
        $doctors = Doctor::active()
            ->where('primary_specialization_id', $specialization_id)
            ->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'primary_specialization_id']);

        $options = $doctors->map(function ($doctor) {
            return [
                'value' => $doctor->id,
                'text'  => $doctor->getFullNameAttribute() . ' (' . ($doctor->primarySpecialization?->name ?? __('file.no_specialization')) . ')',
            ];
        });

        return response()->json($options);
    }

    public function availableTimes(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date'            => 'required|date|after_or_equal:today',
            'duration_minutes' => 'required|integer|in:15,30,45,60,90',
        ]);

        $date = $request->date;
        $duration = $request->duration_minutes;
        $carbonDate = \Carbon\Carbon::parse($date);

        // Get active schedules for this doctor that cover this date and are active
        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                ->orWhereDate('valid_until', '>=', $date);
            })
            ->with('days')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Find schedules applicable for the day of week
        $dayOfWeek = strtolower($carbonDate->format('l')); // monday, tuesday, etc.

        $applicableSchedules = $schedules->filter(function ($schedule) use ($dayOfWeek) {
            return in_array($dayOfWeek, $schedule->getDaysOfWeekAttribute());
        });

        if ($applicableSchedules->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Collect all possible time ranges for the day
        $possibleSlots = [];
        foreach ($applicableSchedules as $schedule) {
            $start = \Carbon\Carbon::parse($schedule->start_time);
            $end   = \Carbon\Carbon::parse($schedule->end_time);

            $current = $start->copy();
            while ($current->addMinutes($duration)->lte($end)) {
                $slotStart = $current->copy()->subMinutes($duration); // back to start of slot
                $slotEnd   = $slotStart->copy()->addMinutes($duration);

                $possibleSlots[] = $slotStart->format('H:i');
                $current = $slotEnd->copy();
            }
        }

        // Remove duplicates and sort
        $possibleSlots = array_unique($possibleSlots);
        sort($possibleSlots);

        // TODO: In a real system, subtract booked appointments here
        // For now, we return all possible slots from schedules
        $availableSlots = array_values($possibleSlots);

        return response()->json([
            'slots' => $availableSlots
        ]);
    }
    
}