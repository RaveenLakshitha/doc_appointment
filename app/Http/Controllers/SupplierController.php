<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:suppliers.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:suppliers.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:suppliers.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:suppliers.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
        if (!Auth::user()->can('suppliers.index')) {
            return redirect()->route('home')
                ->with('error', __('file.permission_denied'));
        }

        $suppliers = Supplier::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('contact_person', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('category', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = trim($request->input('search.value', ''));
        $status = $request->input('status');

        $query = Supplier::query()
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('contact_person', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%")
                    ->orWhere('category', 'like', "%{$searchValue}%")
                    ->orWhere('location', 'like', "%{$searchValue}%");
            })
            ->when($status === 'active', fn($q) => $q->where('status', true))
            ->when($status === 'inactive', fn($q) => $q->where('status', false));

        $totalRecords = Supplier::count();
        $filteredRecords = (clone $query)->count();

        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'name', 'contact_person', 'email', 'phone', 'location', 'status'];
        $sortColumn = $columns[$orderIdx] ?? 'name';

        $suppliers = $query->orderBy($sortColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $suppliers->map(function ($supplier) {
            $statusHtml = $supplier->status
                ? '<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">' . __('file.active') . '</span>'
                : '<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . __('file.inactive') . '</span>';

            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contact_person' => $supplier->contact_person ?? '-',
                'email' => $supplier->email ?? '-',
                'phone' => $supplier->phone ?? '-',
                'location' => $supplier->location ?? '-',
                'status_html' => $statusHtml,
                'show_url' => route('suppliers.show', $supplier),
                'edit_url' => \Auth::user()->can('suppliers.edit') ? route('suppliers.edit', $supplier) : null,
                'delete_url' => \Auth::user()->can('suppliers.delete') ? route('suppliers.destroy', $supplier) : null,
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
        if (!Auth::user()->can('suppliers.create')) {
            return redirect()->route('suppliers.index')
                ->with('error', __('file.permission_denied'));
        }

        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('suppliers.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'min:7', 'max:15'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $validated['status'] = $request->boolean('status', true);

        $deleted = Supplier::withTrashed()
            ->where('name', $validated['name'])
            ->where('email', $validated['email'] ?? null)
            ->first();

        if ($deleted) {
            $deleted->forceDelete();
        }

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('file.supplier_created_successfully'));
    }

    public function show(Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.index')) {
            return redirect()->route('suppliers.index')
                ->with('error', __('file.permission_denied'));
        }

        $supplier->load([
            'inventoryItems',
            'secondaryItems',
        ]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.edit')) {
            return redirect()->route('suppliers.index')
                ->with('error', __('file.permission_denied'));
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('suppliers')->ignore($supplier->id)],
            'phone' => ['nullable', 'string', 'min:7', 'max:15'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('file.supplier_updated_successfully'));
    }

    public function destroy(Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return redirect()->route('suppliers.index')
                ->with('error', __('file.permission_denied'));
        }

        $supplier->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.supplier_deleted_successfully')]);
        }

        return back()->with('success', __('file.supplier_trashed_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('suppliers.delete')) {
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
            'ids.*' => 'exists:suppliers,id'
        ]);

        if ($validator->fails()) {
            $msg = __('file.validation_failed');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $validator->errors()], 422);
            }
            return back()->with('error', $msg);
        }

        Supplier::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.selected_suppliers_deleted')
            ]);
        }

        return back()->with('success', __('file.selected_suppliers_deleted'));
    }
}