<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SpecializationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('specializations.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('specializations.index');
    }

    public function datatable(Request $request)
    {
        $query = Specialization::query()
            ->with('department')
            ->withCount(['doctors as doctors_count' => fn($q) => $q->where('is_active', true)]);

        return DataTables::of($query)
            ->addColumn('department_name', fn($row) => $row->department?->name ?? '-')
            ->addColumn('delete_url', fn($row) => Auth::user()->can('specializations.delete') 
                ? route('specializations.destroy', $row) 
                : null)
            ->editColumn('description', fn($row) => $row->description ?? '')
            ->editColumn('doctors_count', fn($row) => (int) $row->doctors_count)
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->can('specializations.create')) {
            return redirect()->route('specializations.index')
                ->with('error', __('file.specializations_create_denied'));
        }

        $departments = Department::where('status', true)->orderBy('name')->get();

        return view('specializations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('specializations.create')) {
            abort(403);
        }

        $request->validate([
            'name'          => 'required|string|max:255|unique:specializations,name',
            'description'   => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
        ]);

        Specialization::create($request->only('name', 'description', 'department_id'));

        return redirect()->route('specializations.index')
            ->with('success', __('file.specialization_created_successfully'));
    }

    public function edit(Specialization $specialization)
    {
        if (!Auth::user()->can('specializations.edit')) {
            return redirect()->route('specializations.index')
                ->with('error', __('file.specializations_edit_denied'));
        }

        $departments = Department::where('status', true)->orderBy('name')->get();

        return view('specializations.edit', compact('specialization', 'departments'));
    }

    public function update(Request $request, Specialization $specialization)
    {
        // if (!Auth::user()->can('specializations.edit')) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Permission denied: specializations.edit',
        //         'user_id' => Auth::id(),
        //         'permissions' => Auth::user()->getAllPermissions()->pluck('name')->toArray(),
        //     ], 403);
        // }


        // $request->validate([
        //     'name'          => 'required|string|max:255|unique:specializations,name,' . $specialization->id,
        //     'description'   => 'nullable|string|max:2000',
        //     'department_id' => 'required|exists:departments,id',
        // ]);

        $specialization->update($request->only('name', 'description', 'department_id'));

        return response()->json([
            'success' => true,
            'message' => __('file.specialization_updated_successfully')
        ]);
    }

    public function destroy(Specialization $specialization)
    {
        if (!Auth::user()->can('specializations.delete')) {
            return back()->with('error', __('file.specializations_delete_denied'));
        }

        if ($specialization->doctors()->exists()) {
            return back()->with('error', __('file.cannot_delete_specialization_with_doctors'));
        }

        $specialization->delete();

        return back()->with('success', __('file.specialization_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('specializations.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.specializations_bulk_delete_denied')
            ], 403);
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:specializations,id'
        ]);

        $deleted = Specialization::whereIn('id', $ids)
            ->whereDoesntHave('doctors')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? __(':count specializations deleted successfully.', ['count' => $deleted])
                : __('file.no_specializations_deleted')
        ]);
    }
}