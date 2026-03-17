<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('reports.inventory', compact('categories', 'suppliers'));
    }

    public function summary(Request $request)
    {
        $categoryId = $request->integer('category_id') ?: null;
        $supplierId = $request->integer('supplier_id') ?: null;

        // Base query for inventory items
        $query = InventoryItem::query()
            ->with(['category', 'primarySupplier'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($supplierId, fn($q) => $q->where('primary_supplier_id', $supplierId));

        // ────────────────────────────────────────────────
        // Summary Cards
        // ────────────────────────────────────────────────
        $totalItems = (clone $query)->count();

        $lowStockItems = (clone $query)
            ->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockItems = (clone $query)
            ->where('current_stock', 0)
            ->count();

        // Expiring soon = next 90 days from TODAY
        $expiringSoon = MedicineBatch::query()
            ->where('expiry_date', '<=', Carbon::today()->addDays(90))
            ->where('expiry_date', '>=', Carbon::today())
            ->where('current_quantity', '>', 0)
            ->count();

        $inventoryValue = (clone $query)
            ->sum(DB::raw('current_stock * unit_cost'));

        // ────────────────────────────────────────────────
        // Stock Status Distribution
        // ────────────────────────────────────────────────
        $stockStatus = [
            'In Stock' => (clone $query)->where('current_stock', '>', DB::raw('minimum_stock_level'))->count(),
            'Low Stock' => $lowStockItems,
            'Out of Stock' => $outOfStockItems,
        ];

        // ────────────────────────────────────────────────
        // Inventory by Category
        // ────────────────────────────────────────────────
        $byCategory = (clone $query)
            ->join('categories', 'inventory_items.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->pluck('count', 'name')
            ->toArray();

        // ────────────────────────────────────────────────
        // Top Low Stock Items
        // ────────────────────────────────────────────────
        $topLowStock = (clone $query)
            ->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'sku' => $item->sku ?? '—',
                'category' => $item->category?->name ?? '—',
                'current_stock' => $item->current_stock,
                'minimum_stock_level' => $item->minimum_stock_level,
                'unit' => $item->unit_of_measure ?? 'units',
                'value' => number_format($item->total_value, 2),
                'days_to_reorder' => $item->lead_time_days ? "~{$item->lead_time_days} days" : '—',
            ]);

        // ────────────────────────────────────────────────
        // Expiring Soon Items (top 10, ordered by soonest expiry)
        // ────────────────────────────────────────────────
        $expiringItems = MedicineBatch::with(['item.category'])
            ->where('expiry_date', '<=', Carbon::today()->addDays(90))
            ->where('expiry_date', '>=', Carbon::today())
            ->where('current_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->take(10)
            ->get()
            ->map(function ($batch) {
                $daysLeft = Carbon::today()->diffInDays($batch->expiry_date, false);
                return [
                    'item_name' => $batch->item?->name ?? '—',
                    'batch_number' => $batch->batch_number,
                    'expiry_date' => $batch->expiry_date?->format('M d, Y'),
                    'days_left' => $daysLeft,
                    'quantity' => $batch->current_quantity,
                    'category' => $batch->item?->category?->name ?? '—',
                ];
            });

        return response()->json([
            'summary' => [
                'total_items' => $totalItems,
                'low_stock_items' => $lowStockItems,
                'expiring_soon' => $expiringSoon,
                'inventory_value' => number_format($inventoryValue, 2),
            ],
            'stock_status' => $stockStatus,
            'by_category' => $byCategory,
            'top_low_stock' => $topLowStock,
            'expiring_items' => $expiringItems,
        ]);
    }
}