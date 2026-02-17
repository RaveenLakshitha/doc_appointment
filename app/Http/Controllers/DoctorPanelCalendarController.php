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

    $appointments = Appointment::with(['patient'])
        ->where('doctor_id', $doctor->id)
        ->whereBetween('scheduled_start', [$request->start, $request->end])
        ->get();


    $events = $appointments->map(function ($appt) {
        $doctorName = Auth::user()->full_name ?? Auth::user()->name ?? 'Dr. You';

        return [
            'id'              => $appt->id,
            'start'           => $appt->scheduled_start ? $appt->scheduled_start->toISOString() : null,
            'end'             => $appt->scheduled_end   ? $appt->scheduled_end->toISOString()   : null,
            'backgroundColor' => $this->getStatusColor($appt->status ?? 'scheduled'),
            'textColor'       => '#ffffff',
            'extendedProps'   => [
                'patient' => $appt->patient?->full_name ?? $appt->patient?->name ?? '—',
                'doctor'  => $doctorName,
                'status'  => ucfirst($appt->status ?? 'Scheduled'),
            ],
        ];
    });

    
    \Log::info('Sending events to frontend', ['events_count' => $events->count()]);
    
    return response()->json($events);
}

    private function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'confirmed'  => '#10b981',
            'pending'    => '#f59e0b',
            'cancelled'  => '#ef4444',
            'completed'  => '#6366f1',
            default      => '#3b82f6',
        };
    }
}