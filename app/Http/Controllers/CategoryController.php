<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $categories = Category::query()
            ->when($search, fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            )
            ->when(in_array($sort, ['name', 'description']), fn($q) => $q->orderBy($sort, $direction))
            ->active()
            ->with(['parent', 'children' => fn($q) => $q->active()->with('children')])
            ->paginate(10)
            ->appends($request->query());

        return view('categories.index', compact('categories', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $parents = Category::active()->orderBy('name')->get();
        return view('categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'is_active'   => 'sometimes|boolean',
        ]);

        Category::create(
            $request->only(['name', 'description', 'parent_id', 'is_active'])
            + ['is_active' => $request->boolean('is_active', true)]
        );

        return redirect()->route('categories.index')
                         ->with('success', 'Category created.');
    }

    public function show(Category $category)
    {
        $category->loadMissing(['parent', 'children' => fn($q) => $q->active()->with('children')]);
        return view('categories.show', compact('category'));
    }

    public function details(Category $category)
    {
        $category->loadMissing(['parent', 'children' => fn($q) => $q->active()->with('children')]);
        return response()->json([
            'category'      => $category,
            'subcategories' => $category->children->toArray(),
        ]);
    }

    public function edit(Category $category)
    {
        $parents = Category::active()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
        ]);

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id,
        ]);

        return redirect()->route('categories.index')
                        ->with('success', 'Category updated.');
    }

    /**
     * Permanently delete a single category
     */
    public function destroy(Category $category)
    {
        $category->delete();   // Hard-delete (no soft-delete)

        return back()->with('success', 'Category permanently deleted.');
    }

    /**
     * Permanently delete multiple categories
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        // Optional: Delete related records first if you have foreign keys
        // DB::table('subcategories')->whereIn('parent_id', $request->ids)->delete();

        Category::whereIn('id', $request->ids)->delete(); // Hard-delete

        return back()->with('success', 'Selected categories permanently deleted.');
    }
}