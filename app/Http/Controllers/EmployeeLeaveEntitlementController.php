<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeaveEntitlement;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeLeaveEntitlementController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('leave-entitlements.index')) {
            return redirect()->route('home')
                ->with('error', 'Sorry! You are not allowed to access this module.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('leave-entitlements.index', compact('employees'));
    }

    public function datatable(Request $request)
    {
        if (!Auth::user()->can('leave-entitlements.index')) {
            abort(403);
        }

        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $employeeFilter = $request->employee_id;
        $yearFilter     = $request->year;

        $query = EmployeeLeaveEntitlement::with(['employee', 'leaveType'])
            ->select('employee_leave_entitlements.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('leaveType', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                  ->orWhereHas('employee', function ($sq) use ($searchValue) {
                      $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$searchValue}%"])
                         ->orWhere('employee_code', 'like', "%{$searchValue}%");
                  });
            })
            ->when($employeeFilter, fn($q) => $q->where('employee_id', $employeeFilter))
            ->when($yearFilter, fn($q) => $q->where('year', $yearFilter));

        $totalRecords    = EmployeeLeaveEntitlement::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int)$orderIdx) {
            1 => 'year',
            2 => 'employees.first_name',
            3 => 'leave_types.name',
            4 => 'entitled_days',
            default => 'year',
        };

        if (in_array($sortColumn, ['employees.first_name', 'leave_types.name'])) {
            $query->join('employees', 'employee_leave_entitlements.employee_id', '=', 'employees.id')
                  ->leftJoin('leave_types', 'employee_leave_entitlements.leave_type_id', '=', 'leave_types.id')
                  ->orderBy($sortColumn === 'employees.first_name' ? 'employees.first_name' : 'leave_types.name', $orderDir);
        } else {
            $query->orderBy($sortColumn, $orderDir);
        }

        $entitlements = $query->offset($start)->limit($length)->get();

        $data = $entitlements->map(function ($ent) {
            $remaining = $ent->entitled_days - $ent->used_days;

            $remainingHtml = $remaining > 0
                ? '<span class="text-green-600 font-medium">' . number_format($remaining, 1) . '</span>'
                : ($remaining < 0 ? '<span class="text-red-600 font-medium">' . number_format($remaining, 1) . '</span>' : '<span class="text-gray-600">0</span>');

            $edit_url   = Auth::user()->can('leave-entitlements.update') ? route('leave-entitlements.edit', $ent) : null;
            $delete_url = Auth::user()->can('leave-entitlements.delete') ? route('leave-entitlements.destroy', $ent) : null;

            return [
                'id'             => $ent->id,
                'employee_name'  => $ent->employee ? $ent->employee->full_name : '-',
                'leave_type'     => $ent->leaveType->name ?? '-',
                'year'           => $ent->year,
                'entitled'       => number_format($ent->entitled_days, 1),
                'used'           => number_format($ent->used_days, 1),
                'remaining_html' => $remainingHtml,
                'edit_url'       => $edit_url,
                'delete_url'     => $delete_url,
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('leave-entitlements.create')) {
            return redirect()->route('leave-entitlements.index')
                ->with('error', 'Sorry! You are not allowed to create entitlements.');
        }

        $employees  = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $leaveTypes = LeaveType::where('active', true)->orderBy('name')->get();
        $currentYear = now()->year;

        return view('leave-entitlements.create', compact('employees', 'leaveTypes', 'currentYear'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leave-entitlements.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id'     => ['required', 'exists:employees,id'],
            'leave_type_id'   => ['required', 'exists:leave_types,id'],
            'year'            => ['required', 'integer', 'min:2000', 'max:' . (now()->year + 5)],
            'entitled_days'   => ['required', 'numeric', 'min:0', 'max:365'],
            'accrual_rate'    => ['nullable', 'numeric', 'min:0', 'max:30'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $exists = EmployeeLeaveEntitlement::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['year' => 'Entitlement already exists for this employee, leave type, and year.'])
                ->withInput();
        }

        EmployeeLeaveEntitlement::create($validated);

        return redirect()->route('leave-entitlements.index')
            ->with('success', 'Leave entitlement assigned successfully.');
    }

    public function edit(EmployeeLeaveEntitlement $entitlement)
    {
        if (!Auth::user()->can('leave-entitlements.update')) {
            return redirect()->route('leave-entitlements.index')
                ->with('error', 'Sorry! You are not allowed to edit entitlements.');
        }

        $employees  = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $leaveTypes = LeaveType::where('active', true)->orderBy('name')->get();

        return view('leave-entitlements.edit', compact('entitlement', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, EmployeeLeaveEntitlement $entitlement)
    {
        if (!Auth::user()->can('leave-entitlements.update')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id'     => ['required', 'exists:employees,id'],
            'leave_type_id'   => ['required', 'exists:leave_types,id'],
            'year'            => ['required', 'integer', 'min:2000', 'max:' . (now()->year + 5)],
            'entitled_days'   => ['required', 'numeric', 'min:0', 'max:365'],
            'accrual_rate'    => ['nullable', 'numeric', 'min:0', 'max:30'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        // Check for duplicate (excluding current record)
        $duplicate = EmployeeLeaveEntitlement::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $entitlement->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['year' => 'Another entitlement already exists for this combination.'])
                ->withInput();
        }

        $entitlement->update($validated);

        return redirect()->route('leave-entitlements.index')
            ->with('success', 'Leave entitlement updated successfully.');
    }

    public function destroy(EmployeeLeaveEntitlement $entitlement)
    {
        if (!Auth::user()->can('leave-entitlements.delete')) {
            return redirect()->route('leave-entitlements.index')
                ->with('error', 'Sorry! You are not allowed to delete entitlements.');
        }

        // Optional: prevent delete if already used significantly
        if ($entitlement->used_days > 0) {
            return back()->with('error', 'Cannot delete: Leave has already been used.');
        }

        $entitlement->delete();

        return redirect()->route('leave-entitlements.index')
            ->with('success', 'Leave entitlement deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('leave-entitlements.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete entitlements.'
            ], 403);
        }

        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No entitlements selected'
            ]);
        }

        // Optional: add check for used_days > 0 before bulk delete

        EmployeeLeaveEntitlement::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected entitlements deleted successfully.'
        ]);
    }
}