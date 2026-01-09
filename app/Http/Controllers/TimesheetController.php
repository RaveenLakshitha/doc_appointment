<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function index()
    {
        return view('timesheets.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);

        $query = Timesheet::query()
            ->with(['employee.department'])
            ->select('timesheets.*');

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $query->when($request->employee, fn($q) => $q->where('employee_id', $request->employee))
              ->when($request->department, fn($q) => $q->whereHas('employee', fn($sq) => $sq->where('department_id', $request->department)))
              ->when($request->date_from, fn($q) => $q->whereDate('date', '>=', $request->date_from))
              ->when($request->date_to, fn($q) => $q->whereDate('date', '<=', $request->date_to));

        $totalRecords = Timesheet::count();
        $filteredRecords = (clone $query)->count();

        $timesheets = $query->orderBy('date', 'desc')->offset($start)->limit($length)->get();

        $data = $timesheets->map(function ($t) {
            return [
                'date'             => $t->date->format('Y-m-d'),
                'employee_name'    => $t->employee->name ?? '-',
                'department_name'  => $t->employee->department?->name ?? '-',
                'start_time'       => $t->start_time->format('H:i'),
                'end_time'         => $t->end_time->format('H:i'),
                'hours_worked'     => number_format($t->hours_worked, 2),
                'description'      => $t->description ? '<span class="text-sm text-gray-600 dark:text-gray-400">' . Str::limit($t->description, 50) . '</span>' : '-',
                'edit_url'         => route('timesheets.edit', $t),
                'delete_url'       => route('timesheets.destroy', $t),
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

        if ($column === 'employee') {
            return Employee::orderBy('name')->pluck('name', 'id');
        }

        if ($column === 'department') {
            return Department::orderBy('name')->pluck('name', 'id');
        }

        return response()->json([]);
    }
}