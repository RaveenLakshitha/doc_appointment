<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeLeaveEntitlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:leave-requests.index', ['only' => ['index', 'show', 'datatable', 'myRequests']]);
        $this->middleware('permission:leave-requests.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:leave-requests.edit', ['only' => ['edit', 'update', 'approve', 'reject']]);
        $this->middleware('permission:leave-requests.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('leave-requests.index')) {
            return redirect()->route('home')
                ->with('error', __('file.permission_denied'));
        }

        $employees = Employee::orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        $leaveTypes = LeaveType::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('leave-requests.index', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leave-requests.create')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        LeaveRequest::create([
            'employee_id' => $validated['employee_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_requested' => $days,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        if (!Auth::user()->can('leave-requests.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $oldStatus = $leaveRequest->status;
        $newStatus = $validated['status'];

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        DB::beginTransaction();
        try {
            $leaveRequest->update([
                'employee_id' => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days_requested' => $days,
                'reason' => $validated['reason'],
                'status' => $newStatus,
            ]);

            // If status changed to approved, trigger the same logic as approve()
            if ($oldStatus !== 'approved' && $newStatus === 'approved') {
                $this->handleApproval($leaveRequest);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Leave Update Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function handleApproval(LeaveRequest $leaveRequest)
    {
        $startDate = Carbon::parse($leaveRequest->start_date);
        $endDate = Carbon::parse($leaveRequest->end_date);
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $leaveRequest->employee_id,
                    'date' => $currentDate->toDateString(),
                ],
                [
                    'status' => 'leave',
                    'notes' => 'Leave Approved: ' . ($leaveRequest->leaveType?->name ?? 'Leave'),
                    'marked_by' => Auth::id(),
                    'marked_at' => now(),
                    'leave_request_id' => $leaveRequest->id
                ]
            );
            $currentDate->addDay();
        }

        $entitlement = EmployeeLeaveEntitlement::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $startDate->year)
            ->first();

        if ($entitlement) {
            $entitlement->increment('used_days', $leaveRequest->days_requested);
        }
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $statusFilter = $request->status;
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

        $totalRecords = LeaveRequest::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int) $orderIdx) {
            0 => 'employee_id',
            1 => 'leave_type_id',
            2 => 'start_date',
            3 => 'days_requested',
            4 => 'status',
            default => 'created_at',
        };

        $query->orderBy($sortColumn, $orderDir);

        $requests = $query->offset($start)->limit($length)->get();

        $data = $requests->map(function ($req) {
            $statusClasses = [
                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
            ];

            $statusHtml = '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ' .
                ($statusClasses[$req->status] ?? 'bg-gray-100') . '">' .
                ucfirst($req->status) . '</span>';

            return [
                'id' => $req->id,
                'employee_name' => $req->employee ? $req->employee->full_name : '-',
                'leave_type' => $req->leaveType->name ?? '-',
                'dates' => $req->start_date->format('d M Y') . ' → ' . $req->end_date->format('d M Y'),
                'days' => $req->days_requested,
                'status_html' => $statusHtml,
                'reason' => $req->reason ?? '',
                'employee_id' => $req->employee_id,
                'leave_type_id' => $req->leave_type_id,
                'start_date' => $req->start_date->format('Y-m-d'),
                'end_date' => $req->end_date->format('Y-m-d'),
                'can_approve' => $req->status === 'pending' && Auth::user()->can('leave-requests.approve'),
                'can_reject' => $req->status === 'pending' && Auth::user()->can('leave-requests.reject'),
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        Log::info("Approve Method Called for Request ID: " . $leaveRequest->id);

        if (!Auth::user()->can('leave-requests.approve') || $leaveRequest->status !== 'pending') {
            Log::warning("Unauthorized or not pending status for request " . $leaveRequest->id);
            return response()->json(['success' => false, 'message' => 'Unauthorized or not pending'], 403);
        }

        DB::beginTransaction();
        try {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::user()->employee?->id,
                'approved_at' => now(),
            ]);

            $this->handleApproval($leaveRequest);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Leave Approval Error [Request ID: {$leaveRequest->id}]: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if (!Auth::user()->can('leave-requests.reject') || $leaveRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Unauthorized or not pending'], 403);
        }

        $request->validate(['reason' => 'required|string|max:500']);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->employee?->id,
            'approved_at' => now(),
            'rejected_reason' => $request->reason,
        ]);

        return response()->json(['success' => true]);
    }

    public function myRequests()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('home')
                ->with('error', 'No employee profile linked to your account.');
        }

        $requests = LeaveRequest::with(['leaveType', 'approver'])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveBalances = EmployeeLeaveEntitlement::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get()
            ->map(function ($ent) {
                return [
                    'type' => $ent->leaveType->name,
                    'entitled' => $ent->entitled_days,
                    'used' => $ent->used_days,
                    'remaining' => $ent->entitled_days - $ent->used_days,
                ];
            });

        return view('leave-requests.my-requests', compact('requests', 'leaveBalances', 'employee'));
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        if (!Auth::user()->can('leave-requests.delete')) {
            return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
        }

        $leaveRequest->delete();

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('leave-requests.delete')) {
            return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
        }

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_matching_records')], 400);
        }

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $count = LeaveRequest::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.bulk_deleted', ['count' => $count])
        ]);
    }
}