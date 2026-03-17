<?php

namespace App\Http\Controllers;

use App\Models\MedicineTemplate;
use App\Models\InventoryItem;
use App\Models\OptionList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:medicine-templates.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:medicine-templates.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:medicine-templates.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:medicine-templates.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
        if (!Auth::user()->can('medicine-templates.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $templates = MedicineTemplate::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('category', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('medicine_templates.index', compact('templates'));
    }

    public function datatable(Request $request)
    {
        if (!Auth::user()->can('medicine-templates.index')) {
            return response()->json(['error' => __('file.module_access_denied')], 403);
        }

        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = trim($request->input('search.value', ''));
        $category = $request->input('category');

        $query = MedicineTemplate::query()
            ->withCount('medications')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category !== null && $category !== '', function ($q) use ($category) {
                $q->where('category', 'like', "%{$category}%");
            });

        $totalRecords = MedicineTemplate::count();
        $filteredRecords = (clone $query)->count();

        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'name', 'category', 'description', 'medications_count'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $templates = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $templates->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'category' => $template->category ?? '-',
                'description' => $template->description ? Str::limit($template->description, 60) : '-',
                'medications_count' => $template->medications_count,
                'show_url' => route('medicine-templates.show', $template),
                'edit_url' => \Auth::user()->can('medicine-templates.edit') ? route('medicine-templates.edit', $template) : null,
                'delete_url' => \Auth::user()->can('medicine-templates.delete') ? route('medicine-templates.destroy', $template) : null,
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function getMedications($id)
    {
        $template = MedicineTemplate::with('medications')->find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $meds = $template->medications->map(fn($m) => [
            'name' => $m->name ?? $m->inventoryItem?->generic_name ?? $m->inventoryItem?->name ?? '(unnamed)',
            'dosage' => $m->dosage ?? '',
            'route' => $m->route ?? '',
            'frequency' => $m->frequency ?? '',
            'per_day' => $m->per_day ?? 1,
            'duration_days' => $m->duration_days ?? null,
        ]);

        return response()->json($meds->toArray());
    }

    public function create()
    {
        if (!Auth::user()->can('medicine-templates.create')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $medicines = InventoryItem::query()
            ->orderBy('generic_name')
            ->get(['id', 'name', 'generic_name', 'dosage'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => trim($item->generic_name ?: $item->name),
                    'dosage' => $item->dosage ?? '',
                ];
            })
            ->values();

        $routes = OptionList::where('type', 'medication_route')
            ->where('status', true)
            ->orderBy('order')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->prepend('Select Route', '')
            ->toArray();

        return view('medicine_templates.create', compact('medicines', 'routes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('medicine-templates.create')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $template = MedicineTemplate::create($request->only(['name', 'category', 'description']));

            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $med) {
                    if (!empty($med['inventory_item_id']) || !empty($med['name'])) {
                        $template->medications()->create([
                            'inventory_item_id' => $med['inventory_item_id'] ?? null,
                            'name' => $med['name'] ?? null,
                            'dosage' => $med['dosage'] ?? null,
                            'route' => $med['route'] ?? null,
                            'frequency' => $med['frequency'] ?? null,
                            'instructions' => $med['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('medicine-templates.index')
            ->with('success', __('file.medicine_template_created'));
    }

    public function show(MedicineTemplate $medicineTemplate)
    {
        if (!Auth::user()->can('medicine-templates.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $medicineTemplate->load('medications.inventoryItem');
        return view('medicine_templates.show', compact('medicineTemplate'));
    }

    public function edit(MedicineTemplate $medicineTemplate)
    {
        if (!Auth::user()->can('medicine-templates.edit')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $medicineTemplate->load('medications');

        $medicines = InventoryItem::query()
            ->orderBy('generic_name')
            ->get(['id', 'name', 'generic_name', 'dosage'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => trim($item->generic_name ?: $item->name),
                    'dosage' => $item->dosage ?? '',
                ];
            })
            ->values();

        $routes = OptionList::where('type', 'medication_route')
            ->where('status', true)
            ->orderBy('order')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->prepend('Select Route', '')
            ->toArray();

        return view('medicine_templates.edit', compact('medicineTemplate', 'medicines', 'routes'));
    }

    public function update(Request $request, MedicineTemplate $medicineTemplate)
    {
        if (!Auth::user()->can('medicine-templates.edit')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $medicineTemplate) {
            $medicineTemplate->update($request->only(['name', 'category', 'description']));

            $medicineTemplate->medications()->delete();

            if ($request->filled('medications') && is_array($request->medications)) {
                foreach ($request->medications as $med) {
                    if (!empty($med['inventory_item_id']) || !empty($med['name'])) {
                        $medicineTemplate->medications()->create([
                            'inventory_item_id' => $med['inventory_item_id'] ?? null,
                            'name' => $med['name'] ?? null,
                            'dosage' => $med['dosage'] ?? null,
                            'route' => $med['route'] ?? null,
                            'frequency' => $med['frequency'] ?? null,
                            'instructions' => $med['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('medicine-templates.index')
            ->with('success', __('file.medicine_template_updated'));
    }

    public function destroy(MedicineTemplate $medicineTemplate)
    {
        if (!Auth::user()->can('medicine-templates.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.module_access_denied')], 403);
            }
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $medicineTemplate->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.medicine_template_deleted')]);
        }

        return back()->with('success', __('file.medicine_template_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('medicine-templates.delete')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.module_access_denied')], 403);
            }
            return response()->json([
                'success' => false,
                'message' => __('file.module_access_denied')
            ], 403);
        }

        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('No templates selected.')], 400);
            }
            return back()->with('error', __('No templates selected.'));
        }

        $count = MedicineTemplate::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.selected_templates_deleted'),
                'deleted' => $count
            ]);
        }

        return back()->with('success', __('file.selected_templates_deleted'));
    }
}