<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('suppliers.index')) {
            return redirect()->route('home')
                ->with('error', 'Sorry! You are not allowed to access this module.');
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
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $searchValue = trim($request->input('search.value', ''));
        $status      = $request->input('status');

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

        $totalRecords    = Supplier::count();
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
                ? '<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>'
                : '<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>';

            return [
                'id'             => $supplier->id,
                'name'           => $supplier->name,
                'contact_person' => $supplier->contact_person ?? '-',
                'email'          => $supplier->email ?? '-',
                'phone'          => $supplier->phone ?? '-',
                'location'       => $supplier->location ?? '-',
                'status_html'    => $statusHtml,
                'show_url'       => route('suppliers.show', $supplier),
                'edit_url'       => route('suppliers.edit', $supplier),
                'delete_url'     => route('suppliers.destroy', $supplier),
            ];
        });

        return response()->json([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('suppliers.create')) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Sorry! You are not allowed to create suppliers.');
        }

        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('suppliers.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'category'       => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email'          => ['nullable', 'email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'location'       => ['nullable', 'string', 'max:255'],
            'website'        => ['nullable', 'url', 'max:255'],
            'status'         => ['sometimes', 'boolean'],
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
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.show')) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Sorry! You are not allowed to view this supplier.');
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
                ->with('error', 'Sorry! You are not allowed to edit suppliers.');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'category'       => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email'          => ['nullable', 'email', Rule::unique('suppliers')->ignore($supplier->id)],
            'phone'          => ['nullable', 'string', 'max:20'],
            'location'       => ['nullable', 'string', 'max:255'],
            'website'        => ['nullable', 'url', 'max:255'],
            'status'         => ['sometimes', 'boolean'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if (!Auth::user()->can('suppliers.delete')) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Sorry! You are not allowed to delete suppliers.');
        }

        $supplier->delete();

        return back()->with('success', 'Supplier moved to trash successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('suppliers.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete suppliers.'
            ], 403);
        }

        $ids = $request->input('ids');

        if (is_string($ids) && !empty($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid suppliers selected.'
            ], 422);
        }

        $validator = \Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected suppliers do not exist or are invalid.'
            ], 422);
        }

        Supplier::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' supplier(s) moved to trash successfully.'
        ]);
    }
}