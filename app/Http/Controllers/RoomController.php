<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index');
    }

    public function datatable(Request $request)
    {
        $query = Room::query()->with('department');

        return DataTables::of($query)
            ->addColumn('delete_url', fn($row) => route('rooms.destroy', $row))
            ->editColumn('status', fn($row) => (bool) $row->is_active)
            ->editColumn('description', fn($row) => $row->description ?? '')
            ->editColumn('room_number', fn($row) => $row->room_number ?? '')
            ->editColumn('floor', fn($row) => $row->floor ?? '')
            ->editColumn('capacity', fn($row) => $row->capacity ?? '')
            ->addColumn('department_name', fn($row) => $row->department?->name ?? '—')
            ->addColumn('facilities_list', fn($row) => $row->facilities_list)
            ->rawColumns([])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:rooms,name',
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'floor' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|in:wifi,air_conditioning,television,telephone,wheelchair_accessible,attached_bathroom,oxygen_supply,nurse_call_button',
        ]);

        Room::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'is_active' => $request->status,
            'facilities' => $request->facilities ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('file.room_created')
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'floor' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|in:wifi,air_conditioning,television,telephone,wheelchair_accessible,attached_bathroom,oxygen_supply,nurse_call_button',
        ]);

        $room->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'is_active' => $request->status,
            'facilities' => $request->facilities ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('file.room_updated')
        ]);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.room_deleted')
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
            'ids.*' => 'exists:rooms,id'
        ]);

        $deleted = Room::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? __(':count rooms deleted.', ['count' => $deleted])
                : __('file.no_rooms_deleted')
        ]);
    }
}