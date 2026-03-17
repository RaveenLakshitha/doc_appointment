<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorPanelCalendarController extends Controller
{
    public function index()
    {
        return view('doctor-panel.calendar');
    }

    public function events(Request $request)
    {
        $user = Auth::user();

        $doctor = $user->doctor;

        if (!$doctor) {
            \Log::warning('No doctor profile found for user', ['user_id' => $user->id]);
            return response()->json([]);
        }

        $startDate = \Carbon\Carbon::parse($request->start);
        $endDate = \Carbon\Carbon::parse($request->end);

        $appointments = Appointment::with(['patient'])
            ->where('doctor_id', $doctor->id)
            ->where('scheduled_start', '<', $endDate)
            ->where('scheduled_end', '>', $startDate)
            ->get();


        $events = $appointments->map(function ($appt) {
            $doctorName = Auth::user()->full_name ?? Auth::user()->name ?? 'Dr. You';

            return [
                'id' => $appt->id,
                'start' => $appt->scheduled_start ? $appt->scheduled_start->toISOString() : null,
                'end' => $appt->scheduled_end ? $appt->scheduled_end->toISOString() : null,
                'backgroundColor' => $this->getStatusColor($appt->status ?? 'scheduled'),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'patient' => $appt->patient?->full_name ?? $appt->patient?->name ?? '—',
                    'doctor' => $doctorName,
                    'status' => ucfirst($appt->status ?? 'Scheduled'),
                ],
            ];
        });


        \Log::info('Sending events to frontend', ['events_count' => $events->count()]);

        return response()->json($events);
    }

    private function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'approved'  => '#10b981', // green
            'running'   => '#f59e0b', // amber
            'completed' => '#6366f1', // indigo
            'paid'      => '#3b82f6', // blue
            'cancelled', 'rejected' => '#ef4444', // red
            'pending'   => '#9ca3af', // gray
            default     => '#3b82f6',
        };
    }
}