<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\EmployeeLeaveEntitlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('leave-requests.index')) {
            return redirect()->route('home')
                ->with('error', 'Sorry! You are not allowed to access this module.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('leave-requests.index', compact('employees'));
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $statusFilter   = $request->status;
        $employeeFilter = $request->employee_id;

        $query = LeaveRequest::with(['employee', 'leaveType', 'approver'])
            ->select('leave_requests.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('employee', fn($sq) => $sq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchValue}%"]))
                  ->orWhereHas('leaveType', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                  ->orWhere('reason', 'like', "%{$searchValue}%");
            })
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($employeeFilter, fn($q) => $q->where('employee_id', $employeeFilter));

        $totalRecords    = LeaveRequest::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int)$orderIdx) {
            1 => 'created_at',
            2 => 'start_date',
            3 => 'status',
            default => 'created_at',
        };

        $query->orderBy($sortColumn, $orderDir === 'asc' ? 'desc' : 'asc'); // most recent first by default

        $requests = $query->offset($start)->limit($length)->get();

        $data = $requests->map(function ($req) {
            $statusClasses = [
                'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30',
                'approved'  => 'bg-green-100 text-green-800 dark:bg-green-900/30',
                'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900/30',
                'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-gray-700',
            ];

            $statusHtml = '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ' .
                ($statusClasses[$req->status] ?? 'bg-gray-100') . '">' .
                ucfirst($req->status) . '</span>';

            $edit_url   = null; // usually not editable after submit
            $approve_url = ($req->status === 'pending' && Auth::user()->can('leave-requests.approve'))
                ? route('leave-requests.approve', $req) : null;

            return [
                'id'             => $req->id,
                'employee_name'  => $req->employee ? $req->employee->full_name : '-',
                'leave_type'     => $req->leaveType->name ?? '-',
                'dates'          => $req->start_date->format('d M Y') . ' → ' . $req->end_date->format('d M Y'),
                'days'           => $req->days_requested,
                'status_html'    => $statusHtml,
                'approve_url'    => $approve_url,
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function myRequests()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('home')
                ->with('error', 'No employee profile linked to your account.');
        }

        // Get only this employee's requests
        $requests = LeaveRequest::with(['leaveType', 'approver'])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Optional: pass leave balances if you want to show them too
        $leaveBalances = EmployeeLeaveEntitlement::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get()
            ->map(function ($ent) {
                return [
                    'type'      => $ent->leaveType->name,
                    'entitled'  => $ent->entitled_days,
                    'used'      => $ent->used_days,
                    'remaining' => $ent->entitled_days - $ent->used_days,
                ];
            });

        return view('leave-requests.my-requests', compact('requests', 'leaveBalances', 'employee'));
    }
}