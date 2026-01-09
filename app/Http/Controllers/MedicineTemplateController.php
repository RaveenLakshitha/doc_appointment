<?php

namespace App\Http\Controllers;

use App\Models\MedicineTemplate;
use App\Models\TemplateMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineTemplateController extends Controller
{
    public function index(Request $request)
    {
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

        // Map column index to actual column
        $columns = ['id', 'name', 'category', 'description', 'medications_count'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $templates = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $templates->map(function ($template) {
            return [
                'id'                => $template->id,
                'name'              => $template->name,
                'category'          => $template->category ?? '-',
                'description'       => $template->description ? Str::limit($template->description, 60) : '-',
                'medications_count' => $template->medications_count,
                'show_url'          => route('medicine-templates.show', $template),
                'edit_url'          => route('medicine-templates.edit', $template),
                'delete_url'        => route('medicine-templates.destroy', $template),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        return view('medicine_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $template = MedicineTemplate::create($request->only(['name', 'category', 'description']));

            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $med) {
                    if (!empty($med['name'])) {
                        $template->medications()->create([
                            'name'        => $med['name'],
                            'dosage'      => $med['dosage'] ?? null,
                            'route'       => $med['route'] ?? 'Oral',
                            'frequency'   => $med['frequency'] ?? null,
                            'instructions'=> $med['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('medicine-templates.index')->with('success', 'Medicine template created successfully.');
    }

    public function show(MedicineTemplate $medicineTemplate)
    {
        $medicineTemplate->load('medications');
        return view('medicine_templates.show', compact('medicineTemplate'));
    }

    public function getMedications($id)
    {
        $template = MedicineTemplate::with('medications')->findOrFail($id);

        return response()->json($template->medications->map(function ($med) {
            return [
                'name'          => $med->name,
                'dosage'        => $med->dosage,
                'route'         => $med->route,
                'frequency'     => $med->frequency,
                'duration_days' => $med->duration_days ?? null,
                'instructions'  => $med->instructions ?? null,
            ];
        }));
    }

    public function edit(MedicineTemplate $medicineTemplate)
    {
        $medicineTemplate->load('medications');
        return view('medicine_templates.edit', compact('medicineTemplate'));
    }

    public function update(Request $request, MedicineTemplate $medicineTemplate)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $medicineTemplate) {
            $medicineTemplate->update($request->only(['name', 'category', 'description']));

            // Delete existing medications and recreate
            $medicineTemplate->medications()->delete();

            if ($request->has('medications') && is_array($request->medications)) {
                foreach ($request->medications as $med) {
                    if (!empty($med['name'])) {
                        $medicineTemplate->medications()->create([
                            'name'        => $med['name'],
                            'dosage'      => $med['dosage'] ?? null,
                            'route'       => $med['route'] ?? 'Oral',
                            'frequency'   => $med['frequency'] ?? null,
                            'instructions'=> $med['instructions'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('medicine-templates.index')->with('success', 'Medicine template updated successfully.');
    }

    public function destroy(MedicineTemplate $medicineTemplate)
    {
        $medicineTemplate->delete();
        return back()->with('success', 'Medicine template deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:medicine_templates,id',
        ]);

        MedicineTemplate::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected templates deleted successfully.');
    }
}