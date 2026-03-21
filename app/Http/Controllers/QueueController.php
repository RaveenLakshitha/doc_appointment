<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSessionQueue;
use Carbon\Carbon;
use App\Models\User;
use App\Models\NotificationSetting;
use App\Notifications\AppointmentCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;

class QueueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:queues.index', ['only' => ['dailyQueueOverview']]);
    }
    protected function authorizeQueueManage(Appointment $appointment)
    {
        $user = Auth::user();
        
        if ($user->can('queues.manage')) {
            return true;
        }

        if ($user->doctor && $user->doctor->id === $appointment->doctor_id) {
            return true;
        }

        return false;
    }
    public function dailyQueueOverview(Request $request)
    {
        if (!Auth::user()->can('queues.view')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : today();

        $selectedDoctor = $request->query('doctor_id');
        $selectedRoom = $request->query('room_id');

        $doctors = Doctor::orderBy('first_name')->get();
        $rooms = Room::orderBy('room_number')->get();

        $query = Appointment::query()
            ->whereDate('scheduled_start', $date)
            ->whereIn('status', [Appointment::STATUS_APPROVED, Appointment::STATUS_RUNNING, Appointment::STATUS_PAID])
            ->with(['patient' => fn($q) => $q->select('id', 'first_name', 'middle_name', 'last_name'), 'room' => fn($q) => $q->select('id', 'room_number', 'name')])
            ->orderBy('session_key')
            ->orderByRaw("CASE WHEN status = 'running' THEN 0 ELSE 1 END")
            ->orderBy('queue_number');

        if ($selectedDoctor) {
            $query->where('doctor_id', $selectedDoctor);
        }

        if ($selectedRoom) {
            $query->where('room_id', $selectedRoom);
        }

        $appointments = $query->get();

        $runningDoctorIds = $appointments->where('status', Appointment::STATUS_RUNNING)->pluck('doctor_id')->unique()->toArray();

        $queues = $appointments->groupBy('session_key')->map(function ($group) {
            return [
                'session_key' => $group->first()->session_key,
                'doctor_name' => $group->first()->doctor?->getFullNameAttribute() ?? 'Unknown',
                'room_name' => $group->first()->room?->room_number,
                'patients' => $group->map(fn($appt) => [
                    'queue_number' => $appt->queue_number,
                    'patient_name' => $appt->patient?->getFullNameAttribute() ?? '-',
                    'time' => $appt->scheduled_start?->format('h:i A'),
                    'id' => $appt->id,
                    'status' => $appt->status,
                    'doctor_id' => $appt->doctor_id,
                ])->sortBy('queue_number'),
            ];
        });

        return view('queues.daily-overview', compact('queues', 'date', 'doctors', 'selectedDoctor', 'runningDoctorIds', 'rooms', 'selectedRoom'));
    }

    public function start(Appointment $appointment)
    {
        if (!$this->authorizeQueueManage($appointment)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.queues_manage_denied')], 403);
            }
            return back()->with('error', __('file.queues_manage_denied'));
        }

        if (!in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_PAID])) {
            $msg = __('file.only_approved_appointments_can_be_started') ?? 'Only approved appointments can be started.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        $updates = [
            'status' => Appointment::STATUS_RUNNING,
        ];

        if (!$appointment->queue_number) {
            $maxNumber = Appointment::query()
                ->where('session_key', $appointment->session_key)
                ->where('doctor_id', $appointment->doctor_id)
                ->whereDate('scheduled_start', $appointment->scheduled_start?->startOfDay())
                ->max('queue_number');

            $updates['queue_number'] = ($maxNumber ?: 0) + 1;
        }

        $appointment->update($updates);

        if (isset($updates['queue_number'])) {
            $this->syncLastNumber(
                $appointment->session_key,
                $appointment->doctor_id,
                $appointment->scheduled_start?->startOfDay()
            );
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.appointment_started_successfully') ?? 'Appointment started successfully.',
                'status' => Appointment::STATUS_RUNNING,
                'queue_number' => $appointment->queue_number
            ]);
        }

        return back()->with('success', __('file.appointment_started_successfully') ?? 'Appointment started successfully.');
    }

    public function complete(Appointment $appointment)
    {
        if (!$this->authorizeQueueManage($appointment)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.queues_manage_denied')], 403);
            }
            return back()->with('error', __('file.queues_manage_denied'));
        }

        if (!in_array($appointment->status, [Appointment::STATUS_APPROVED, Appointment::STATUS_PAID, Appointment::STATUS_RUNNING])) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.only_approved_appointments_can_be_completed')], 400);
            }
            return back()->with('error', __('file.only_approved_appointments_can_be_completed'));
        }

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        $this->syncLastNumber(
            $appointment->session_key,
            $appointment->doctor_id,
            $appointment->scheduled_start?->startOfDay()
        );

        \App\Services\NotificationService::send('appointment_completed', new AppointmentCompleted($appointment), array_filter([$appointment->doctor?->user, $appointment->patient?->user]));

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('file.appointment_completed_successfully'),
                'status' => 'completed'
            ]);
        }

        return back()->with('success', __('file.appointment_completed_successfully'));
    }

    public function updateQueueNumber(Appointment $appointment, Request $request)
    {
        if (!$this->authorizeQueueManage($appointment)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.queues_manage_denied')], 403);
            }
            return back()->with('error', __('file.queues_manage_denied'));
        }

        $request->validate([
            'queue_number' => 'required|integer|min:1',
        ]);

        $appointment->update([
            'queue_number' => $request->queue_number,
        ]);

        $this->syncLastNumber(
            $appointment->session_key,
            $appointment->doctor_id,
            $appointment->scheduled_start?->startOfDay()
        );

        return back()->with('success', __('file.queue_number_updated_successfully'));
    }

    protected function renumberSession($sessionKey, $doctorId, $date)
    {
        if (!$sessionKey || !$doctorId || !$date) {
            return;
        }

        $appointments = Appointment::query()
            ->where('session_key', $sessionKey)
            ->where('doctor_id', $doctorId)
            ->whereDate('scheduled_start', $date)
            ->where('status', Appointment::STATUS_APPROVED)
            ->orderBy('queue_number')
            ->orderBy('scheduled_start')
            ->get();

        $maxNumber = 0;

        foreach ($appointments as $index => $appt) {
            /** @var \App\Models\Appointment $appt */
            $newNumber = $index + 1;
            $appt->update(['queue_number' => $newNumber]);

            if ($newNumber > $maxNumber) {
                $maxNumber = $newNumber;
            }
        }

        DoctorSessionQueue::updateOrCreate(
            [
                'doctor_id' => $doctorId,
                'queue_date' => $date,
                'session_key' => $sessionKey,
            ],
            [
                'last_number' => $maxNumber,
            ]
        );
    }

    protected function syncLastNumber($sessionKey, $doctorId, $date)
    {
        if (!$sessionKey || !$doctorId || !$date) {
            return;
        }

        $max = Appointment::query()
            ->where('session_key', $sessionKey)
            ->where('doctor_id', $doctorId)
            ->whereDate('scheduled_start', $date)
            ->where('status', Appointment::STATUS_APPROVED)
            ->max('queue_number');

        $lastNumber = $max ? (int) $max : 0;

        DoctorSessionQueue::updateOrCreate(
            [
                'doctor_id' => $doctorId,
                'queue_date' => $date,
                'session_key' => $sessionKey,
            ],
            [
                'last_number' => $lastNumber,
            ]
        );
    }
}
