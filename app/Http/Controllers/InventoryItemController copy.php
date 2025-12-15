<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::query()
            ->with(['category', 'primarySupplier']);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('primarySupplier', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $items = $query->paginate(20)->appends($request->query());

        return view('inventory-items.index', compact('items', 'sort', 'direction'));
    }

    public function details(InventoryItem $inventoryitem)
    {
        $inventoryitem->load([
            'category',
            'primarySupplier',
            'secondaryItems'   // already loads Supplier + pivot data
        ]);

        $secondary_suppliers = $inventoryitem->secondaryItems->map(function ($supplier) {
            $pivot = $supplier->pivot;
            return [
                'name'       => $supplier->name,
                'item_code'  => $pivot->supplier_item_code,
                'price'      => $pivot->supplier_price,
                'lead_time'  => $pivot->lead_time_days,
                'min_qty'    => $pivot->minimum_order_quantity,
            ];
        })->toArray();

        return response()->json([
            'item'               => $inventoryitem,
            'secondary_suppliers' => $secondary_suppliers
        ]);
    }

    public function show(InventoryItem $inventoryitem)
    {
        $inventoryitem->load([
            'category',
            'primarySupplier',
            'secondaryItems'   // already loads Supplier + pivot
        ]);

        // Build the same array you returned in JSON
        $secondary_suppliers = $inventoryitem->secondaryItems->map(function ($supplier) {
            $pivot = $supplier->pivot;
            return [
                'name'       => $supplier->name,
                'item_code'  => $pivot->supplier_item_code,
                'price'      => $pivot->supplier_price,
                'lead_time'  => $pivot->lead_time_days,
                'min_qty'    => $pivot->minimum_order_quantity,
            ];
        })->toArray();

        return view('inventory-items.show', compact(
            'inventoryitem',
            'secondary_suppliers'
        ));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        $units = UnitOfMeasure::active()->orderBy('name')->get();

        return view('inventory-items.create', compact('categories', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory_items,sku',
            'category_id' => 'required|exists:categories,id',
            'unit_of_measure' => 'required|exists:unit_of_measures,name',
            'unit_quantity' => 'required|integer|min:1',
            'current_stock' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'primary_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        InventoryItem::create($request->all());

        return redirect()
            ->route('inventory-items.index')
            ->with('success', 'Inventory item created.');
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        $units = UnitOfMeasure::active()->orderBy('name')->get();

        return view('inventory-items.edit', compact('inventoryItem', 'categories', 'suppliers', 'units'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                Rule::unique('inventory_items')->ignore($inventoryItem->id),
            ],
            'category_id' => 'required|exists:categories,id',
            'unit_of_measure' => 'required|exists:unit_of_measures,name',
            'unit_quantity' => 'required|integer|min:1',
            'current_stock' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'primary_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $inventoryItem->update($request->all());

        return redirect()
            ->route('inventory-items.index')
            ->with('success', 'Inventory item updated.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete(); // Hard delete

        return back()->with('success', 'Inventory item permanently deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:inventory_items,id',
        ]);

        InventoryItem::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Selected items permanently deleted.');
    }
}