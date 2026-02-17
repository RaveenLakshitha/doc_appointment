<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSessionQueue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function dailyQueueOverview(Request $request)
    {
        if (!Auth::user()->can('queues.view')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : today();

        $appointments = Appointment::query()
            ->whereDate('scheduled_start', $date)
            ->where('status', Appointment::STATUS_APPROVED)
            ->with(['patient' => fn($q) => $q->select('id', 'first_name', 'middle_name', 'last_name')])
            ->orderBy('session_key')
            ->orderBy('queue_number')
            ->get();

        $queues = $appointments->groupBy('session_key')->map(function ($group) {
            return [
                'session_key' => $group->first()->session_key,
                'doctor_name' => $group->first()->doctor?->getFullNameAttribute() ?? 'Unknown',
                'patients'    => $group->map(fn($appt) => [
                    'queue_number' => $appt->queue_number,
                    'patient_name' => $appt->patient?->getFullNameAttribute() ?? '—',
                    'time'         => $appt->scheduled_start?->format('h:i A'),
                    'id'           => $appt->id,
                ])->sortBy('queue_number'),
            ];
        });

        return view('queues.daily-overview', compact('queues', 'date'));
    }

    public function complete(Appointment $appointment)
    {
        if (!Auth::user()->can('queues.manage')) {
            return back()->with('error', __('file.queues_manage_denied'));
        }

        if ($appointment->status !== Appointment::STATUS_APPROVED) {
            return back()->with('error', __('file.only_approved_appointments_can_be_completed'));
        }

        $appointment->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        $this->syncLastNumber(
            $appointment->session_key,
            $appointment->doctor_id,
            $appointment->scheduled_start?->startOfDay()
        );

        return back()->with('success', __('file.appointment_completed_successfully'));
    }

    public function updateQueueNumber(Appointment $appointment, Request $request)
    {
        if (!Auth::user()->can('queues.manage')) {
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
            $newNumber = $index + 1;
            $appt->update(['queue_number' => $newNumber]);

            if ($newNumber > $maxNumber) {
                $maxNumber = $newNumber;
            }
        }

        DoctorSessionQueue::updateOrCreate(
            [
                'doctor_id'   => $doctorId,
                'queue_date'  => $date,
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
                'doctor_id'   => $doctorId,
                'queue_date'  => $date,
                'session_key' => $sessionKey,
            ],
            [
                'last_number' => $lastNumber,
            ]
        );
    }
}