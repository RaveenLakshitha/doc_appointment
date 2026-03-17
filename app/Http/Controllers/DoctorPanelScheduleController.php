<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorPanelScheduleController extends Controller
{
    public function calendar()
    {
        return view('doctor-panel.schedule-calendar');
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return response()->json([]);
        }

        $schedules = DoctorSchedule::with(['doctor', 'room.department', 'days'])
            ->where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $days = $schedule->days->pluck('day_of_week')->toArray();
            if (empty($days))
                continue;

            $startTime = $schedule->start_time->format('H:i:s');
            $endTime = $schedule->end_time->format('H:i:s');
            $timeLabel = $schedule->start_time->format('g:i A') . ' - ' . $schedule->end_time->format('g:i A');

            $dayNames = collect($days)->map(fn($d) => ucfirst($d))->join(', ');

            $roomInfo = $schedule->room
                ? $schedule->room->room_number . ' (' . ($schedule->room->department?->name ?? '—') . ')'
                : '(Room deleted)';

            $dowMap = [
                'sunday' => 0,
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6
            ];

            $dow = array_values(array_map(fn($d) => $dowMap[$d], $days));

            $event = [
                'title'       => "Room $roomInfo · $timeLabel",
                'startTime'   => $startTime,
                'endTime'     => $endTime,
                'daysOfWeek'  => $dow,
                'startRecur'  => $schedule->valid_from?->format('Y-m-d') ?? '2000-01-01',
                'backgroundColor' => '#6366f1',
                'borderColor' => '#4f46e5',
                'textColor'   => '#ffffff',
                'extendedProps' => [
                    'room'      => $roomInfo,
                    'time'      => $timeLabel,
                    'days'      => $dayNames,
                    'is_active' => $schedule->is_active,
                ]
            ];

            if ($schedule->valid_until) {
                $event['endRecur'] = $schedule->valid_until->addDay()->format('Y-m-d');
            }

            $events[] = $event;
        }

        return response()->json($events);
    }
}