<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:departments.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:departments.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:departments.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:departments.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('departments.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('departments.index');
    }

    public function create()
    {
        if (!Auth::user()->can('departments.create')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('departments.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('departments.create')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $validated = $request->validate([
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->whereNull('deleted_at'),
            ],
            'status'            => 'required|in:0,1',
            'location'          => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|min:7|max:15',
            'description'       => 'nullable|string',
        ]);

        $department = Department::withTrashed()->where('name', $request->name)->first();
        if ($department && $department->trashed()) {
            $department->restore();
            $department->update($validated);
        } else {
            Department::create($validated);
        }

        return redirect()->route('departments.index')
            ->with('success', __('file.department_created_successfully', ['name' => $request->name]));
    }

    public function edit(Department $department)
    {
        if (!Auth::user()->can('departments.edit')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $doctors = Doctor::active()
            ->orderByFullName()
            ->get(['id', 'first_name', 'middle_name', 'last_name']);

        return view('departments.edit', compact('department', 'doctors'));
    }

    public function update(Request $request, Department $department)
    {
        if (!Auth::user()->can('departments.edit')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('file.module_access_denied')
                ], 403);
            }

            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $validated = $request->validate([
            'name'           => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($department->id)->whereNull('deleted_at'),
            ],
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|min:7|max:15',
            'location'       => 'nullable|string|max:255',
            'status'         => 'required|in:0,1',
            'description'    => 'nullable|string',
        ]);

        $department->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.department_updated_successfully')
            ]);
        }

        return redirect()->route('departments.index')
            ->with('success', __('file.department_updated_successfully'));
    }

    public function destroy(Department $department)
    {
        if (!Auth::user()->can('departments.delete')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        if ($department->trashed()) {
            return redirect()->route('departments.index')
                ->with('info', __('file.department_already_deleted'));
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', __('file.department_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('departments.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.module_access_denied')
            ], 403);
        }

        $idsInput = $request->input('ids');

        if (empty($idsInput)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_items_selected')
            ]);
        }

        if (is_array($idsInput)) {
            $idsArray = $idsInput;
        } else {
            $idsArray = explode(',', $idsInput);
        }

        $idsArray = array_filter($idsArray, 'is_numeric');

        if (empty($idsArray)) {
            return response()->json([
                'success' => false,
                'message' => __('file.invalid_selection')
            ]);
        }

        $deletedCount = Department::whereIn('id', $idsArray)
            ->whereNull('deleted_at')
            ->delete();

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_departments_deleted')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => trans_choice('file.departments_deleted_successfully', $deletedCount, ['count' => $deletedCount])
        ]);
    }

    public function datatable(Request $request)
    {
        $query = Department::query()
            ->withCount('doctors as staff_count')
            ->withCount('services as specializations_count');

        return DataTables::of($query)
            ->addColumn('edit_url', fn($row) => \Auth::user()->can('departments.edit') ? route('departments.edit', $row) : null)
            ->addColumn('delete_url', fn($row) => \Auth::user()->can('departments.delete') ? route('departments.destroy', $row) : null)
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }
}