<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentChangeRequest;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\NewAppointmentChangeRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AppointmentChangeRequestController extends Controller
{
    // ── Middleware ────────────────────────────────────────────────────────────
    public function __construct()
    {
        // Only staff who can manage appointments can review requests
        $this->middleware('permission:appointments.edit', [
            'only' => ['approve', 'reject', 'adminIndex', 'adminDatatable'],
        ]);
    }

    // =========================================================================
    // PATIENT-FACING
    // =========================================================================

    /**
     * Show all change requests for the currently-logged-in patient.
     */
    public function patientIndex()
    {
        $patient = $this->resolvePatient();
        if (!$patient) {
            abort(403, 'No patient profile linked to your account.');
        }

        return view('appointment-change-requests.patient-index', compact('patient'));
    }

    /**
     * Datatable endpoint for patient's own change requests.
     */
    public function patientDatatable(Request $request)
    {
        $patient = $this->resolvePatient();
        if (!$patient) {
            return response()->json(['data' => []]);
        }

        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));

        $query = AppointmentChangeRequest::with(['appointment.doctor', 'appointment.specialization'])
            ->where('patient_id', $patient->id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('request_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $total    = (clone $query)->count();
        $filtered = $total;

        $rows = $query->latest()->offset($start)->limit($length)->get();

        $data = $rows->map(fn($r) => $this->formatRowForPatient($r));

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data->toArray(),
        ]);
    }

    /**
     * Submit a new change request (reschedule or cancel).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id'  => 'required|exists:appointments,id',
            'request_type'    => 'required|in:reschedule,cancel',
            'reason'          => 'required|string|max:1000',
            'requested_date'  => 'nullable|date|after_or_equal:today',
            'requested_time'  => 'nullable|date_format:H:i',
            'slot'            => 'nullable|string',
            'preferred_time'  => 'nullable|in:morning,evening,anytime',
        ]);

        $appointment = Appointment::with('patient')->find($validated['appointment_id']);

        // If patient is logged in, verify ownership
        $patient = $this->resolvePatient();
        if ($patient) {
            if ($appointment->patient_id !== $patient->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
        } else {
            // If staff/admin, they can create for any patient
            if (!auth()->user()->can('appointments.edit')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            $patient = $appointment->patient;
        }

        if (!$appointment || !in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_ASSIGNED, Appointment::STATUS_PENDING])) {
            return response()->json([
                'success' => false,
                'message' => __('file.appointment_not_found_or_not_eligible') ?? 'Appointment not found or not eligible for change requests.',
            ], 422);
        }

        // Prevent duplicate pending requests
        $existing = AppointmentChangeRequest::where('appointment_id', $appointment->id)
            ->where('status', AppointmentChangeRequest::STATUS_PENDING)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => __('file.pending_request_exists') ?? 'A pending request already exists for this appointment.',
            ], 422);
        }

        $changeRequest = AppointmentChangeRequest::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $patient->id,
            'request_type'   => $validated['request_type'],
            'status'         => AppointmentChangeRequest::STATUS_PENDING,
            'reason'         => $validated['reason'],
            'requested_date' => $validated['requested_date'] ?? null,
            'requested_time' => $validated['requested_time'] ?? null,
            'slot'           => $validated['slot'] ?? null,
            'preferred_time' => $validated['preferred_time'] ?? null,
        ]);

        // Reload with relationships so we can format the admin row
        $changeRequest->load(['appointment.doctor', 'appointment.specialization', 'patient', 'reviewer']);

        // Send notification to admins & receptionists
        $recipients = User::role(['admin', 'receptionist'])->get();
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewAppointmentChangeRequestNotification($changeRequest));
        }

        // Staff/admin flow: return approve/reject URLs + full row so the frontend
        // can immediately open the Approve/Reject modal without a page refresh.
        $isStaff = auth()->user()->can('appointments.edit');

        return response()->json([
            'success'     => true,
            'message'     => __('file.change_request_submitted') ?? 'Your request has been submitted successfully.',
            'request'     => $changeRequest,
            'approve_url' => $isStaff ? route('appointment-change-requests.approve', $changeRequest) : null,
            'reject_url'  => $isStaff ? route('appointment-change-requests.reject',  $changeRequest) : null,
            'row'         => $isStaff ? $this->formatRowForAdmin($changeRequest) : null,
        ]);
    }

    /**
     * Patient can withdraw their own pending request.
     */
    public function withdraw(AppointmentChangeRequest $changeRequest)
    {
        $patient = $this->resolvePatient();

        if (!$patient || $changeRequest->patient_id !== $patient->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (!$changeRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => __('file.request_already_processed') ?? 'This request has already been processed.',
            ], 422);
        }

        $changeRequest->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.request_withdrawn') ?? 'Request withdrawn successfully.',
        ]);
    }

    // =========================================================================
    // ADMIN / STAFF FACING
    // =========================================================================

    /**
     * Admin view – list all pending change requests.
     */
    public function adminIndex()
    {
        return view('appointment-change-requests.admin-index');
    }

    /**
     * Datatable endpoint for admin – all change requests.
     */
    public function adminDatatable(Request $request)
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));
        $status = $request->input('status');
        $type   = $request->input('request_type');

        $query = AppointmentChangeRequest::with([
            'patient',
            'appointment.doctor',
            'appointment.specialization',
            'reviewer',
        ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('request_type', $type);
        }

        if ($request->has('patient_id') && $request->input('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('request_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($sq) => $sq->whereRaw(
                      "CONCAT_WS(' ', first_name, NULLIF(middle_name, ''), last_name) LIKE ?",
                      ["%{$search}%"]
                  ));
            });
        }

        $total    = (clone $query)->count();
        $filtered = $total;

        $rows = $query->latest()->offset($start)->limit($length)->get();

        $data = $rows->map(fn($r) => $this->formatRowForAdmin($r));

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data->toArray(),
        ]);
    }

    /**
     * Approve a change request (applies the reschedule or cancellation).
     */
    public function approve(Request $request, AppointmentChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => __('file.request_already_processed') ?? 'Request already processed.',
            ], 422);
        }

        $request->validate([
            'admin_notes'    => 'nullable|string|max:1000',
            'requested_date' => 'nullable|date|after_or_equal:today',
            'requested_time' => 'nullable|date_format:H:i',
            'slot'           => 'nullable|string',
            'preferred_time' => 'nullable|in:morning,evening,anytime',
            'doctor_id'      => 'nullable|exists:doctors,id',
        ]);

        DB::transaction(function () use ($request, $changeRequest) {
            $appointment = $changeRequest->appointment;

            if ($changeRequest->isCancel()) {
                // Cancel the linked appointment
                $appointment->update([
                    'status'       => Appointment::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'admin_notes'  => $request->input('admin_notes'),
                ]);
            } elseif ($changeRequest->isReschedule()) {
                // Override values from the approve-modal form take priority;
                // fall back to what the patient originally requested.
                $newDate = $request->input('requested_date')
                    ? \Carbon\Carbon::parse($request->input('requested_date'))
                    : $changeRequest->requested_date;

                $newTime = $request->input('requested_time')
                    ?? $changeRequest->requested_time;

                $newSlot = $request->input('slot')
                    ?? $changeRequest->slot;

                $newPreferred = $request->input('preferred_time')
                    ?? $changeRequest->preferred_time
                    ?? $appointment->preferred_time;

                $updateData = [
                    'status'         => Appointment::STATUS_APPROVED,
                    'preferred_time' => $newPreferred,
                    'doctor_id'      => $request->input('doctor_id') ?? $appointment->doctor_id,
                ];

                if ($newDate && $newTime) {
                    $start    = \Carbon\Carbon::parse("{$newDate->format('Y-m-d')} {$newTime}");
                    $duration = $appointment->duration_minutes ?? 30;
                    $updateData['scheduled_start'] = $start;
                    $updateData['scheduled_end']   = $start->copy()->addMinutes($duration);
                } elseif ($newDate) {
                    // Only date provided – keep existing time if available
                    if ($appointment->scheduled_start) {
                        $time  = $appointment->scheduled_start->format('H:i');
                        $start = \Carbon\Carbon::parse("{$newDate->format('Y-m-d')} {$time}");
                        $duration = $appointment->duration_minutes ?? 30;
                        $updateData['scheduled_start'] = $start;
                        $updateData['scheduled_end']   = $start->copy()->addMinutes($duration);
                    }
                }

                // Persist the final resolved scheduling values back onto the change request
                $changeRequest->requested_date  = $newDate;
                $changeRequest->requested_time  = $newTime;
                $changeRequest->slot            = $newSlot;
                $changeRequest->preferred_time  = $newPreferred;

                $appointment->update($updateData);
            }

            // Mark request as approved
            $changeRequest->update([
                'status'         => AppointmentChangeRequest::STATUS_APPROVED,
                'reviewed_by'    => auth()->id(),
                'reviewed_at'    => now(),
                'admin_notes'    => $request->input('admin_notes'),
                'requested_date' => $changeRequest->requested_date,
                'requested_time' => $changeRequest->requested_time,
                'slot'           => $changeRequest->slot,
                'preferred_time' => $changeRequest->preferred_time,
            ]);

            // WhatsApp Alert for Reschedule
            if ($changeRequest->isReschedule()) {
                try {
                    $waService = app(\App\Services\WhatsAppService::class);
                    $waService->sendAppointmentAlert($appointment, 'reschedule');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send WhatsApp reschedule alert: ' . $e->getMessage());
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('file.change_request_approved') ?? 'Request approved and appointment updated.',
        ]);
    }

    /**
     * Reject a change request (no changes to appointment).
     */
    public function reject(Request $request, AppointmentChangeRequest $changeRequest)
    {
        if (!$changeRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => __('file.request_already_processed') ?? 'Request already processed.',
            ], 422);
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $changeRequest->update([
            'status'      => AppointmentChangeRequest::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('file.change_request_rejected') ?? 'Request rejected.',
        ]);
    }

    /**
     * Get eligible appointments (approved/assigned) for the patient to submit requests against.
     */
    public function eligibleAppointments()
    {
        $patient = $this->resolvePatient();
        if (!$patient) {
            return response()->json([]);
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', [Appointment::STATUS_APPROVED, Appointment::STATUS_ASSIGNED])
            ->whereDoesntHave('pendingChangeRequests')
            ->with(['doctor', 'specialization'])
            ->orderByDesc('scheduled_start')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'number'      => $a->appointment_number ?? "#{$a->id}",
                'doctor'      => $a->doctor?->getFullNameAttribute() ?? '—',
                'date'        => $a->scheduled_start?->format('M d, Y') ?? __('file.not_set') ?? 'Not set',
                'time'        => $a->scheduled_start?->format('h:i A') ?? '—',
                'status'      => $a->status,
            ]);

        return response()->json($appointments);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function resolvePatient(): ?Patient
    {
        $user = auth()->user();
        // Try direct relationship
        if ($user->patient) {
            return $user->patient;
        }
        // Try matching on email
        return Patient::where('email', $user->email)->first();
    }

    private function formatRowForPatient(AppointmentChangeRequest $r): array
    {
        $appt = $r->appointment;

        $typeBadge = $r->request_type === 'reschedule'
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">'
              . (__('file.reschedule') ?? 'Reschedule') . '</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">'
              . (__('file.cancel_appointment') ?? 'Cancel') . '</span>';

        $statusBadge = match ($r->status) {
            'pending'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">' . (__('file.pending') ?? 'Pending') . '</span>',
            'approved' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">' . (__('file.approved') ?? 'Approved') . '</span>',
            'rejected' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">' . (__('file.rejected') ?? 'Rejected') . '</span>',
            default    => '<span class="text-gray-500">—</span>',
        };

        return [
            'id'            => $r->id,
            'appointment'   => $appt?->appointment_number ?? "#{$appt?->id}",
            'appt_date'     => $appt?->scheduled_start?->translatedFormat('M d, Y h:i A') ?? '—',
            'doctor'        => $appt?->doctor?->getFullNameAttribute() ?? '—',
            'request_type'  => $r->request_type,
            'type_badge'    => $typeBadge,
            'reason'        => e($r->reason),
            'requested_date'=> $r->requested_date?->translatedFormat('M d, Y') ?? '—',
            'requested_time'=> $r->requested_time ? Carbon::createFromFormat('H:i', $r->requested_time)->translatedFormat('h:i A') : '—',
            'slot'          => $r->slot ?? '—',
            'preferred_time'=> $r->preferred_time ? (__('file.' . $r->preferred_time) ?? ucfirst($r->preferred_time)) : '—',
            'status_badge'  => $statusBadge,
            'status'        => $r->status,
            'admin_notes'   => e($r->admin_notes ?? ''),
            'created_at'    => $r->created_at->translatedFormat('M d, Y'),
            'withdraw_url'  => $r->status === 'pending'
                ? route('appointment-change-requests.withdraw', $r)
                : null,
        ];
    }

    private function formatRowForAdmin(AppointmentChangeRequest $r): array
    {
        $appt    = $r->appointment;
        $patient = $r->patient;

        $typeBadge = $r->request_type === 'reschedule'
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">' . (__('file.reschedule') ?? 'Reschedule') . '</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">' . (__('file.cancel_appointment') ?? 'Cancel') . '</span>';

        $statusBadge = match ($r->status) {
            'pending'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">' . (__('file.pending') ?? 'Pending') . '</span>',
            'approved' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">' . (__('file.approved') ?? 'Approved') . '</span>',
            'rejected' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">' . (__('file.rejected') ?? 'Rejected') . '</span>',
            default    => '<span class="text-gray-500">—</span>',
        };

        return [
            'id'                  => $r->id,
            'patient_id'          => $patient?->id,
            'patient_name'        => $patient?->getFullNameAttribute() ?? '—',
            'appointment'         => $appt?->appointment_number ?? "#{$appt?->id}",
            'appt_date'           => $appt?->scheduled_start?->translatedFormat('M d, Y h:i A') ?? '—',
            'doctor'              => $appt?->doctor?->getFullNameAttribute() ?? '—',
            // Raw doctor_id so the approve modal can load slots
            'doctor_id'           => $appt?->doctor_id,
            'request_type'        => $r->request_type,
            'type_badge'          => $typeBadge,
            'reason'              => e($r->reason),
            'requested_date'      => $r->requested_date?->translatedFormat('M d, Y') ?? '—',
            // Raw ISO date for pre-filling the date input in the approve modal
            'requested_date_raw'  => $r->requested_date?->format('Y-m-d'),
            'requested_time'      => $r->requested_time ? Carbon::createFromFormat('H:i', $r->requested_time)->translatedFormat('h:i A') : '—',
            'slot'                => $r->slot ?? '—',
            'preferred_time'      => $r->preferred_time ? (__('file.' . $r->preferred_time) ?? ucfirst($r->preferred_time)) : '—',
            // Raw value for pre-selecting the preferred time dropdown
            'preferred_time_raw'  => $r->preferred_time,
            'status_badge'        => $statusBadge,
            'status'              => $r->status,
            'admin_notes'         => e($r->admin_notes ?? ''),
            'reviewed_by'         => $r->reviewer?->name ?? '—',
            'reviewed_at'         => $r->reviewed_at?->translatedFormat('M d, Y') ?? '—',
            'created_at'          => $r->created_at->translatedFormat('M d, Y'),
            'approve_url'         => $r->status === 'pending' ? route('appointment-change-requests.approve', $r) : null,
            'reject_url'          => $r->status === 'pending' ? route('appointment-change-requests.reject',  $r) : null,
        ];
    }
}
