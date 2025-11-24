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
        $search = $request->get('search');
        $categoryId = $request->get('category');

        $categories = MedicationTemplateCategory::active()->get();

        $templates = MedicationTemplate::query()
            ->with([
                'category' => fn($q) => $q->select('id', 'name', 'color'),
                'creator' => fn($q) => $q->select('id', 'first_name', 'last_name'), 
                'medications'
            ])
            ->select('medication_templates.*')
            ->when($search, fn($q) => $q->search($search))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->withUsage()
            ->orderByDesc('last_used_at')
            ->orderByDesc('usage_count')
            ->orderBy('name')
            ->paginate(10);

        return view('medication-templates.index', compact('templates', 'categories', 'search', 'categoryId'));
    }

    public function show(MedicationTemplate $template)
    {
        $template->load([
            'category:id,name,color',
            'medications.dosageForm',
            'creator:id,first_name,last_name'
        ]);

        $template->loadCount('usages');

        return view('medication-templates.show', compact('template'));
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

    public function bulkDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->get('ids', '')));

        MedicationTemplate::whereIn('id', $ids)
            ->where('created_by', Auth::id())
            ->delete();

        return back()->with('success', 'Selected templates moved to trash.');
    }
}