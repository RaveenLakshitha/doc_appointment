<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index()
    {
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

            return [
                'id'           => $item->id,
                'name'         => $item->name,
                'code'         => '<span class="font-mono text-sm text-gray-600 dark:text-gray-400">' . ($item->sku ?? '-') . '</span>',
                'category'     => ['name' => $item->category?->name ?? '-'],
                'supplier'     => ['name' => $item->primarySupplier?->name ?? '-'],
                'quantity'     => "<span class=\"font-semibold {$stockColor}\">{$item->current_stock}</span>",
                'status_html'  => $statusHtml,
                'show_url'     => route('inventory.show', $item),
                'edit_url'     => route('inventory.edit', $item),
                'delete_url'   => route('inventory.destroy', $item),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
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

        return view('inventory.show', compact(
            'inventoryitem',
            'secondary_suppliers'
        ));
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

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected']);
        }

        InventoryItem::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
}