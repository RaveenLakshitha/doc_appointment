<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments.index');
    }

    public function datatable(Request $request)
    {
        $query = Department::query()
            ->withCount([
                'specializations as specializations_count',
                'doctors as staff_count' => fn($q) => $q->where('is_active', true)
            ]);

        return DataTables::of($query)
            ->addColumn('delete_url', fn($row) => route('departments.destroy', $row))
            ->editColumn('status', fn($row) => (bool) $row->status)
            ->editColumn('description', fn($row) => $row->description ?? '')
            ->editColumn('email', fn($row) => $row->email ?? '')
            ->editColumn('phone', fn($row) => $row->phone ?? '')
            ->editColumn('location', fn($row) => $row->location ?? '')
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Department::create($request->only([
            'name', 'location', 'status', 'email', 'phone', 'description'
        ]));

        return redirect()->route('departments.index')
            ->with('success', __('file.department_created'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $department->update($request->only([
            'name', 'location', 'status', 'email', 'phone', 'description'
        ]));

        return response()->json([
            'success' => true,
            'message' => __('file.department_updated')
        ]);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.department_deleted')
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:departments,id'
        ]);

        $deleted = Department::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? __(':count departments deleted.', ['count' => $deleted])
                : __('file.no_departments_deleted')
        ]);
    }
}