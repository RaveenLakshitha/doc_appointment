<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendances.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);

        $query = Attendance::query()
            ->with(['employee.department'])
            ->select('attendances.*');

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        $query->when($request->employee, fn($q) => $q->where('employee_id', $request->employee))
              ->when($request->department, fn($q) => $q->whereHas('employee', fn($sq) => $sq->where('department_id', $request->department)))
              ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
              ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to))
              ->when($request->status, fn($q) => $q->where('status', $request->status));

        $totalRecords = Attendance::count();
        $filteredRecords = (clone $query)->count();

        $attendances = $query->orderBy('date', 'desc')->offset($start)->limit($length)->get();

        $data = $attendances->map(function ($a) {
            $statusBadge = match($a->status) {
                'present'   => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Present</span>',
                'absent'    => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Absent</span>',
                'late'      => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Late</span>',
                'half_day'  => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Half Day</span>',
                default     => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">'.ucfirst($a->status).'</span>'
            };

            return [
                'date'             => $a->date->format('Y-m-d'),
                'employee_name'    => $a->employee->name ?? '-',
                'department_name'  => $a->employee->department?->name ?? '-',
                'clock_in'         => $a->clock_in ? $a->clock_in->format('H:i') : '-',
                'clock_out'        => $a->clock_out ? $a->clock_out->format('H:i') : '-',
                'status_html'      => $statusBadge,
                'notes'            => $a->notes,
                'edit_url'         => route('attendances.edit', $a),
                'delete_url'       => route('attendances.destroy', $a),
            ];
        });

        return response()->json([
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        if ($column === 'employee') {
            return Employee::orderBy('name')->pluck('name', 'id');
        }

        if ($column === 'department') {
            return Department::orderBy('name')->pluck('name', 'id');
        }

        return response()->json([]);
    }
}