<?php

namespace App\Http\Controllers;

use App\Models\MedicationTemplateCategory;
use Illuminate\Http\Request;

class MedicationTemplateCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:medication_template_categories',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        MedicationTemplateCategory::create($request->all() + ['is_active' => true]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, MedicationTemplateCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:medication_template_categories,name,' . $category->id,
            'color' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $category->update($request->all());

        return back()->with('success', 'Category updated.');
    }

    public function destroy(MedicationTemplateCategory $category)
    {
        if ($category->templates()->count() > 0) {
            return back()->with('error', 'Cannot delete category with templates.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}