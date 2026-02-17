<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleDay;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        return view('doctor-schedules.index');
    }

    public function create()
    {
        $doctors = Doctor::active()->orderBy('last_name')->orderBy('first_name')->get();
        $rooms   = Room::with('department')->orderBy('room_number')->get();

        return view('doctor-schedules.create', compact('doctors', 'rooms'));
    }

    public function store(Request $request)
    {
        $settings = Setting::getAll();

        $validated = $request->validate([
            'doctor_id'    => 'required|exists:doctors,id',
            'room_id'      => 'required|exists:rooms,id',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
            'is_active'    => 'sometimes|boolean',
        ], [
            'end_time.required' => 'The end time is required.',
        ]);

        DB::transaction(function () use ($validated) {
            $schedule = DoctorSchedule::create([
                'doctor_id'   => $validated['doctor_id'],
                'room_id'     => $validated['room_id'],
                'start_time'  => $validated['start_time'],
                'end_time'    => $validated['end_time'],
                'valid_from'  => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'is_active'   => $validated['is_active'] ?? true,
            ]);

            foreach ($validated['days_of_week'] as $day) {
                DoctorScheduleDay::create([
                    'doctor_schedule_id' => $schedule->id,
                    'day_of_week'        => $day,
                ]);
            }
        });

        return redirect()->route('doctor-schedules.index')
                         ->with('success', __('file.schedule_created_successfully'));
    }

    public function edit(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->load('days');

        $doctors = Doctor::active()->orderBy('last_name')->orderBy('first_name')->get();
        $rooms   = Room::with('department')->orderBy('room_number')->get();

        return view('doctor-schedules.edit', compact('doctorSchedule', 'doctors', 'rooms'));
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        $validated = $request->validate([
            'doctor_id'    => 'required|exists:doctors,id',
            'room_id'      => 'required|exists:rooms,id',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
            'is_active'    => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($validated, $doctorSchedule) {
            $doctorSchedule->update([
                'doctor_id'   => $validated['doctor_id'],
                'room_id'     => $validated['room_id'],
                'start_time'  => $validated['start_time'],
                'end_time'    => $validated['end_time'],
                'valid_from'  => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'is_active'   => $validated['is_active'] ?? false,
            ]);

            $doctorSchedule->days()->delete();
            foreach ($validated['days_of_week'] as $day) {
                DoctorScheduleDay::create([
                    'doctor_schedule_id' => $doctorSchedule->id,
                    'day_of_week'        => $day,
                ]);
            }
        });

        return redirect()->route('doctor-schedules.index')
                         ->with('success', __('file.schedule_updated_successfully'));
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->days()->delete();
        $doctorSchedule->delete();

        return redirect()->route('doctor-schedules.index')
                         ->with('success', __('file.schedule_deleted_successfully'));
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $searchValue = trim($request->input('search.value', ''));
        $doctorFilter = $request->doctor;
        $roomFilter   = $request->room;

        $query = DoctorSchedule::query()
            ->with(['doctor', 'room.department'])
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('doctor', fn($sq) => $sq->where('first_name', 'like', "%{$searchValue}%")
                    ->orWhere('last_name', 'like', "%{$searchValue}%"))
                  ->orWhereHas('room', fn($sq) => $sq->where('room_number', 'like', "%{$searchValue}%"));
            })
            ->when($doctorFilter, fn($q) => $q->where('doctor_id', $doctorFilter))
            ->when($roomFilter, fn($q) => $q->where('room_id', $roomFilter));

        $totalRecords    = DoctorSchedule::count();
        $filteredRecords = (clone $query)->count();

        $query->orderBy('start_time', 'asc');

        $schedules = $query->offset($start)->limit($length)->get();

        $data = $schedules->map(function ($schedule) {
            $days = $schedule->days->pluck('day_of_week')->map(fn($d) => ucfirst($d))->join(', ');

            $time = $schedule->start_time->format('g:i A') . ' - ' . $schedule->end_time->format('g:i A');

            $roomDisplay = $schedule->room
                ? $schedule->room->room_number . ' (' . ($schedule->room->department?->name ?? '—') . ')'
                : '(Room deleted)';

            return [
                'id'     => $schedule->id,
                'doctor' => $schedule->doctor->getFullNameAttribute(),
                'room'   => $roomDisplay,
                'days'   => $days ?: '-',
                'time'   => $time,
                'edit_url'    => route('doctor-schedules.edit', $schedule),
                'delete_url'  => route('doctor-schedules.destroy', $schedule),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        return match ($column) {
            'doctor' => Doctor::active()->orderBy('last_name')->get()->map(fn($d) => [
                'id'   => $d->id,
                'name' => $d->getFullNameAttribute()
            ])->pluck('name', 'id'),
            'room'   => Room::orderBy('room_number')->get()->map(fn($r) => [
                'id'   => $r->id,
                'name' => $r->room_number . ' - ' . $r->department->name
            ])->pluck('name', 'id'),
            default  => response()->json([]),
        };
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No schedules selected']);
        }

        DoctorSchedule::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }

    public function calendar(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $doctors  = Doctor::active()
                          ->orderBy('last_name')
                          ->orderBy('first_name')
                          ->get();

        return view('doctor-schedules.calendar', compact('doctors', 'doctorId'));
    }

    public function calendarEvents(Request $request)
    {
        $start    = $request->query('start');
        $end      = $request->query('end');
        $doctorId = $request->query('doctor_id');

        $schedules = DoctorSchedule::with(['doctor', 'room.department', 'days'])
            ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
            ->where('is_active', true)
            ->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $days = $schedule->days->pluck('day_of_week')->toArray();
            if (empty($days)) continue;

            $startTime = $schedule->start_time->format('H:i:s');
            $endTime   = $schedule->end_time->format('H:i:s');
            $timeLabel = $schedule->start_time->format('g:i A') . ' - ' . $schedule->end_time->format('g:i A');
            $dayNames  = collect($days)->map(fn($d) => ucfirst($d))->join(', ');

            $doctorName = $schedule->doctor->getFullNameAttribute();
            $roomInfo   = $schedule->room
                ? $schedule->room->room_number . ' (' . ($schedule->room->department?->name ?? '—') . ')'
                : '(Room deleted)';

            $dowMap = [
                'sunday'    => 0,
                'monday'    => 1,
                'tuesday'   => 2,
                'wednesday' => 3,
                'thursday'  => 4,
                'friday'    => 5,
                'saturday'  => 6
            ];
            $dow = array_values(array_map(fn($d) => $dowMap[$d], $days));

            $event = [
                'title'          => "Dr. $doctorName - Room $roomInfo",
                'startTime'      => $startTime,
                'endTime'        => $endTime,
                'daysOfWeek'     => $dow,
                'startRecur'     => $schedule->valid_from?->format('Y-m-d') ?? '2000-01-01',
                'backgroundColor'=> '#6366f1',
                'borderColor'    => '#4f46e5',
                'textColor'      => '#ffffff',
                'extendedProps'  => [
                    'doctor'    => $doctorName,
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

    public function currentQueue(Doctor $doctor)
    {
        $today = today();

        $current = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_start', $today)
            ->where('status', Appointment::STATUS_APPROVED)
            ->orderBy('queue_number')
            ->first();

        $next = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_start', $today)
            ->where('status', Appointment::STATUS_APPROVED)
            ->where('queue_number', '>', $current?->queue_number ?? 0)
            ->orderBy('queue_number')
            ->take(5)
            ->get();

        return view('doctor.current-queue', compact('doctor', 'current', 'next'));
    }
}