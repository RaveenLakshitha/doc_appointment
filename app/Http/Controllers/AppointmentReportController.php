<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentReportController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get(['id', 'name']);
        $doctors = Doctor::active()
            ->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'department_id']);

        return view('reports.appointments', [
            'departments' => $departments,
            'doctors'     => $doctors,
        ]);
    }

    public function summary(Request $request)
    {
        [$start, $end] = $this->parseDateRange($request);

        $departmentId = $request->integer('department_id') ?: null;
        $doctorId     = $request->integer('doctor_id') ?: null;

        $query = Appointment::with(['patient', 'doctor.department'])
            ->whereBetween('scheduled_start', [$start, $end]);

        if ($departmentId) {
            $query->whereHas('doctor', fn($q) => $q->where('department_id', $departmentId));
        }
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        // Summary stats
        $total     = (clone $query)->count();
        $completed = (clone $query)->where('status', Appointment::STATUS_COMPLETED)->count();
        $cancelled = (clone $query)->where('status', Appointment::STATUS_CANCELLED)->count();

        $noShow = (clone $query)
            ->where('status', Appointment::STATUS_APPROVED)
            ->where('scheduled_end', '<', now())
            ->count();

        // Status distribution for doughnut chart
        $statusDist = (clone $query)
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $statusData = [
            'completed' => $statusDist['completed'] ?? 0,
            'approved'  => $statusDist['approved']  ?? 0,
            'cancelled' => $statusDist['cancelled'] ?? 0,
            'pending'   => $statusDist['pending']   ?? 0,
        ];

        // By department for bar chart
        $byDept = (clone $query)
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('departments', 'doctors.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(*) as cnt'))
            ->groupBy('departments.id', 'departments.name')
            ->pluck('cnt', 'name')
            ->toArray();

        // Recent appointments (last 10)
        $recent = (clone $query)
            ->latest('scheduled_start')
            ->take(10)
            ->get()
            ->map(function ($appt) {
                return [
                    'patient'     => $appt->patient?->full_name ?? '—',
                    'doctor'      => $appt->doctor?->full_name ?? '—',
                    'date_time'   => $appt->scheduled_start?->format('M d, Y g:i A') ?? '—',
                    'status'      => $appt->status,
                    'status_class'=> $this->getStatusClass($appt->status),
                ];
            })->toArray();

        // By doctor table data
        $byDoctorQuery = Appointment::query()
            ->select(
                'doctor_id',
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "' . Appointment::STATUS_COMPLETED . '" then 1 else 0 end) as completed'),
                DB::raw('sum(case when status = "' . Appointment::STATUS_CANCELLED . '" then 1 else 0 end) as cancelled'),
                DB::raw('sum(case when status = "' . Appointment::STATUS_APPROVED . '" and scheduled_end < now() then 1 else 0 end) as no_show')
            )
            ->whereBetween('scheduled_start', [$start, $end])
            ->groupBy('doctor_id')
            ->with('doctor.department');

        if ($departmentId) {
            $byDoctorQuery->whereHas('doctor', fn($q) => $q->where('department_id', $departmentId));
        }
        if ($doctorId) {
            $byDoctorQuery->where('doctor_id', $doctorId);
        }

        $byDoctor = $byDoctorQuery->get()->map(function ($row) {
            $total = $row->total;
            $rate  = $total > 0 ? round(($row->completed / $total) * 100, 1) : 0;

            return [
                'doctor'          => $row->doctor?->full_name ?? '—',
                'department'      => $row->doctor?->department?->name ?? '—',
                'total'           => $total,
                'completed'       => $row->completed,
                'cancelled'       => $row->cancelled,
                'no_show'         => $row->no_show,
                'completion_rate' => $rate,
            ];
        })->toArray();

        // By department table data – FIXED ambiguous 'status'
        $byDepartmentQuery = DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('departments', 'doctors.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department',
                DB::raw('count(*) as total'),
                DB::raw('sum(case when appointments.status = "' . Appointment::STATUS_COMPLETED . '" then 1 else 0 end) as completed'),
                DB::raw('sum(case when appointments.status = "' . Appointment::STATUS_CANCELLED . '" then 1 else 0 end) as cancelled'),
                DB::raw('sum(case when appointments.status = "' . Appointment::STATUS_APPROVED . '" and scheduled_end < now() then 1 else 0 end) as no_show')
            )
            ->whereBetween('appointments.scheduled_start', [$start, $end])
            ->groupBy('departments.id', 'departments.name');

        if ($departmentId) {
            $byDepartmentQuery->where('departments.id', $departmentId);
        }
        if ($doctorId) {
            $byDepartmentQuery->where('appointments.doctor_id', $doctorId);
        }

        $byDepartment = $byDepartmentQuery->get()->map(function ($row) {
            $rate = $row->total > 0 ? round(($row->completed / $row->total) * 100, 1) : 0;
            return [
                'department'      => $row->department,
                'total'           => $row->total,
                'completed'       => $row->completed,
                'cancelled'       => $row->cancelled,
                'no_show'         => $row->no_show,
                'completion_rate' => $rate,
            ];
        })->toArray();

        $response = [
            'summary'             => compact('total', 'completed', 'cancelled', 'noShow'),
            'status_distribution' => $statusData,
            'by_department'       => $byDept,
            'recent'              => $recent,
            'by_doctor'           => $byDoctor,
            'by_department_table' => $byDepartment,
        ];

        return response()->json($response);
    }

    private function parseDateRange(Request $request): array
    {
        $range = $request->string('date_range', '');

        if (str_contains($range, ' - ')) {
            [$startStr, $endStr] = explode(' - ', $range);
            $start = Carbon::parse($startStr)->startOfDay();
            $end   = Carbon::parse($endStr)->endOfDay();
        } else {
            $start = Carbon::today()->subMonths(3)->startOfDay();
            $end   = Carbon::now()->endOfDay();
        }

        return [$start, $end];
    }

    private function getStatusClass(string $status): string
    {
        return match ($status) {
            Appointment::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            Appointment::STATUS_CANCELLED => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            Appointment::STATUS_APPROVED  => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            Appointment::STATUS_PENDING   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            default                       => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}