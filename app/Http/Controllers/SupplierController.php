<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $sort       = $request->get('sort', 'name');
        $direction  = $request->get('direction', 'asc');

        $suppliers = Supplier::query()
            ->when($search, fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
            )
            ->when(in_array($sort, ['name', 'email', 'phone']), fn($q) => $q->orderBy($sort, $direction))
            ->active()
            ->paginate(10)
            ->appends($request->query());

        return view('suppliers.index', compact('suppliers', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:suppliers,name',
            'category'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|unique:suppliers,email',
            'phone'          => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:255',
            'website'        => 'nullable|url',
            'status'         => 'sometimes|boolean',
        ]);

        Supplier::create($request->only([
            'name', 'category', 'description', 'contact_person',
            'email', 'phone', 'location', 'website'
        ]) + ['status' => true]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'category'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone'          => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:255',
            'website'        => 'nullable|url',
            'status'         => 'sometimes|boolean',
        ]);

        $supplier->update($request->only([
            'name', 'category', 'description', 'contact_person',
            'email', 'phone', 'location', 'website', 'status'
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['status' => false]);
        $supplier->delete();

        return back()->with('success', 'Supplier deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:suppliers,id',
        ]);

        Supplier::whereIn('id', $request->ids)
            ->update(['status' => false]);

        Supplier::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Selected suppliers deleted.');
    }
}