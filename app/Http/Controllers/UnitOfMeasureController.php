<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UnitOfMeasureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:unit-of-measures.index', ['only' => ['index', 'datatable']]);
        $this->middleware('permission:unit-of-measures.create', ['only' => ['store']]);
        $this->middleware('permission:unit-of-measures.edit', ['only' => ['update']]);
        $this->middleware('permission:unit-of-measures.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('unit-of-measures.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('unit-of-measures.index');
    }

    public function datatable(Request $request)
    {
        $query = UnitOfMeasure::query();

        return DataTables::of($query)
            ->addColumn('delete_url', fn($row) => Auth::user()->can('unit-of-measures.delete') ? route('unit-of-measures.destroy', $row) : null)
            ->addColumn('edit_url', fn($row) => Auth::user()->can('unit-of-measures.edit') ? true : null)
            ->editColumn('name', fn($row) => $row->name ?? '-')
            ->editColumn('abbreviation', fn($row) => $row->abbreviation ?? '—')
            ->editColumn('display_name', fn($row) => $row->display_name)
            ->editColumn('status_html', fn($row) => $row->is_active
                ? '<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">' . __('file.active') . '</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">' . __('file.inactive') . '</span>')
            ->addColumn('is_active', fn($row) => $row->is_active)
            ->rawColumns(['status_html'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('unit-of-measures.create')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('unit_of_measures', 'name')->whereNull('deleted_at'),
            ],
            'abbreviation' => 'nullable|string|max:50',
            'is_active'    => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $unit = UnitOfMeasure::withTrashed()->where('name', $request->name)->first();
        if ($unit && $unit->trashed()) {
            $unit->restore();
            $unit->update($validated);
        } else {
            UnitOfMeasure::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => __('file.unit_created_successfully')
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('unit-of-measures.edit')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $unitOfMeasure = UnitOfMeasure::findOrFail($id);

        $validated = $request->validate([
            'name'         => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('unit_of_measures')->ignore($unitOfMeasure->id)->whereNull('deleted_at')
            ],
            'abbreviation' => 'nullable|string|max:50',
            'is_active'    => 'sometimes|boolean',
        ]);

        $unitOfMeasure->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.unit_updated_successfully')
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('unit-of-measures.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.unauthorized')
            ], 403);
        }

        $unitOfMeasure = UnitOfMeasure::findOrFail($id);
        $unitOfMeasure->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.unit_deleted_successfully')
        ]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('unit-of-measures.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.unauthorized')
            ], 403);
        }

        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_units_selected')
            ]);
        }

        UnitOfMeasure::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.units_bulk_deleted_successfully')
        ]);
    }
}