<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchases.index')->only(['index', 'show', 'datatable', 'filters']);
        $this->middleware('permission:purchases.create')->only(['create', 'store']);
        $this->middleware('permission:purchases.edit')->only(['edit', 'update']);
        $this->middleware('permission:purchases.delete')->only(['destroy', 'bulkDelete']);
    }

    public function index()
    {
        $suppliers = Supplier::all();
        $inventoryItems = InventoryItem::where('is_active', true)->get();
        $cashRegisters = CashRegister::where('status', 'open')->get();
        return view('purchases.index', compact('suppliers', 'inventoryItems', 'cashRegisters'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $inventoryItems = InventoryItem::where('is_active', true)->get();
        return view('purchases.create', compact('suppliers', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_no' => ['required', 'string', Rule::unique('purchases')->whereNull('deleted_at')],
            'supplier_id' => 'required|exists:suppliers,id',
            'item' => 'required|exists:inventory_items,id',
            'total_qty' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'payment_status' => 'required|integer',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'reference_no' => $request->reference_no,
                'supplier_id' => $request->supplier_id,
                'item' => $request->item,
                'total_qty' => $request->total_qty,
                'total_cost' => $request->total_cost,
                'grand_total' => $request->grand_total,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'cash_register_id' => $request->cash_register_id,
                'note' => $request->note,
                'user_id' => Auth::id()
            ]);

            if ($purchase->cash_register_id) {
                CashRegisterTransaction::create([
                    'cash_register_id' => $purchase->cash_register_id,
                    'purchase_id' => $purchase->id,
                    'user_id' => Auth::id(),
                    'type' => 'purchase',
                    'amount' => $purchase->grand_total,
                    'payment_method' => 'cash',
                    'happened_at' => now(),
                    'notes' => 'Purchase: ' . $purchase->reference_no,
                ]);
            }

            // Adjust Inventory Stock
            if ($purchase->status == 1) { // 1 = Received
                $inventoryItem = InventoryItem::find($purchase->item);
                if ($inventoryItem) {
                    $inventoryItem->increment('current_stock', $purchase->total_qty);
                }
            }
            DB::commit();

            if($request->ajax()) {
                return response()->json(['success' => true, 'message' => __('file.Purchase created successfully.')]);
            }
            return redirect()->route('purchases.index')->with('success', __('file.Purchase created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            if($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.Error adding purchase')]);
            }
            return redirect()->back()->with('error', __('file.Error adding purchase'));
        }
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'reference_no' => ['required', 'string', Rule::unique('purchases')->ignore($purchase->id)->whereNull('deleted_at')],
            'supplier_id' => 'required|exists:suppliers,id',
            'item' => 'required|exists:inventory_items,id',
            'total_qty' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'payment_status' => 'required|integer',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
        ]);

        DB::beginTransaction();
        try {
            $oldItem = $purchase->item;
            $oldQty = $purchase->total_qty;
            $oldStatus = $purchase->status;

            $purchase->update([
                'reference_no' => $request->reference_no,
                'supplier_id' => $request->supplier_id,
                'item' => $request->item,
                'total_qty' => $request->total_qty,
                'total_cost' => $request->total_cost,
                'grand_total' => $request->grand_total,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'cash_register_id' => $request->cash_register_id,
                'note' => $request->note
            ]);

            // Reconcile cash register transaction
            if ($purchase->wasChanged('cash_register_id') || $purchase->wasChanged('grand_total')) {
                CashRegisterTransaction::where('purchase_id', $purchase->id)->delete();

                if ($purchase->cash_register_id) {
                    CashRegisterTransaction::create([
                        'cash_register_id' => $purchase->cash_register_id,
                        'purchase_id' => $purchase->id,
                        'user_id' => Auth::id(),
                        'type' => 'purchase',
                        'amount' => $purchase->grand_total,
                        'payment_method' => 'cash',
                        'happened_at' => now(),
                        'notes' => 'Purchase (updated): ' . $purchase->reference_no,
                    ]);
                }
            }

            // Adjust Stock Logic - We need to reverse the old effect if it was Received (1), and apply the new effect if new status is Received
            // To be precise:
            // 1. Revert old stock if oldStatus was 1
            if ($oldStatus == 1 && $oldItem) {
                InventoryItem::where('id', $oldItem)->decrement('current_stock', $oldQty);
            }
            // 2. Apply new stock if newStatus is 1
            if ($purchase->status == 1 && $purchase->item) {
                InventoryItem::where('id', $purchase->item)->increment('current_stock', $purchase->total_qty);
            }

            DB::commit();

            if($request->ajax()) {
                return response()->json(['success' => true, 'message' => __('file.Purchase updated successfully.')]);
            }
            return redirect()->route('purchases.index')->with('success', __('file.Purchase updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            if($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.Error updating purchase')]);
            }
            return redirect()->back()->with('error', __('file.Error updating purchase'));
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_items_selected')]);
        }

        if (is_array($ids)) {
            $idsArray = $ids;
        } else {
            $idsArray = array_filter(explode(',', $ids), 'is_numeric');
        }

        if (empty($idsArray)) {
            return response()->json(['success' => false, 'message' => __('file.invalid_selection')]);
        }

        DB::beginTransaction();
        try {
            $purchases = Purchase::whereIn('id', $idsArray)->get();
            $deletedCount = 0;

            foreach ($purchases as $purchase) {
                // Decrement stock if item was 'Received'
                if ($purchase->status == 1 && $purchase->item) {
                    InventoryItem::where('id', $purchase->item)->decrement('current_stock', $purchase->total_qty);
                }
                CashRegisterTransaction::where('purchase_id', $purchase->id)->delete();
                Purchase::destroy($purchase->id);
                $deletedCount++;
            }

            DB::commit();

            if ($deletedCount === 0) {
                return response()->json(['success' => false, 'message' => __('file.No items deleted')]);
            }

            return response()->json([
                'success' => true,
                'message' => trans_choice('file.Items deleted successfully', $deletedCount, ['count' => $deletedCount])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('file.Error deleting items')]);
        }
    }

    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            if ($purchase->status == 1 && $purchase->item) {
                InventoryItem::where('id', $purchase->item)->decrement('current_stock', $purchase->total_qty);
            }
            CashRegisterTransaction::where('purchase_id', $purchase->id)->delete();
            $purchase->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => __('file.Purchase deleted successfully.')]);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('file.Error deleting purchase')]);
        }
    }

    public function datatable(Request $request)
    {
        $query = Purchase::with(['supplier', 'inventoryItem', 'user']);

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        return DataTables::of($query)
            ->addColumn('edit_url', fn($row) => Auth::user()->can('purchases.edit') ? route('purchases.edit', $row) : null)
            ->addColumn('delete_url', fn($row) => Auth::user()->can('purchases.delete') ? route('purchases.destroy', $row) : null)
            ->addColumn('supplier_name', fn($row) => $row->supplier ? $row->supplier->name : '-')
            ->addColumn('item_name', fn($row) => $row->inventoryItem ? $row->inventoryItem->name : '-')
            ->addColumn('cash_register_name', fn($row) => $row->cashRegister ? 'CR-' . str_pad($row->cashRegister->id, 4, '0', STR_PAD_LEFT) : '-')
            ->addColumn('user_name', fn($row) => $row->user ? $row->user->name : '-')
            ->editColumn('grand_total', fn($row) => number_format($row->grand_total, 2))
            ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
            ->make(true);
    }

    public function filters(Request $request)
    {
        $column = $request->get('column');
        if ($column === 'supplier') {
            return response()->json(Supplier::pluck('name', 'id'));
        }
        return response()->json([]);
    }
}
