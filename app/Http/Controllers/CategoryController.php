<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:categories.index', ['only' => ['index', 'show', 'details', 'datatable']]);
        $this->middleware('permission:categories.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categories.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categories.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index()
    {
        if (!Auth::user()->can('categories.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('categories.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $statusFilter = $request->status;

        $query = Category::query()
            ->select('categories.*')
            ->with('parent:id,name,deleted_at')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            })
            ->when($statusFilter, function ($q) use ($statusFilter) {
                if ($statusFilter === 'active')
                    return $q->where('is_active', true);
                if ($statusFilter === 'inactive')
                    return $q->where('is_active', false);
            });

        $totalRecords = Category::count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int) $orderIdx) {
            1 => 'name',
            2 => 'description',
            3 => 'parent_id',
            default => 'name',
        };

        if ($sortColumn === 'parent_id') {
            $query->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
                ->orderByRaw("COALESCE(parents.name, 'zzz') {$orderDir}")
                ->select('categories.*');
        } else {
            $query->orderBy($sortColumn, $orderDir);
        }

        $categories = $query->offset($start)->limit($length)->get();

        $data = $categories->map(function ($category) {
            $statusHtml = $category->is_active
                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">' . __('file.active') . '</span>'
                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">' . __('file.inactive') . '</span>';

            $edit_url = Auth::user()->can('categories.edit') ? route('categories.edit', $category) : null;
            $delete_url = Auth::user()->can('categories.delete') ? route('categories.destroy', $category) : null;

            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description ?? '—',
                'parent_name' => $category->parent?->deleted_at
                    ? ($category->parent?->name . ' ' . __('file.deleted_suffix'))
                    : ($category->parent?->name ?? '—'),
                'parent_id' => $category->parent_id,
                'is_active' => $category->is_active,
                'status_html' => $statusHtml,
                'show_url' => route('categories.show', $category),
                'edit_url' => $edit_url,
                'delete_url' => $delete_url,
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('categories.create')) {
            return redirect()->route('categories.index')
                ->with('error', __('file.categories_create_denied'));
        }

        $parents = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('categories.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category = Category::withTrashed()->where('name', $request->name)->first();
        if ($category && $category->trashed()) {
            $category->restore();
            $category->update($validated);
        } else {
            Category::create($validated);
        }

        return redirect()->route('categories.index')
            ->with('success', __('file.category_created_successfully'));
    }

    public function show(Category $category)
    {
        if (!Auth::user()->can('categories.index')) {
            return redirect()->route('categories.index')
                ->with('error', __('file.categories_show_denied'));
        }

        $category->load([
            'parent',
            'children' => fn($q) => $q->orderBy('name')
        ]);

        return view('categories.show', compact('category'));
    }

    public function details(Category $category)
    {
        if (!Auth::user()->can('categories.index')) {
            return response()->json(['error' => __('file.unauthorized')], 403);
        }

        $category->load([
            'parent:id,name',
            'children' => fn($q) => $q->select('id', 'name', 'is_active', 'parent_id')
                ->with(['children:id,name,is_active,parent_id'])
        ]);

        return response()->json([
            'category' => $category,
            'subcategories' => $category->children->toArray(),
        ]);
    }

    public function edit(Category $category)
    {
        if (!Auth::user()->can('categories.edit')) {
            return redirect()->route('categories.index')
                ->with('error', __('file.categories_edit_denied'));
        }

        $parents = Category::where('id', '!=', $category->id)
            ->where(function ($q) use ($category) {
                $q->whereNull('parent_id')
                    ->orWhereNotIn('id', $this->getDescendantIds($category));
            })
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        if (!Auth::user()->can('categories.edit')) {
            return response()->json([
                'success' => false,
                'message' => __('file.unauthorized')
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)->whereNull('deleted_at')],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value && in_array($value, $this->getDescendantIds($category))) {
                        $fail(__('file.descendant_category_error'));
                    }
                },
            ],
            'is_active' => 'sometimes|boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('file.category_updated_successfully')
        ]);
    }

    public function destroy(Category $category)
    {
        if (!Auth::user()->can('categories.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return redirect()->route('categories.index')
                ->with('error', __('file.permission_denied'));
        }

        $category->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.category_deleted_successfully')]);
        }

        return back()->with('success', __('file.category_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('categories.delete')) {
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
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            $msg = __('file.validation_failed');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $validator->errors()], 422);
            }
            return back()->with('error', $msg);
        }

        Category::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.categories_bulk_deleted_successfully')
            ]);
        }

        return back()->with('success', __('file.categories_bulk_deleted_successfully'));
    }

    protected function getDescendantIds(Category $category): array
    {
        $ids = [];
        $children = $category->children()->pluck('id')->toArray();

        foreach ($children as $childId) {
            $ids[] = $childId;
            $child = Category::find($childId);
            if ($child) {
                $ids = array_merge($ids, $this->getDescendantIds($child));
            }
        }

        return $ids;
    }
}