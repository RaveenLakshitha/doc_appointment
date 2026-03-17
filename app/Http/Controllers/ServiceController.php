<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:services.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:services.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:services.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:services.delete', ['only' => ['destroy', 'bulkDelete']]);
    }

    /**
     * Display a listing of services (paginated – optional fallback)
     */
    public function index(Request $request)
    {
        $services = Service::with('department')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('services.index', compact('services'));
    }

    /**
     * DataTable AJAX endpoint
     */
    public function datatable(Request $request)
    {
        try {
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
                });

            $totalRecords = Service::count();
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
                    'Diagnostic' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">' . __('file.diagnostic') . '</span>',
                    'Therapeutic' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">' . __('file.therapeutic') . '</span>',
                    'Consultation' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">' . __('file.consultation') . '</span>',
                    default => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">' . __('file.other') . '</span>',
                };

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'department_name' => $service->department?->name ?? '-',
                    'type' => $typeBadge,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                    'show_url' => route('services.show', $service),
                    'edit_url' => \Auth::user()->can('services.edit') ? route('services.edit', $service) : null,
                    'delete_url' => \Auth::user()->can('services.delete') ? route('services.destroy', $service) : null,
                ];
            });

            \Log::info('Services datatable success', [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'row_count' => count($data),
            ]);

            return response()->json([
                'draw' => (int) $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data->toArray(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Services datatable failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'error' => __('file.server_error'),
                'message' => $e->getMessage(), // only for development – remove in production
            ], 500);
        }
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $equipment = Equipment::orderBy('name')->get();

        return view('services.create', compact('departments', 'equipment'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('services', 'name')->whereNull('deleted_at')
            ],
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|in:Diagnostic,Therapeutic,Consultation,Other',
            'duration_minutes' => 'nullable|integer|min:5|max:480',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'patient_preparation' => 'nullable|string',
            'requires_insurance' => 'sometimes|boolean',
            'requires_referral' => 'sometimes|boolean',

            // Equipment
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id',
        ]);

        $serviceData = [
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'type' => $validated['type'],
            'duration_minutes' => $validated['duration_minutes'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'patient_preparation' => $validated['patient_preparation'] ?? null,
            'requires_insurance' => $validated['requires_insurance'] ?? false,
            'requires_referral' => $validated['requires_referral'] ?? false,
            'is_active' => true,
        ];

        $service = Service::withTrashed()->where('name', $request->name)->first();
        if ($service && $service->trashed()) {
            $service->restore();
            $service->update($serviceData);
        } else {
            $service = Service::create($serviceData);
        }

        // Sync equipment
        $service->equipment()->sync($validated['equipment'] ?? []);

        return redirect()->route('services.index')
            ->with('success', __('file.service_created_successfully'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $service->load(['department', 'doctors', 'equipment']);
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the service.
     */
    public function edit(Service $service)
    {
        $departments = Department::orderBy('name')->get();
        $equipment = Equipment::orderBy('name')->get();

        return view('services.edit', compact('service', 'departments', 'equipment'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('services', 'name')->ignore($service->id)->whereNull('deleted_at')
            ],
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|in:Diagnostic,Therapeutic,Consultation,Other',
            'duration_minutes' => 'nullable|integer|min:5|max:480',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'patient_preparation' => 'nullable|string',
            'requires_insurance' => 'sometimes|boolean',
            'requires_referral' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',

            // Equipment
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id',
        ]);

        $service->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'type' => $validated['type'],
            'duration_minutes' => $validated['duration_minutes'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'patient_preparation' => $validated['patient_preparation'] ?? null,
            'requires_insurance' => $validated['requires_insurance'] ?? $service->requires_insurance,
            'requires_referral' => $validated['requires_referral'] ?? $service->requires_referral,
            'is_active' => $validated['is_active'] ?? $service->is_active,
        ]);

        // Sync equipment
        $service->equipment()->sync($validated['equipment'] ?? []);

        return redirect()->route('services.index')
            ->with('success', __('file.service_updated_successfully'));
    }

    /**
     * Deactivate (soft delete) the service.
     */
    public function destroy(Service $service)
    {
        if (!\Auth::user()->can('services.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return back()->with('error', __('file.permission_denied'));
        }

        $service->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.service_deleted_successfully')]);
        }

        return back()->with('success', __('file.service_deleted_successfully'));
    }

    /**
     * Bulk deactivate services.
     */
    public function bulkDelete(Request $request)
    {
        if (!\Auth::user()->can('services.delete')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return back()->with('error', __('file.permission_denied'));
        }

        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids ?? '')));
        }

        if (!is_array($ids) || empty($ids)) {
            $msg = __('file.no_items_selected');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        $validator = Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            $msg = __('file.validation_failed');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $validator->errors()], 422);
            }
            return back()->with('error', $msg);
        }

        Service::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.selected_services_deleted')]);
        }

        return back()->with('success', __('file.selected_services_deleted'));
    }
}