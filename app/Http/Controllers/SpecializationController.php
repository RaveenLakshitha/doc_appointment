<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SpecializationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:specializations.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:specializations.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:specializations.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:specializations.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
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
            ->withCount([
                'doctors as doctors_count' => fn($q) => $q->where('is_active', true)
            ]);

        return DataTables::of($query)
            ->addColumn('department_name', fn($row) => $row->department?->name ?? '—')
            ->addColumn('delete_url', fn($row) =>
                Auth::user()->can('specializations.delete')
                    ? route('specializations.destroy', $row)
                    : null
            )
            ->addColumn('edit_url', fn($row) =>
                Auth::user()->can('specializations.edit') ? true : null
            )
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

        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get();

        return view('specializations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('specializations.create')) {
            abort(403, __('file.unauthorized_action'));
        }

        $validated = $request->validate([
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('specializations', 'name')->whereNull('deleted_at'),
            ],
            'description'   => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
        ]);

        $specialization = Specialization::withTrashed()->where('name', $request->name)->first();
        if ($specialization && $specialization->trashed()) {
            $specialization->restore();
            $specialization->update($validated);
        } else {
            Specialization::create($validated);
        }

        return redirect()->route('specializations.index')
            ->with('success', __('file.specialization_created_successfully'));
    }

    public function edit(Specialization $specialization)
    {
        if (!Auth::user()->can('specializations.edit')) {
            return redirect()->route('specializations.index')
                ->with('error', __('file.specializations_edit_denied'));
        }

        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get();

        return view('specializations.edit', compact('specialization', 'departments'));
    }

    public function update(Request $request, Specialization $specialization)
    {
        if (!Auth::user()->can('specializations.edit')) {
            return response()->json([
                'success' => false,
                'message' => __('file.unauthorized_action'),
            ], 403);
        }

        $validated = $request->validate([
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('specializations', 'name')->ignore($specialization->id)->whereNull('deleted_at'),
            ],
            'description'   => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
        ]);

        $specialization->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.specialization_updated_successfully'),
        ]);
    }

    public function destroy(Specialization $specialization)
    {
        if (!Auth::user()->can('specializations.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.specializations_delete_denied')], 403);
            }
            return back()->with('error', __('file.specializations_delete_denied'));
        }

        if ($specialization->doctors()->where('doctors.is_active', true)->exists()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_delete_specialization_with_doctors')], 422);
            }
            return back()->with('error', __('file.cannot_delete_specialization_with_doctors'));
        }

        $specialization->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.specialization_deleted_successfully')]);
        }
        return back()->with('success', __('file.specialization_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('specializations.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.specializations_bulk_delete_denied'),
            ], 403);
        }

        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids ?? '')));
        }

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_items_selected')], 400);
        }

        $validator = Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:specializations,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => __('file.validation_failed'), 'errors' => $validator->errors()], 422);
        }

        $deletedCount = Specialization::whereIn('id', $ids)
            ->whereDoesntHave('doctors', function ($q) {
                $q->where('doctors.is_active', true);
            })
            ->delete();

        $message = $deletedCount > 0
            ? __(':count specializations deleted successfully.', ['count' => $deletedCount])
            : __('file.no_specializations_deleted_or_not_allowed');

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted' => $deletedCount,
        ]);
    }
}