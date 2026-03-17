<?php

namespace App\Http\Controllers;

use App\Models\AgeGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AgeGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:age-groups.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:age-groups.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:age-groups.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:age-groups.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('age-groups.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('age-groups.index');
    }

    public function datatable(Request $request)
    {
        $query = AgeGroup::query()
            ->withCount(['doctors as doctors_count' => fn($q) => $q->where('is_active', true)]);

        return DataTables::of($query)
            ->addColumn('delete_url', fn($row) => Auth::user()->can('age-groups.delete') 
                ? route('age-groups.destroy', $row) 
                : null)
            ->addColumn('can_edit', fn($row) => Auth::user()->can('age-groups.edit'))
            ->editColumn('description', fn($row) => $row->description ?? '')
            ->editColumn('age_range', fn($row) => $row->min_age !== null || $row->max_age !== null 
                ? ($row->min_age ?? '0') . ' – ' . ($row->max_age ?? '∞') 
                : '-')
            ->editColumn('doctors_count', fn($row) => (int) $row->doctors_count)
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->can('age-groups.create')) {
            return redirect()->route('age-groups.index')
                ->with('error', __('file.age_groups_create_denied'));
        }

        return view('age-groups.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('age-groups.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('age_groups', 'name')->whereNull('deleted_at'),
            ],
            'min_age'     => 'nullable|integer|min:0',
            'max_age'     => 'nullable|integer|gte:min_age',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
        ]);

        $ageGroup = AgeGroup::withTrashed()->where('name', $request->name)->first();
        if ($ageGroup && $ageGroup->trashed()) {
            $ageGroup->restore();
            $ageGroup->update($validated);
        } else {
            AgeGroup::create($validated);
        }

        return redirect()->route('age-groups.index')
            ->with('success', __('file.age_group_created_successfully'));
    }

    public function edit(AgeGroup $ageGroup)
    {
        if (!Auth::user()->can('age-groups.edit')) {
            return redirect()->route('age-groups.index')
                ->with('error', __('file.age_groups_edit_denied'));
        }

        return view('age-groups.edit', compact('ageGroup'));
    }

    public function update(Request $request, AgeGroup $ageGroup)
    {
        $request->validate([
            'name'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('age_groups', 'name')->ignore($ageGroup->id)->whereNull('deleted_at'),
            ],
            'min_age'     => 'nullable|integer|min:0',
            'max_age'     => 'nullable|integer|gte:min_age',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
        ]);

        $ageGroup->update($request->only('name', 'min_age', 'max_age', 'description', 'is_active'));

        return response()->json([
            'success' => true,
            'message' => __('file.age_group_updated_successfully')
        ]);
    }

    public function destroy(AgeGroup $ageGroup)
    {
        if (!Auth::user()->can('age-groups.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.age_groups_delete_denied')], 403);
            }
            return back()->with('error', __('file.age_groups_delete_denied'));
        }

        if ($ageGroup->doctors()->where('doctors.is_active', true)->exists()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_delete_age_group_with_doctors')], 422);
            }
            return back()->with('error', __('file.cannot_delete_age_group_with_doctors'));
        }

        $ageGroup->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.age_group_deleted_successfully')]);
        }
        return back()->with('success', __('file.age_group_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('age-groups.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.age_groups_bulk_delete_denied')
            ], 403);
        }

        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids ?? '')));
        }

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_ids_provided'),
                'errors' => ['ids' => [__('file.no_ids_provided')]]
            ], 422);
        }

        $validator = Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:age_groups,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('file.validation_failed'),
                'errors'  => $validator->errors()
            ], 422);
        }

        $deleted = AgeGroup::whereIn('id', $ids)
            ->whereDoesntHave('doctors', function ($q) {
                $q->where('doctors.is_active', true);
            })
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? __(':count age groups deleted successfully.', ['count' => $deleted])
                : __('file.no_age_groups_deleted')
        ]);
    }
}