<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services (paginated – optional fallback)
     */
    public function index(Request $request)
    {
        $services = Service::with('department')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('services.index', compact('services'));
    }

    /**
     * DataTable AJAX endpoint
     */
    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $search = trim($request->input('search.value', ''));

        $query = Service::query()
            ->with('department')
            ->select('services.*')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('services.name', 'like', "%{$search}%")
                  ->orWhere('services.type', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            })
            ->where('services.is_active', true);

        $totalRecords = Service::where('is_active', true)->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        if ($orderColumnIndex == 0) {
            $query->orderBy('name', $orderDir);
        } elseif ($orderColumnIndex == 1) {
            $query->join('departments', 'services.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $orderDir);
        } elseif ($orderColumnIndex == 2) {
            $query->orderBy('type', $orderDir);
        } elseif ($orderColumnIndex == 3) {
            $query->orderBy('duration_minutes', $orderDir);
        } elseif ($orderColumnIndex == 4) {
            $query->orderBy('price', $orderDir);
        } else {
            $query->orderBy('name', 'asc');
        }

        $services = $query->offset($start)->limit($length)->get();

        $data = $services->map(function ($service) {
            $typeBadge = match ($service->type) {
                'Diagnostic'    => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">Diagnostic</span>',
                'Therapeutic'   => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Therapeutic</span>',
                'Consultation'  => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">Consultation</span>',
                default         => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Other</span>',
            };

            return [
                'id'                => $service->id,
                'name'              => $service->name,
                'department_name'   => $service->department?->name ?? '-',
                'type'              => $typeBadge,
                'duration_minutes'  => $service->duration_minutes,
                'price'             => $service->price,
                'show_url'          => route('services.show', $service),
                'edit_url'          => route('services.edit', $service),
                'delete_url'        => route('services.destroy', $service),
            ];
        });

        return response()->json([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $equipment   = Equipment::orderBy('name')->get();

        return view('services.create', compact('departments', 'equipment'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255|unique:services,name',
            'department_id'       => 'required|exists:departments,id',
            'type'                => 'required|in:Diagnostic,Therapeutic,Consultation,Other',
            'duration_minutes'    => 'required|integer|min:5|max:480',
            'price'               => 'required|numeric|min:0',
            'description'         => 'nullable|string',
            'patient_preparation' => 'nullable|string',
            'requires_insurance'  => 'sometimes|boolean',
            'requires_referral'   => 'sometimes|boolean',

            // Equipment
            'equipment'           => 'nullable|array',
            'equipment.*'         => 'exists:equipment,id',

            // Availability slots
            'slots'               => 'nullable|array',
            'slots.*.day'         => 'required_with:slots|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'slots.*.start_time'  => 'required_with:slots|date_format:H:i',
            'slots.*.end_time'    => 'required_with:slots|date_format:H:i|after:slots.*.start_time',
        ]);

        $service = Service::create([
            'name'                => $validated['name'],
            'department_id'       => $validated['department_id'],
            'type'                => $validated['type'],
            'duration_minutes'    => $validated['duration_minutes'],
            'price'               => $validated['price'],
            'description'         => $validated['description'] ?? null,
            'patient_preparation' => $validated['patient_preparation'] ?? null,
            'requires_insurance'  => $validated['requires_insurance'] ?? false,
            'requires_referral'   => $validated['requires_referral'] ?? false,
            'is_active'           => true,
        ]);

        // Sync equipment
        $service->equipment()->sync($validated['equipment'] ?? []);

        // Create availability slots
        if (!empty($validated['slots'])) {
            foreach ($validated['slots'] as $slot) {
                $service->availabilitySlots()->create([
                    'day_of_week' => $slot['day'],
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }

        return redirect()->route('services.index')
                         ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $service->load(['department', 'doctors', 'equipment', 'availabilitySlots']);
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the service.
     */
    public function edit(Service $service)
    {
        $departments = Department::orderBy('name')->get();
        $equipment   = Equipment::orderBy('name')->get();

        return view('services.edit', compact('service', 'departments', 'equipment'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255|unique:services,name,' . $service->id,
            'department_id'       => 'required|exists:departments,id',
            'type'                => 'required|in:Diagnostic,Therapeutic,Consultation,Other',
            'duration_minutes'    => 'required|integer|min:5|max:480',
            'price'               => 'required|numeric|min:0',
            'description'         => 'nullable|string',
            'patient_preparation' => 'nullable|string',
            'requires_insurance'  => 'sometimes|boolean',
            'requires_referral'   => 'sometimes|boolean',
            'is_active'           => 'sometimes|boolean',

            // Equipment
            'equipment'           => 'nullable|array',
            'equipment.*'         => 'exists:equipment,id',

            // Availability slots
            'slots'               => 'nullable|array',
            'slots.*.day'         => 'required_with:slots|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'slots.*.start_time'  => 'required_with:slots|date_format:H:i',
            'slots.*.end_time'    => 'required_with:slots|date_format:H:i|after:slots.*.start_time',
        ]);

        $service->update([
            'name'                => $validated['name'],
            'department_id'       => $validated['department_id'],
            'type'                => $validated['type'],
            'duration_minutes'    => $validated['duration_minutes'],
            'price'               => $validated['price'],
            'description'         => $validated['description'] ?? null,
            'patient_preparation' => $validated['patient_preparation'] ?? null,
            'requires_insurance'  => $validated['requires_insurance'] ?? $service->requires_insurance,
            'requires_referral'   => $validated['requires_referral'] ?? $service->requires_referral,
            'is_active'           => $validated['is_active'] ?? $service->is_active,
        ]);

        // Sync equipment
        $service->equipment()->sync($validated['equipment'] ?? []);

        // Replace all availability slots (delete old, create new)
        $service->availabilitySlots()->delete();

        if (!empty($validated['slots'])) {
            foreach ($validated['slots'] as $slot) {
                $service->availabilitySlots()->create([
                    'day_of_week' => $slot['day'],
                    'start_time'  => $slot['start_time'],
                    'end_time'    => $slot['end_time'],
                ]);
            }
        }

        return redirect()->route('services.index')
                         ->with('success', 'Service updated successfully.');
    }

    /**
     * Deactivate (soft delete) the service.
     */
    public function destroy(Service $service)
    {
        $service->update(['is_active' => false]);

        return back()->with('success', 'Service deactivated successfully.');
    }

    /**
     * Bulk deactivate services.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        Service::whereIn('id', $ids)->update(['is_active' => false]);

        return back()->with('success', 'Selected services deactivated.');
    }
}