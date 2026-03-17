<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct()
    {
        // Adjust permissions if you have a specific permission system
        // $this->middleware('permission:holidays.index', ['only' => ['index', 'datatable']]);
        // $this->middleware('permission:holidays.create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:holidays.edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:holidays.delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('holidays.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $query = Holiday::query();

        if ($searchValue !== '') {
            $query->where('name', 'like', "%{$searchValue}%")
                ->orWhere('description', 'like', "%{$searchValue}%");
        }

        $totalRecords = Holiday::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int) $orderIdx) {
            1 => 'name',
            2 => 'start_date',
            3 => 'end_date',
            4 => 'description',
            default => 'created_at',
        };

        $query->orderBy($sortColumn, $orderDir);
        $holidays = $query->offset($start)->limit($length)->get();

        $data = $holidays->map(function ($holiday) {
            return [
                'id' => $holiday->id,
                'name' => $holiday->name,
                'start_date' => \Carbon\Carbon::parse($holiday->start_date)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($holiday->end_date)->format('Y-m-d'),
                'description' => $holiday->description ?? '-',
                'edit_url' => route('holidays.edit', $holiday->id),
                'delete_url' => route('holidays.destroy', $holiday->id),
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Holiday::create($validated);

        return response()->json(['success' => true, 'message' => __('file.holiday_created_successfully') ?? 'Holiday created successfully.']);
    }

    public function edit(Holiday $holiday)
    {
        return response()->json([
            'id' => $holiday->id,
            'name' => $holiday->name,
            'start_date' => \Carbon\Carbon::parse($holiday->start_date)->format('Y-m-d'),
            'end_date' => \Carbon\Carbon::parse($holiday->end_date)->format('Y-m-d'),
            'description' => $holiday->description,
        ]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $holiday->update($validated);

        return response()->json(['success' => true, 'message' => __('file.holiday_updated_successfully') ?? 'Holiday updated successfully.']);
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return response()->json(['success' => true, 'message' => __('file.holiday_deleted_successfully') ?? 'Holiday deleted successfully.']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (!empty($ids)) {
            Holiday::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => __('file.holidays_deleted_successfully') ?? 'Selected holidays deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => __('file.no_holiday_selected') ?? 'No holidays selected.']);
    }
}
