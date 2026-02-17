<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class TreatmentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('treatments.index')) {
            return redirect()->route('home')->with('error', __('file.module_access_denied'));
        }

        return view('treatments.index');
    }

    public function datatable(Request $request)
    {
        $query = Treatment::query()
            ->whereNull('deleted_at')
            ->withCount('appointments as appointment_count');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $edit_url   = Auth::user()->can('treatments.edit') ? route('treatments.edit', $row) : '';
                $delete_url = Auth::user()->can('treatments.delete') ? route('treatments.destroy', $row) : '';

                return compact('edit_url', 'delete_url');
            })
            ->addColumn('delete_url', fn($row) =>
                Auth::user()->can('treatments.delete') ? route('treatments.destroy', $row) : null
            )
            ->editColumn('active', fn($row) => (bool) $row->active)
            ->editColumn('code', fn($row) => $row->code ?? '—')
            ->editColumn('appointment_count', fn($row) => $row->appointment_count ?? 0)
            ->make(true);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('treatments.create')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:treatments,name',
            'active' => 'boolean',
        ]);

        $last = Treatment::latest('id')->first();
        $next = $last ? ((int) substr($last->code ?? 'TRT-000', 4)) + 1 : 1;
        $code = sprintf('TRT-%03d', $next);

        Treatment::create([
            'name'   => $validated['name'],
            'code'   => $code,
            'active' => $request->boolean('active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('file.treatment_created_successfully')
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
        if (!Auth::user()->can('treatments.edit')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name'   => [
                'required',
                'string',
                'max:255',
                Rule::unique('treatments', 'name')->ignore($treatment->id),
            ],
            'active' => 'boolean',
        ]);

        $treatment->update([
            'name'   => $validated['name'],
            'active' => $request->boolean('active', $treatment->active),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('file.treatment_updated_successfully')
        ]);
    }

    public function destroy(Treatment $treatment)
    {
        if (!Auth::user()->can('treatments.delete')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $treatment->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.treatment_deleted_successfully')
        ]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('treatments.delete')) {
            return response()->json(['success' => false, 'message' => __('file.unauthorized')], 403);
        }

        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = array_filter(explode(',', $request->ids));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 422);
        }

        Treatment::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.selected_treatments_deleted')
        ]);
    }
}