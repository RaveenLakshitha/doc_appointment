<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('medicines.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('medicines.create', compact('categories', 'suppliers'));
    }

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
            'precautions_warnings' => 'nullable|string',
            'reorder_point'        => 'nullable|integer|min:0',
            'maximum_stock_level'  => 'nullable|integer|gte:reorder_point',
            'unit_cost'            => 'required|numeric|min:0',     // still required (for future purchases)
            'unit_price'           => 'required|numeric|gte:unit_cost',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            // ── Batch fields now optional ────────────────────────────────
            'batch_number'         => 'nullable|string|max:100|required_with:initial_quantity,expiry_date',
            'manufacturing_date'   => 'nullable|date',
            'expiry_date'          => 'nullable|date|after_or_equal:today|required_with:initial_quantity',
            'initial_quantity'     => 'nullable|integer|min:0|required_with:batch_number,expiry_date',
            'storage_conditions'   => 'nullable|array',
            'storage_conditions.*' => 'in:Room Temperature,Refrigerated,Frozen,Protect from Light',
            'is_active'            => 'boolean',
            'medicine_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'package_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $storageConditions = $request->filled('storage_conditions') ? $request->storage_conditions : null;

        $medicine = DB::transaction(function () use ($validated, $request, $storageConditions) {

            // 1. Create InventoryItem – always with 0 stock
            $item = InventoryItem::create([
                'name'                => $validated['name'],
                'category_id'         => $validated['category_id'],
                'description'         => $validated['description'] ?? null,
                'manufacturer'        => $validated['manufacturer'] ?? null,
                'primary_supplier_id' => $validated['primary_supplier_id'] ?? null,
                'current_stock'       => 0,
                'reorder_point'       => $validated['reorder_point'] ?? 0,
                'maximum_stock_level' => $validated['maximum_stock_level'] ?? null,
                'unit_cost'           => $validated['unit_cost'],
                'unit_price'          => $validated['unit_price'],
                'expiry_tracking'     => true,
            ]);

            // 2. Create Medicine record
            $medicine = Medicine::create([
                'inventory_item_id'    => $item->id,
                'generic_name'         => $validated['generic_name'],
                'medicine_type'        => $validated['medicine_type'],
                'dosage'               => $validated['dosage'] ?? null,
                'side_effects'         => $validated['side_effects'] ?? null,
                'precautions_warnings' => $validated['precautions_warnings'] ?? null,
                'tax_rate'             => $validated['tax_rate'] ?? 0,
                'storage_conditions'   => $storageConditions ? json_encode($storageConditions) : null,
                'is_active'            => $request->boolean('is_active', true),
            ]);

            // 3. Images
            if ($request->hasFile('medicine_image')) {
                $medicine->medicine_image = $request->file('medicine_image')->store('medicines/images', 'public');
            }
            if ($request->hasFile('package_image')) {
                $medicine->package_image = $request->file('package_image')->store('medicines/packages', 'public');
            }
            $medicine->save();

            // 4. Optional: create first batch only if quantity > 0 and batch info provided
            if ($request->filled('initial_quantity') && $validated['initial_quantity'] > 0 &&
                $request->filled('batch_number') && $request->filled('expiry_date')) {

                MedicineBatch::create([
                    'medicine_id'         => $medicine->id,
                    'batch_number'        => $validated['batch_number'],
                    'manufacturing_date'  => $validated['manufacturing_date'] ?? null,
                    'expiry_date'         => $validated['expiry_date'],
                    'initial_quantity'    => $validated['initial_quantity'],
                    'current_quantity'    => $validated['initial_quantity'],
                    // You can add purchase_price later in Purchases controller
                ]);

                $item->current_stock = $validated['initial_quantity'];
                $item->save();
            }

            return $medicine;
        });

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine added successfully.' . 
                ($medicine->inventoryItem->current_stock > 0 ? ' Initial stock recorded.' : ' No initial stock added – you can add batches later.'));
    }

    public function show(Medicine $medicine)
    {
        $medicine->load([
            'inventoryItem.category',
            'inventoryItem.primarySupplier',
            'batches'
        ]);

        return view('medicines.show', compact('medicine'));
    }

    public function edit(Medicine $medicine)
    {
        $medicine->load('inventoryItem');

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('medicines.edit', compact('medicine', 'categories', 'suppliers'));
    }

    public function update(Request $request, Medicine $medicine)
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
            'precautions_warnings' => 'nullable|string',
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

        DB::transaction(function () use ($request, $validated, $storageConditions, $medicine) {

            // Update InventoryItem
            $medicine->inventoryItem->update([
                'name'                => $validated['name'],
                'category_id'         => $validated['category_id'],
                'description'         => $validated['description'] ?? null,
                'manufacturer'        => $validated['manufacturer'] ?? null,
                'primary_supplier_id' => $validated['primary_supplier_id'] ?? null,
                'reorder_point'       => $validated['reorder_point'] ?? 0,
                'maximum_stock_level' => $validated['maximum_stock_level'] ?? null,
                'unit_cost'           => $validated['unit_cost'],
                'unit_price'          => $validated['unit_price'],
            ]);

            // Update Medicine
            $medicine->update([
                'generic_name'         => $validated['generic_name'],
                'medicine_type'        => $validated['medicine_type'],
                'dosage'               => $validated['dosage'] ?? null,
                'side_effects'         => $validated['side_effects'] ?? null,
                'precautions_warnings' => $validated['precautions_warnings'] ?? null,
                'tax_rate'             => $validated['tax_rate'] ?? 0,
                'storage_conditions'   => $storageConditions ? json_encode($storageConditions) : null,
                'is_active'            => $request->boolean('is_active', true),
            ]);

            // Handle image replacement
            if ($request->hasFile('medicine_image')) {
                if ($medicine->medicine_image) {
                    Storage::disk('public')->delete($medicine->medicine_image);
                }
                $medicine->medicine_image = $request->file('medicine_image')->store('medicines/images', 'public');
            }

            if ($request->hasFile('package_image')) {
                if ($medicine->package_image) {
                    Storage::disk('public')->delete($medicine->package_image);
                }
                $medicine->package_image = $request->file('package_image')->store('medicines/packages', 'public');
            }

            $medicine->save();
        });

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        DB::transaction(function () use ($medicine) {
            // Delete images
            if ($medicine->medicine_image) {
                Storage::disk('public')->delete($medicine->medicine_image);
            }
            if ($medicine->package_image) {
                Storage::disk('public')->delete($medicine->package_image);
            }

            // Batches are cascade-deleted via foreign key
            $medicine->delete();           // deletes Medicine
            // InventoryItem can stay or be deleted depending on business rule
            // $medicine->inventoryItem->delete(); // ← only if you want to delete the item too
        });

        return response()->json(['success' => true]);
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
        $statusFilter   = $request->status;

        $query = Medicine::query()
            ->with(['inventoryItem.category', 'inventoryItem.primarySupplier'])
            ->withMin('batches', 'expiry_date', 'min_expiry_date')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->where('generic_name', 'like', "%{$searchValue}%")
                  ->orWhereHas('inventoryItem', function ($sq) use ($searchValue) {
                      $sq->where('name', 'like', "%{$searchValue}%")
                         ->orWhere('description', 'like', "%{$searchValue}%");
                  })
                  ->orWhere('dosage', 'like', "%{$searchValue}%")
                  ->orWhereHas('inventoryItem.category', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($categoryFilter, fn($q) => $q->whereHas('inventoryItem', fn($sq) => $sq->where('category_id', $categoryFilter)))
            ->when($statusFilter, function ($q) use ($statusFilter) {
                return match ($statusFilter) {
                    'low_stock'    => $q->whereHas('inventoryItem', fn($sq) => $sq->whereColumn('current_stock', '<=', 'reorder_point')),
                    'out_of_stock' => $q->whereHas('inventoryItem', fn($sq) => $sq->where('current_stock', 0)),
                    'inactive'     => $q->where('is_active', false),
                    default        => $q,
                };
            });

        $totalRecords    = Medicine::count();
        $filteredRecords = (clone $query)->count();

        // Sorting logic (adjusted for relations)
        $sortColumn = match ((int)$orderIdx) {
            2 => 'inventory_items.name',     // assuming name is from item
            4 => 'inventory_items.current_stock',
            5 => 'min_expiry_date',
            default => 'generic_name',
        };

        if (str_contains($sortColumn, 'inventory_items.')) {
            $query->join('inventory_items', 'medicines.inventory_item_id', '=', 'inventory_items.id')
                  ->orderBy($sortColumn, $orderDir);
        } else {
            $query->orderBy($sortColumn, $orderDir);
        }

        $medicines = $query->offset($start)->limit($length)->get();

        $data = $medicines->map(function ($medicine) {
            $item = $medicine->inventoryItem;
            $nearestExpiry = $medicine->min_expiry_date
                ? \Carbon\Carbon::parse($medicine->min_expiry_date)->format('d M Y')
                : null;

            return [
                'id'             => $medicine->id,
                'name'           => $item?->name ?? '-',
                'generic_name'   => $medicine->generic_name,
                'category'       => ['name' => $item?->category?->name ?? '-'],
                'current_stock'  => $item?->current_stock ?? 0,
                'reorder_point'  => $item?->reorder_point ?? 0,
                'nearest_expiry' => $nearestExpiry,
                'is_active'      => $medicine->is_active,
                'show_url'       => route('medicines.show', $medicine),
                'edit_url'       => route('medicines.edit', $medicine),
                'delete_url'     => route('medicines.destroy', $medicine),
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