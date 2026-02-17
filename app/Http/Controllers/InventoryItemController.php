<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class InventoryItemController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('inventory-items.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        return view('inventory.index');
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $categoryFilter = $request->category;
        $supplierFilter = $request->supplier;
        $statusFilter   = $request->status;

        $query = InventoryItem::query()
            ->with(['category', 'primarySupplier'])
            ->select('inventory_items.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('sku', 'like', "%{$searchValue}%")
                  ->orWhere('manufacturer', 'like', "%{$searchValue}%")
                  ->orWhere('brand', 'like', "%{$searchValue}%")
                  ->orWhere('model_version', 'like', "%{$searchValue}%")
                  ->orWhereHas('category', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                  ->orWhereHas('primarySupplier', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($categoryFilter, fn($q) => $q->where('category_id', $categoryFilter))
            ->when($supplierFilter, fn($q) => $q->where('primary_supplier_id', $supplierFilter))
            ->when($statusFilter, function ($q) use ($statusFilter) {
                return match ($statusFilter) {
                    'low_stock'     => $q->lowStock(),
                    'out_of_stock'  => $q->outOfStock(),
                    'in_stock'      => $q->where('current_stock', '>', 10),
                    default         => $q,
                };
            });

        $totalRecords     = InventoryItem::count();
        $filteredRecords  = (clone $query)->count();

        $sortColumn = match ((int)$orderIdx) {
            1 => 'name',
            2 => 'sku',
            3 => 'category_id',
            4 => 'primary_supplier_id',
            5 => 'current_stock',
            default => 'name',
        };

        if (in_array($sortColumn, ['category_id', 'primary_supplier_id'])) {
            $relation = $sortColumn === 'category_id' ? 'category' : 'primarySupplier';
            $query->join("{$relation}s", "inventory_items.{$sortColumn}", '=', "{$relation}s.id")
                  ->orderBy("{$relation}s.name", $orderDir)
                  ->select('inventory_items.*');
        } else {
            $query->orderBy($sortColumn, $orderDir);
        }

        $items = $query->offset($start)->limit($length)->get();

        $data = $items->map(function ($item) {
            $statusHtml = match (true) {
                $item->current_stock == 0 => '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Out of Stock</span>',
                $item->current_stock <= $item->reorder_point => '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Low Stock</span>',
                default => '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">In Stock</span>',
            };

            $stockColor = $item->current_stock == 0 ? 'text-red-600' :
                          ($item->current_stock <= $item->reorder_point ? 'text-yellow-600' : 'text-green-600');

            $edit_url   = Auth::user()->can('inventory-items.edit') ? route('inventory.edit', $item) : null;
            $delete_url = Auth::user()->can('inventory-items.delete') ? route('inventory.destroy', $item) : null;

            return [
                'id'           => $item->id,
                'name'         => $item->name,
                'code'         => '<span class="font-mono text-sm text-gray-600 dark:text-gray-400">' . ($item->sku ?? '-') . '</span>',
                'category'     => ['name' => $item->category?->name ?? '-'],
                'supplier'     => ['name' => $item->primarySupplier?->name ?? '-'],
                'quantity'     => "<span class=\"font-semibold {$stockColor}\">{$item->current_stock}</span>",
                'status_html'  => $statusHtml,
                'show_url'     => route('inventory.show', $item),
                'edit_url'     => $edit_url,
                'delete_url'   => $delete_url,
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
        if (!Auth::user()->can('inventory-items.create')) {
            return redirect()->route('inventory.index')
                ->with('error', __('file.inventory_create_denied'));
        }

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        $units      = UnitOfMeasure::where('is_active', true)->orderBy('name')->get();

        return view('inventory.create', compact('categories', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('inventory-items.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'sku'                 => ['required', 'string', 'max:100', 'unique:inventory_items,sku'],
            'category_id'         => ['required', 'exists:categories,id'],
            'primary_supplier_id' => ['required', 'exists:suppliers,id'],
            'description'         => ['nullable', 'string'],
            'unit_of_measure'     => ['nullable', 'string', 'max:50'],
            'unit_quantity'       => ['nullable', 'integer', 'min:1'],
            'storage_location'    => ['nullable', 'string', 'max:255'],
            'manufacturer'        => ['nullable', 'string', 'max:255'],
            'brand'               => ['nullable', 'string', 'max:255'],
            'model_version'       => ['nullable', 'string', 'max:100'],
            'additional_info'     => ['nullable', 'string'],
            'current_stock'       => ['nullable', 'integer', 'min:0'],
            'minimum_stock_level' => ['nullable', 'integer', 'min:0'],
            'maximum_stock_level' => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'reorder_quantity'    => ['nullable', 'integer', 'min:0'],
            'unit_cost'           => ['nullable', 'numeric', 'min:0'],
            'unit_price'          => ['nullable', 'numeric', 'min:0'],
            'tax_rate'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'supplier_item_code'      => ['nullable', 'string', 'max:100'],
            'supplier_price'          => ['nullable', 'numeric', 'min:0'],
            'lead_time_days'          => ['nullable', 'integer', 'min:0'],
            'minimum_order_quantity'  => ['nullable', 'integer', 'min:0'],
            'generic_name'            => ['nullable', 'string', 'max:255'],
            'medicine_type'           => ['nullable', 'string', 'max:100'],
            'dosage'                  => ['nullable', 'string', 'max:100'],
            'side_effects'            => ['nullable', 'string'],
            'precautions_warnings'    => ['nullable', 'string'],
            'expiry_tracking'         => ['boolean'],
            'requires_refrigeration'  => ['boolean'],
            'controlled_substance'    => ['boolean'],
            'hazardous_material'      => ['boolean'],
            'sterile'                 => ['boolean'],
            'is_active'               => ['boolean'],
            'medicine_image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'package_image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'storage_conditions'      => ['nullable', 'array'],
            'storage_conditions.*'    => ['string'],
            'batch_number'            => ['nullable', 'string', 'max:100'],
            'manufacturing_date'      => ['nullable', 'date'],
            'expiry_date'             => ['nullable', 'date', 'after_or_equal:today'],
            'initial_quantity'        => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('medicine_image')) {
            $validated['medicine_image'] = $request->file('medicine_image')->store('inventory/medicine', 'public');
        }

        if ($request->hasFile('package_image')) {
            $validated['package_image'] = $request->file('package_image')->store('inventory/packages', 'public');
        }

        if (isset($validated['storage_conditions'])) {
            $validated['storage_conditions'] = json_encode($validated['storage_conditions']);
        }

        $validated['is_active']              = $request->boolean('is_active', true);
        $validated['expiry_tracking']        = $request->boolean('expiry_tracking', false);
        $validated['requires_refrigeration'] = $request->boolean('requires_refrigeration', false);
        $validated['controlled_substance']   = $request->boolean('controlled_substance', false);
        $validated['hazardous_material']     = $request->boolean('hazardous_material', false);
        $validated['sterile']                = $request->boolean('sterile', false);

        $item = InventoryItem::create($validated);

        if (
            $request->boolean('expiry_tracking') &&
            $request->filled('expiry_date') &&
            $request->filled('initial_quantity')
        ) {
            $item->batches()->create([
                'batch_number'       => $request->input('batch_number'),
                'manufacturing_date' => $request->input('manufacturing_date'),
                'expiry_date'        => $request->input('expiry_date'),
                'initial_quantity'   => $request->input('initial_quantity'),
                'current_quantity'   => $request->input('initial_quantity'),
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', __('file.inventory_item_created_successfully'));
    }

    public function show(InventoryItem $inventoryitem)
    {
       
        $inventoryitem->load([
            'category',
            'primarySupplier',
            'secondaryItems'
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

        return view('inventory.show', compact('inventoryitem', 'secondary_suppliers'));
    }

    public function details(InventoryItem $inventoryitem)
    {
        if (!Auth::user()->can('inventory-items.show')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inventoryitem->load([
            'category',
            'primarySupplier',
            'secondaryItems'
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

    public function edit(InventoryItem $inventoryitem)
    {
        if (!Auth::user()->can('inventory-items.edit')) {
            return redirect()->route('inventory.index')
                ->with('error', __('file.inventory_edit_denied'));
        }

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        $units      = UnitOfMeasure::where('is_active', true)->orderBy('name')->get();

        return view('inventory.edit', compact('inventoryitem', 'categories', 'suppliers', 'units'));
    }

    public function update(Request $request, InventoryItem $inventoryitem)
    {
        if (!Auth::user()->can('inventory-items.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'sku'                 => ['required', 'string', 'max:100', Rule::unique('inventory_items')->ignore($inventoryitem->id)],
            'category_id'         => ['required', 'exists:categories,id'],
            'primary_supplier_id' => ['required', 'exists:suppliers,id'],
            'description'         => ['nullable', 'string'],
            'unit_of_measure'     => ['nullable', 'string', 'max:50'],
            'unit_quantity'       => ['nullable', 'integer', 'min:1'],
            'storage_location'    => ['nullable', 'string', 'max:255'],
            'manufacturer'        => ['nullable', 'string', 'max:255'],
            'brand'               => ['nullable', 'string', 'max:255'],
            'model_version'       => ['nullable', 'string', 'max:100'],
            'additional_info'     => ['nullable', 'string'],
            'current_stock'       => ['nullable', 'integer', 'min:0'],
            'minimum_stock_level' => ['nullable', 'integer', 'min:0'],
            'maximum_stock_level' => ['nullable', 'integer', 'min:0'],
            'reorder_point'       => ['nullable', 'integer', 'min:0'],
            'reorder_quantity'    => ['nullable', 'integer', 'min:0'],
            'unit_cost'           => ['nullable', 'numeric', 'min:0'],
            'unit_price'          => ['nullable', 'numeric', 'min:0'],
            'tax_rate'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'supplier_item_code'      => ['nullable', 'string', 'max:100'],
            'supplier_price'          => ['nullable', 'numeric', 'min:0'],
            'lead_time_days'          => ['nullable', 'integer', 'min:0'],
            'minimum_order_quantity'  => ['nullable', 'integer', 'min:0'],
            'generic_name'            => ['nullable', 'string', 'max:255'],
            'medicine_type'           => ['nullable', 'string', 'max:100'],
            'dosage'                  => ['nullable', 'string', 'max:100'],
            'side_effects'            => ['nullable', 'string'],
            'precautions_warnings'    => ['nullable', 'string'],
            'expiry_tracking'         => ['boolean'],
            'requires_refrigeration'  => ['boolean'],
            'controlled_substance'    => ['boolean'],
            'hazardous_material'      => ['boolean'],
            'sterile'                 => ['boolean'],
            'is_active'               => ['boolean'],
            'medicine_image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'package_image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'storage_conditions'      => ['nullable', 'array'],
            'storage_conditions.*'    => ['string'],
            'batch_number'            => ['nullable', 'string', 'max:100'],
            'manufacturing_date'      => ['nullable', 'date'],
            'expiry_date'             => ['nullable', 'date', 'after_or_equal:today'],
            'initial_quantity'        => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('medicine_image')) {
            if ($inventoryitem->medicine_image) {
                Storage::disk('public')->delete($inventoryitem->medicine_image);
            }
            $validated['medicine_image'] = $request->file('medicine_image')->store('inventory/medicine', 'public');
        }

        if ($request->hasFile('package_image')) {
            if ($inventoryitem->package_image) {
                Storage::disk('public')->delete($inventoryitem->package_image);
            }
            $validated['package_image'] = $request->file('package_image')->store('inventory/packages', 'public');
        }

        if (isset($validated['storage_conditions'])) {
            $validated['storage_conditions'] = json_encode($validated['storage_conditions']);
        }

        $validated['is_active']              = $request->boolean('is_active', $inventoryitem->is_active);
        $validated['expiry_tracking']        = $request->boolean('expiry_tracking', $inventoryitem->expiry_tracking);
        $validated['requires_refrigeration'] = $request->boolean('requires_refrigeration', $inventoryitem->requires_refrigeration);
        $validated['controlled_substance']   = $request->boolean('controlled_substance', $inventoryitem->controlled_substance);
        $validated['hazardous_material']     = $request->boolean('hazardous_material', $inventoryitem->hazardous_material);
        $validated['sterile']                = $request->boolean('sterile', $inventoryitem->sterile);

        $inventoryitem->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', __('file.inventory_item_updated_successfully'));
    }

    public function destroy(InventoryItem $inventoryitem)
    {
        if (!Auth::user()->can('inventory-items.delete')) {
            return redirect()->route('inventory.index')
                ->with('error', __('file.inventory_delete_denied'));
        }

        if ($inventoryitem->medicine_image) {
            Storage::disk('public')->delete($inventoryitem->medicine_image);
        }
        if ($inventoryitem->package_image) {
            Storage::disk('public')->delete($inventoryitem->package_image);
        }

        $inventoryitem->delete();

        return redirect()->route('inventory.index')
            ->with('success', __('file.inventory_item_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('inventory-items.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.inventory_bulk_delete_denied')
            ], 403);
        }

        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_items_selected')
            ]);
        }

        $items = InventoryItem::whereIn('id', $ids)->get();
        foreach ($items as $item) {
            if ($item->medicine_image) Storage::disk('public')->delete($item->medicine_image);
            if ($item->package_image) Storage::disk('public')->delete($item->package_image);
        }

        InventoryItem::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.inventory_items_bulk_deleted_successfully')
        ]);
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        return match ($column) {
            'category' => Category::orderBy('name')->pluck('name', 'id'),
            'supplier' => Supplier::orderBy('name')->pluck('name', 'id'),
            default    => response()->json([]),
        };
    }
}