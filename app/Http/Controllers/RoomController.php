<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('rooms.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('rooms.index');
    }

    public function datatable(Request $request)
    {
        $query = Room::with('department');

        return DataTables::of($query)
            ->addColumn('department_name', fn($row) => $row->department?->name ?? '—')
            ->addColumn('facilities_list', fn($row) => $row->facilities_list)
            ->editColumn('is_active', fn($row) => (bool) $row->is_active)
            ->editColumn('description', fn($row) => $row->description ?? '—')
            ->editColumn('room_number', fn($row) => $row->room_number ?? '—')
            ->editColumn('floor', fn($row) => $row->floor ?? '—')
            ->editColumn('capacity', fn($row) => $row->capacity ?? '—')
            ->addColumn('delete_url', fn($row) => 
                Auth::user()->can('rooms.delete') ? route('rooms.destroy', $row) : null
            )
            ->addColumn('edit_url', fn($row) => 
                Auth::user()->can('rooms.edit') ? route('rooms.edit', $row) : null
            )
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->can('rooms.create')) {
            return redirect()->route('rooms.index')
                ->with('error', __('file.module_access_denied'));
        }

        $departments = Department::active()->orderBy('name')->get();

        return view('rooms.create', compact('departments'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('rooms.create')) {
            return response()->json([
                'success' => false,
                'message' => __('file.module_access_denied')
            ], 403);
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms', 'name')->whereNull('deleted_at'),
            ],
            'room_number'   => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_number')->whereNull('deleted_at'),
            ],
            'floor'         => 'nullable|string|max:50',
            'capacity'      => 'nullable|integer|min:1',
            'is_active'     => 'required|boolean',
            'description'   => 'nullable|string',
            'facilities'    => 'nullable|array',
            'facilities.*'  => 'string|in:wifi,air_conditioning,television,telephone,wheelchair_accessible,attached_bathroom,oxygen_supply,nurse_call_button',
        ]);

        Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.room_created_successfully')
        ]);
    }

    public function edit(Room $room)
    {
        if (!Auth::user()->can('rooms.edit')) {
            return redirect()->route('rooms.index')
                ->with('error', __('file.module_access_denied'));
        }

        $departments = Department::active()->orderBy('name')->get();

        return view('rooms.edit', compact('room', 'departments'));
    }

    public function update(Request $request, Room $room)
    {
        if (!Auth::user()->can('rooms.edit')) {
            return response()->json([
                'success' => false,
                'message' => __('file.module_access_denied')
            ], 403);
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'room_number'   => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'floor'         => 'nullable|string|max:50',
            'capacity'      => 'nullable|integer|min:1',
            'is_active'     => 'required|boolean',
            'description'   => 'nullable|string',
            'facilities'    => 'nullable|array',
            'facilities.*'  => 'string|in:wifi,air_conditioning,television,telephone,wheelchair_accessible,attached_bathroom,oxygen_supply,nurse_call_button',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.room_updated_successfully')
        ]);
    }

    public function destroy(Room $room)
    {
        if (!Auth::user()->can('rooms.delete')) {
            return redirect()->route('rooms.index')
                ->with('error', __('file.module_access_denied'));
        }

        if ($room->trashed()) {
            return redirect()->route('rooms.index')
                ->with('info', __('file.room_already_deleted'));
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', __('file.room_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('rooms.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.module_access_denied')
            ], 403);
        }

        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_items_selected')
            ]);
        }

        $idsArray = array_filter(explode(',', $ids), 'is_numeric');

        if (empty($idsArray)) {
            return response()->json([
                'success' => false,
                'message' => __('file.invalid_selection')
            ]);
        }

        $deletedCount = Room::whereIn('id', $idsArray)
            ->whereNull('deleted_at')
            ->delete();

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_rooms_deleted')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => trans_choice('file.rooms_deleted_successfully', $deletedCount, ['count' => $deletedCount])
        ]);
    }
}