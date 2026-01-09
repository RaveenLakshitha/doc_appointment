<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class LeaveRequestsController extends Controller
{
    public function index()
    {
        return view('leave-requests.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);

        $query = LeaveRequest::query()
            ->with(['employee.department', 'approver'])
            ->select('leave_requests.*');

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('leave_type', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
        }

        $query->when($request->employee, fn($q) => $q->where('employee_id', $request->employee))
              ->when($request->department, fn($q) => $q->whereHas('employee', fn($sq) => $sq->where('department_id', $request->department)))
              ->when($request->leave_type, fn($q) => $q->where('leave_type', $request->leave_type))
              ->when($request->status, fn($q) => $q->where('status', $request->status))
              ->when($request->date_from, fn($q) => $q->whereDate('start_date', '>=', $request->date_from))
              ->when($request->date_to, fn($q) => $q->whereDate('end_date', '<=', $request->date_to));

        $totalRecords = LeaveRequest::count();
        $filteredRecords = (clone $query)->count();

        $requests = $query->orderBy('start_date', 'desc')->offset($start)->limit($length)->get();

        $data = $requests->map(function ($lr) {
            $statusBadge = match($lr->status) {
                'pending'  => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pending</span>',
                'approved' => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Approved</span>',
                'rejected' => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Rejected</span>',
                default    => '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">' . ucfirst($lr->status) . '</span>'
            };

            return [
                'employee_name'   => $lr->employee->name ?? '-',
                'department_name' => $lr->employee->department?->name ?? '-',
                'leave_type'      => ucfirst(str_replace('_', ' ', $lr->leave_type)),
                'start_date'      => $lr->start_date->format('Y-m-d'),
                'end_date'        => $lr->end_date->format('Y-m-d'),
                'days_requested'  => $lr->days_requested,
                'status_html'     => $statusBadge,
                'approved_by'     => $lr->approver?->name ?? '-',
                'reason'          => $lr->reason ? '<span class="text-sm text-gray-600 dark:text-gray-400">' . Str::limit($lr->reason, 50) . '</span>' : '-',
                'edit_url'        => route('leave-requests.edit', $lr),
                'delete_url'      => route('leave-requests.destroy', $lr),
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