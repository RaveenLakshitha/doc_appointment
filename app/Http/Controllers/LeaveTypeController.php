<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('leave-types.index')) {
            return redirect()->route('home')
                ->with('error', 'Sorry! You are not allowed to access this module.');
        }

        return view('leave-types.index');
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $activeFilter = $request->active;

        $query = LeaveType::query()
            ->select('leave_types.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('code', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%");
            })
            ->when($activeFilter !== null, function ($q) use ($activeFilter) {
                $q->where('active', filter_var($activeFilter, FILTER_VALIDATE_BOOLEAN));
            });

        $totalRecords    = LeaveType::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int)$orderIdx) {
            1 => 'name',
            2 => 'code',
            3 => 'days_allowed',
            4 => 'is_paid',
            5 => 'active',
            default => 'name',
        };

        $query->orderBy($sortColumn, $orderDir);

        $types = $query->offset($start)->limit($length)->get();

        $data = $types->map(function ($type) {
            $activeHtml = $type->active
                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>'
                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Inactive</span>';

            $paidHtml = $type->is_paid
                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Paid</span>'
                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Unpaid</span>';

            $edit_url   = Auth::user()->can('leave-types.update') ? route('leave-types.edit', $type) : null;
            $delete_url = Auth::user()->can('leave-types.delete') ? route('leave-types.destroy', $type) : null;

            return [
                'id'              => $type->id,
                'name'            => $type->name,
                'code'            => $type->code ?? '-',
                'days_allowed'    => $type->days_allowed,
                'paid_html'       => $paidHtml,
                'active_html'     => $activeHtml,
                'edit_url'        => $edit_url,
                'delete_url'      => $delete_url,
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
        if (!Auth::user()->can('leave-types.create')) {
            return redirect()->route('leave-types.index')
                ->with('error', 'Sorry! You are not allowed to create leave types.');
        }

        return view('leave-types.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leave-types.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:100', 'unique:leave_types,name'],
            'code'              => ['nullable', 'string', 'max:10', 'unique:leave_types,code'],
            'description'       => ['nullable', 'string'],
            'days_allowed'      => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid'           => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
            'active'            => ['sometimes', 'boolean'],
        ]);

        LeaveType::create($validated);

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function edit(LeaveType $leaveType)
    {
        if (!Auth::user()->can('leave-types.update')) {
            return redirect()->route('leave-types.index')
                ->with('error', 'Sorry! You are not allowed to edit leave types.');
        }

        return view('leave-types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        if (!Auth::user()->can('leave-types.update')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:100', Rule::unique('leave_types')->ignore($leaveType->id)],
            'code'              => ['nullable', 'string', 'max:10', Rule::unique('leave_types')->ignore($leaveType->id)],
            'description'       => ['nullable', 'string'],
            'days_allowed'      => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid'           => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
            'active'            => ['sometimes', 'boolean'],
        ]);

        $leaveType->update($validated);

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        if (!Auth::user()->can('leave-types.delete')) {
            return redirect()->route('leave-types.index')
                ->with('error', 'Sorry! You are not allowed to delete leave types.');
        }

        if ($leaveType->requests()->exists() || $leaveType->entitlements()->exists()) {
            return back()->with('error', 'Cannot delete: Leave type is in use.');
        }

        $leaveType->delete();

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
}