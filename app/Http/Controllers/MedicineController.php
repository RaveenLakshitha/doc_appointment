<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\MedicineBatch;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    /**
     * Display a listing of medicines.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('medicines.index', compact('categories'));
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('medicines.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created medicine in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'generic_name'         => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'medicine_type'        => 'required|in:Tablet,Capsule,Syrup,Injection,Cream/Ointment,Drops,Other',
            'description'          => 'nullable|string',
            'manufacturer'         => 'nullable|string|max:255',
            'primary_supplier_id'  => 'nullable|exists:suppliers,id',
            'dosage'               => 'nullable|string|max:100',
            'side_effects'         => 'nullable|string',
            'precautions_warnings'=> 'nullable|string',
            'initial_quantity'     => 'required|integer|min:1',
            'reorder_point'        => 'nullable|integer|min:0',
            'maximum_stock_level'  => 'nullable|integer|gte:reorder_point',
            'unit_cost'            => 'required|numeric|min:0',
            'unit_price'           => 'required|numeric|gte:unit_cost',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'batch_number'         => 'required|string|max:100',
            'manufacturing_date'   => 'nullable|date',
            'expiry_date'          => 'required|date|after_or_equal:today',
            'storage_conditions'   => 'nullable|array',
            'storage_conditions.*' => 'in:Room Temperature,Refrigerated,Frozen,Protect from Light',
            'is_active'            => 'boolean',
            'medicine_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'package_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $storageConditions = $request->filled('storage_conditions') ? $request->storage_conditions : null;

        DB::transaction(function () use ($validated, $request, $storageConditions) {
            $medicine = InventoryItem::create([
                'name'                  => $validated['name'],
                'generic_name'          => $validated['generic_name'],
                'category_id'           => $validated['category_id'],
                'medicine_type'         => $validated['medicine_type'],
                'description'           => $validated['description'] ?? null,
                'manufacturer'          => $validated['manufacturer'] ?? null,
                'primary_supplier_id'   => $validated['primary_supplier_id'] ?? null,
                'dosage'                => $validated['dosage'] ?? null,
                'side_effects'          => $validated['side_effects'] ?? null,
                'precautions_warnings'  => $validated['precautions_warnings'] ?? null,
                'current_stock'         => 0,
                'reorder_point'         => $validated['reorder_point'] ?? 0,
                'maximum_stock_level'   => $validated['maximum_stock_level'] ?? null,
                'unit_cost'             => $validated['unit_cost'],
                'unit_price'            => $validated['unit_price'],
                'tax_rate'              => $validated['tax_rate'] ?? 0,
                'storage_conditions'    => $storageConditions ? json_encode($storageConditions) : null,
                'is_active'             => $request->boolean('is_active', true),
                'expiry_tracking'       => true,
                'requires_refrigeration'=> in_array('Refrigerated', $storageConditions ?? []),
            ]);

            // Upload images
            if ($request->hasFile('medicine_image')) {
                $medicine->medicine_image = $request->file('medicine_image')->store('medicines/images', 'public');
            }
            if ($request->hasFile('package_image')) {
                $medicine->package_image = $request->file('package_image')->store('medicines/packages', 'public');
            }
            $medicine->save();

            // Create initial batch
            MedicineBatch::create([
                'inventory_item_id'   => $medicine->id,
                'batch_number'        => $validated['batch_number'],
                'manufacturing_date'  => $validated['manufacturing_date'] ?? null,
                'expiry_date'         => $validated['expiry_date'],
                'initial_quantity'    => $validated['initial_quantity'],
                'current_quantity'    => $validated['initial_quantity'],
            ]);

            // Update total stock
            $medicine->increment('current_stock', $validated['initial_quantity']);
        });

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine added successfully with initial batch.');
    }

    /**
     * Display the specified medicine.
     */
    public function show(InventoryItem $medicine)
    {
        $medicine->load(['category', 'primarySupplier', 'batches']);

        return view('medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the specified medicine.
     */
    public function edit(InventoryItem $medicine)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        $medicine->load('batches');

        return view('medicines.edit', compact('medicine', 'categories', 'suppliers'));
    }

    /**
     * Update the specified medicine in storage.
     */
    public function update(Request $request, InventoryItem $medicine)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'generic_name'         => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'medicine_type'        => 'required|in:Tablet,Capsule,Syrup,Injection,Cream/Ointment,Drops,Other',
            'description'          => 'nullable|string',
            'manufacturer'         => 'nullable|string|max:255',
            'primary_supplier_id'  => 'nullable|exists:suppliers,id',
            'dosage'               => 'nullable|string|max:100',
            'side_effects'         => 'nullable|string',
            'precautions_warnings'=> 'nullable|string',
            'reorder_point'        => 'nullable|integer|min:0',
            'maximum_stock_level'  => 'nullable|integer',
            'unit_cost'            => 'required|numeric|min:0',
            'unit_price'           => 'required|numeric|gte:unit_cost',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'storage_conditions'   => 'nullable|array',
            'storage_conditions.*' => 'in:Room Temperature,Refrigerated,Frozen,Protect from Light',
            'is_active'            => 'boolean',
            'medicine_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'package_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $storageConditions = $request->filled('storage_conditions') ? $request->storage_conditions : null;

        $medicine->update([
            'name'                  => $validated['name'],
            'generic_name'          => $validated['generic_name'],
            'category_id'           => $validated['category_id'],
            'medicine_type'         => $validated['medicine_type'],
            'description'           => $validated['description'] ?? null,
            'manufacturer'          => $validated['manufacturer'] ?? null,
            'primary_supplier_id'   => $validated['primary_supplier_id'] ?? null,
            'dosage'                => $validated['dosage'] ?? null,
            'side_effects'          => $validated['side_effects'] ?? null,
            'precautions_warnings'  => $validated['precautions_warnings'] ?? null,
            'reorder_point'         => $validated['reorder_point'] ?? 0,
            'maximum_stock_level'   => $validated['maximum_stock_level'] ?? null,
            'unit_cost'             => $validated['unit_cost'],
            'unit_price'            => $validated['unit_price'],
            'tax_rate'              => $validated['tax_rate'] ?? 0,
            'storage_conditions'    => $storageConditions ? json_encode($storageConditions) : null,
            'is_active'             => $request->boolean('is_active', true),
            'requires_refrigeration'=> in_array('Refrigerated', $storageConditions ?? []),
        ]);

        // Handle image updates
        if ($request->hasFile('medicine_image')) {
            if ($medicine->medicine_image && Storage::disk('public')->exists($medicine->medicine_image)) {
                Storage::disk('public')->delete($medicine->medicine_image);
            }
            $medicine->medicine_image = $request->file('medicine_image')->store('medicines/images', 'public');
        }

        if ($request->hasFile('package_image')) {
            if ($medicine->package_image && Storage::disk('public')->exists($medicine->package_image)) {
                Storage::disk('public')->delete($medicine->package_image);
            }
            $medicine->package_image = $request->file('package_image')->store('medicines/packages', 'public');
        }

        $medicine->save();

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified medicine from storage.
     */
    public function destroy(InventoryItem $medicine)
    {
        // Delete images
        if ($medicine->medicine_image && Storage::disk('public')->exists($medicine->medicine_image)) {
            Storage::disk('public')->delete($medicine->medicine_image);
        }
        if ($medicine->package_image && Storage::disk('public')->exists($medicine->package_image)) {
            Storage::disk('public')->delete($medicine->package_image);
        }

        // Delete associated batches
        $medicine->batches()->delete();

        // Delete medicine
        $medicine->delete();

        return response()->json(['success' => true]);
    }

    /**
     * DataTable endpoint for medicines listing.
     */
    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $categoryFilter = $request->category;
        $statusFilter   = $request->status;

        $query = InventoryItem::query()
            ->with(['category', 'primarySupplier'])
            ->withMin('batches', 'expiry_date')
            ->whereNotNull('generic_name')
            ->orWhere('expiry_tracking', true)
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('generic_name', 'like', "%{$searchValue}%")
                  ->orWhere('dosage', 'like', "%{$searchValue}%")
                  ->orWhereHas('category', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($categoryFilter, fn($q) => $q->where('category_id', $categoryFilter))
            ->when($statusFilter, function ($q) use ($statusFilter) {
                return match ($statusFilter) {
                    'low_stock'     => $q->lowStock(),
                    'out_of_stock'  => $q->outOfStock(),
                    'inactive'      => $q->where('is_active', false),
                    default         => $q,
                };
            });

        $totalRecords    = InventoryItem::whereNotNull('generic_name')->orWhere('expiry_tracking', true)->count();
        $filteredRecords = (clone $query)->count();

        $sortColumn = match ((int)$orderIdx) {
            1 => 'id',
            2 => 'name',
            3 => 'category_id',
            4 => 'current_stock',
            5 => 'batches_min_expiry_date',
            default => 'name',
        };

        if ($sortColumn === 'category_id') {
            $query->join('categories', 'inventory_items.category_id', '=', 'categories.id')
                  ->orderBy('categories.name', $orderDir)
                  ->select('inventory_items.*');
        } elseif ($sortColumn === 'batches_min_expiry_date') {
            $query->orderBy('batches_min_expiry_date', $orderDir ?? 'asc');
        } else {
            $query->orderBy($sortColumn, $orderDir);
        }

        $items = $query->offset($start)->limit($length)->get();

        $data = $items->map(function ($item) {
            $nearestExpiry = $item->batches_min_expiry_date
                ? \Carbon\Carbon::parse($item->batches_min_expiry_date)->format('d M Y')
                : null;

            return [
                'id'             => $item->id,
                'name'           => $item->name,
                'generic_name'   => $item->generic_name,
                'category'       => ['name' => $item->category?->name ?? '-'],
                'current_stock'  => $item->current_stock,
                'reorder_point'  => $item->reorder_point ?? 0,
                'nearest_expiry' => $nearestExpiry,
                'is_active'      => $item->is_active,
                'show_url'       => route('medicines.show', $item),
                'edit_url'       => route('medicines.edit', $item),
                'delete_url'     => route('medicines.destroy', $item),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }
}