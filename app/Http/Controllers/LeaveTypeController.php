<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:leave-types.index', ['only' => ['index', 'datatable']]);
        $this->middleware('permission:leave-types.create', ['only' => ['store']]);
        $this->middleware('permission:leave-types.edit', ['only' => ['update']]);
        $this->middleware('permission:leave-types.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('leave-types.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('leave-types.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));
        $activeFilter = $request->input('active');

        $query = LeaveType::query()
            ->select('leave_types.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('code', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            })
            ->when($activeFilter !== null && $activeFilter !== '', function ($q) use ($activeFilter) {
                $q->where('active', filter_var($activeFilter, FILTER_VALIDATE_BOOLEAN));
            });

        $totalRecords = LeaveType::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int) $orderIdx) {
            0 => 'name',
            1 => 'code',
            2 => 'days_allowed',
            3 => 'is_paid',
            4 => 'requires_approval',
            5 => 'active',
            default => 'name',
        };

        $query->orderBy($sortColumn, $orderDir);

        $types = $query->offset($start)->limit($length)->get();

        $data = $types->map(function ($type) {
            $activeHtml = $type->active
                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">' . __('file.active') . '</span>'
                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">' . __('file.inactive') . '</span>';

            $paidHtml = $type->is_paid
                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">' . __('file.paid') . '</span>'
                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">' . __('file.unpaid') . '</span>';

            return [
                'id' => $type->id,
                'name' => $type->name,
                'code' => $type->code ?? '-',
                'days_allowed' => $type->days_allowed,
                'paid_html' => $paidHtml,
                'requires_approval' => $type->requires_approval,
                'active_html' => $activeHtml,
                'is_active' => $type->active,
                'is_paid' => $type->is_paid,
                'description' => $type->description ?? '',
                'delete_url' => route('leave-types.destroy', $type->id),
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leave-types.create')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('leave_types', 'name')->whereNull('deleted_at')
            ],
            'code' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('leave_types', 'code')->whereNull('deleted_at')
            ],
            'description' => ['nullable', 'string'],
            'days_allowed' => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid' => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $leaveType = LeaveType::withTrashed()
            ->where(function ($q) use ($request) {
                $q->where('name', $request->name);
                if ($request->code) {
                    $q->orWhere('code', $request->code);
                }
            })
            ->first();

        if ($leaveType && $leaveType->trashed()) {
            $leaveType->restore();
            $leaveType->update($validated);
        } else {
            LeaveType::create($validated);
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        if (!Auth::user()->can('leave-types.edit')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('leave_types')->ignore($leaveType->id)->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('leave_types')->ignore($leaveType->id)->whereNull('deleted_at')],
            'description' => ['nullable', 'string'],
            'days_allowed' => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid' => ['sometimes', 'boolean'],
            'requires_approval' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $leaveType->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(LeaveType $leaveType)
    {
        if (!Auth::user()->can('leave-types.delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($leaveType->requests()->exists() || $leaveType->entitlements()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('file.leave_type_in_use')
            ], 422);
        }

        $leaveType->delete();

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('leave-types.delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_ids_provided')], 400);
        }

        $idsArray = is_array($ids) ? $ids : explode(',', $ids);

        $types = LeaveType::whereIn('id', $idsArray)->get();
        $deletedCount = 0;
        $activeCount = 0;

        foreach ($types as $type) {
            if ($type->requests()->exists() || $type->entitlements()->exists()) {
                $activeCount++;
                continue;
            }
            $type->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            $message = $deletedCount . ' leave type(s) deleted successfully.';
            if ($activeCount > 0) {
                $message .= ' ' . $activeCount . ' could not be deleted as they are in use.';
            }
            return response()->json(['success' => true, 'message' => $message]);
        }

        return response()->json([
            'success' => false,
            'message' => __('file.leave_type_in_use')
        ], 422);
    }
}