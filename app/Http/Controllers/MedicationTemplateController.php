<?php

namespace App\Http\Controllers;

use App\Models\MedicationTemplate;
use App\Models\MedicationTemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationTemplateController extends Controller
{
    public function index(Request $request)
    {
        return view('medication-templates.index');
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));
        $categoryFilter = $request->input('category');

        $query = MedicationTemplate::query()
            ->with(['category:id,name,color'])
            ->withCount('medications')
            ->select('medication_templates.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%")
                  ->orWhereHas('category', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($categoryFilter, fn($q) => $q->where('category_id', $categoryFilter));

        $totalRecords    = MedicationTemplate::count();
        $filteredRecords = (clone $query)->count();

        match ((int)$orderIdx) {
            1 => $query->orderBy('name', $orderDir),
            2 => $query->orderBy('description', $orderDir),
            3 => $query->leftJoin('medication_template_categories as c', 'medication_templates.category_id', '=', 'c.id')
                       ->orderBy('c.name', $orderDir)
                       ->select('medication_templates.*'),
            4 => $query->orderBy('medications_count', $orderDir),
            5 => $query->orderBy('last_used_at', $orderDir === 'desc' ? 'desc' : 'asc')
                       ->orderBy('usage_count', $orderDir === 'desc' ? 'desc' : 'asc'),
            default => $query->orderBy('name', 'asc'),
        };

        $templates = $query->offset($start)->limit($length)->get();

        $data = $templates->map(fn($t) => [
            'id'                => $t->id,
            'name'              => $t->name ?? '-',
            'description'       => $t->description,
            'category'          => $t->category?->name,
            'category_color'    => $t->category?->color ?? '#6b7280',
            'medications_count' => $t->medications_count,
            'last_used_at'      => $t->last_used_at,
            'last_used_diff'    => $t->last_used_at?->diffForHumans() ?? 'Never',
            'show_url'          => route('medication-templates.show', $t),
            'edit_url'          => route('medication-templates.edit', $t),
            'delete_url'        => route('medication-templates.destroy', $t),
        ]);

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function filters(Request $request)
    {
        if ($request->query('column') === 'category') {
            return MedicationTemplateCategory::active()
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        return response()->json([]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : $ids;

        if (empty($ids)) {
            return back()->with('error', 'No templates selected.');
        }

        MedicationTemplate::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected templates moved to trash.');
    }

    public function show(MedicationTemplate $medicationTemplate)
    {
        $medicationTemplate->load([
            'category:id,name,color',
            'medications.dosageForm',
            'creator:id,first_name,last_name'
        ])->loadCount('usages');

        return view('medication-templates.show', compact('medicationTemplate'));
    }

    public function create()
    {
        $categories = MedicationTemplateCategory::active()->get();
        return view('medication-templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:medication_template_categories,id',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = Auth::id();
        MedicationTemplate::create($data);

        return redirect()->route('medication-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(MedicationTemplate $medicationTemplate)
    {
        $categories = MedicationTemplateCategory::active()->get();
        return view('medication-templates.edit', compact('medicationTemplate', 'categories'));
    }

    public function update(Request $request, MedicationTemplate $medicationTemplate)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:medication_template_categories,id',
            'description' => 'nullable|string',
        ]);

        $medicationTemplate->update($data);

        return redirect()->route('medication-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(MedicationTemplate $medicationTemplate)
    {
        $medicationTemplate->delete();
        return back()->with('success', 'Template moved to trash.');
    }
}