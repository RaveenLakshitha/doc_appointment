<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SpecializationController extends Controller
{
    public function index()
    {
        return view('specializations.index');
    }

    public function datatable(Request $request)
    {
        $query = Specialization::query()
            ->with('department')
            ->withCount(['doctors as doctors_count' => fn($q) => $q->where('is_active', true)]);

        return DataTables::of($query)
            ->addColumn('department_name', fn($row) => $row->department?->name ?? '-')
            ->addColumn('department', fn($row) => $row->department) // Required for drawer
            ->addColumn('delete_url', fn($row) => route('specializations.destroy', $row))
            ->editColumn('description', fn($row) => $row->description ?? '')
            ->editColumn('doctors_count', fn($row) => (int) $row->doctors_count)
            ->rawColumns(['description'])
            ->make(true);
    }

    public function create()
    {
        $departments = Department::where('status', true)->orderBy('name')->get();
        return view('specializations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name',
            'description' => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
        ]);

        Specialization::create($request->only('name', 'description', 'department_id'));

        return redirect()->route('specializations.index')
            ->with('success', __('file.specialization_created'));
    }

    public function destroy(Specialization $specialization)
    {
        if ($specialization->doctors()->exists()) {
            return back()->with('error', __('file.cannot_delete_specialization_with_doctors'));
        }

        $specialization->delete();

        return back()->with('success', __('file.specialization_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:specializations,id'
        ]);

        $deleted = Specialization::whereIn('id', $ids)
            ->whereDoesntHave('doctors')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? __(':count specializations deleted.', ['count' => $deleted])
                : __('file.no_specializations_deleted')
        ]);
    }
}