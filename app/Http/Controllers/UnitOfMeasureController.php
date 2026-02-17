<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UnitOfMeasureController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('unit-measures.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('unit-of-measures.index');
    }

    public function datatable(Request $request)
    {
        $query = UnitOfMeasure::query();

        return DataTables::of($query)
            ->addColumn('delete_url', fn($row) => route('unit-of-measures.destroy', $row))
            ->editColumn('name', fn($row) => $row->name ?? '-')
            ->editColumn('abbreviation', fn($row) => $row->abbreviation ?? '—')
            ->editColumn('display_name', fn($row) => $row->display_name)
            ->editColumn('status_html', fn($row) => $row->is_active
                ? '<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactive</span>')
            ->addColumn('is_active', fn($row) => $row->is_active)
            ->rawColumns(['status_html'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('unit-measures.create')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:unit_of_measures,name',
            'abbreviation' => 'nullable|string|max:50',
            'is_active'    => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        UnitOfMeasure::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.unit_created_successfully')
        ]);
    }

    public function update(Request $request, UnitOfMeasure $unitOfMeasure)
    {
        if (!Auth::user()->can('unit-measures.edit')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255', Rule::unique('unit_of_measures')->ignore($unitOfMeasure->id)],
            'abbreviation' => 'nullable|string|max:50',
            'is_active'    => 'sometimes|boolean',
        ]);

        $unitOfMeasure->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.unit_updated_successfully')
        ]);
    }

    public function destroy(UnitOfMeasure $unitOfMeasure)
    {
        if (!Auth::user()->can('unit-measures.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.unauthorized')
            ], 403);
        }

        $unitOfMeasure->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.unit_deleted_successfully')
        ]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('unit-measures.delete')) {
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