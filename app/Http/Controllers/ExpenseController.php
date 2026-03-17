<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:expenses.index')->only(['index', 'datatable', 'filters', 'show']);
        $this->middleware('permission:expenses.create')->only(['create', 'store']);
        $this->middleware('permission:expenses.edit')->only(['edit', 'update']);
        $this->middleware('permission:expenses.delete')->only(['destroy', 'bulkDelete']);
    }

    public function index()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $cashRegisters = CashRegister::where('status', 'open')->get();

        return view('expenses.index', compact('categories', 'cashRegisters'));
    }

    public function show(Expense $expense)
    {
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'id' => $expense->id,
                'reference_no' => $expense->reference_no,
                'expense_category_id' => $expense->expense_category_id,
                'category_name' => $expense->expenseCategory?->name ?? '-',
                'amount' => $expense->amount,
                'cash_register_id' => $expense->cash_register_id,
                'cash_register_name' => $expense->cashRegister ? 'CR-' . str_pad($expense->cashRegister->id, 4, '0', STR_PAD_LEFT) : '-',
                'user_name' => $expense->user?->name ?? '-',
                'note' => $expense->note ?? '',
                'created_at' => $expense->created_at->format('Y-m-d H:i'),
            ]);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => ['required', 'string', Rule::unique('expenses')->whereNull('deleted_at')],
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
            'note' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'reference_no' => $validated['reference_no'],
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'cash_register_id' => $validated['cash_register_id'] ?? null,
            'note' => $validated['note'] ?? null,
            'user_id' => Auth::id(),
        ]);

        if ($validated['cash_register_id'] ?? false) {
            CashRegisterTransaction::create([
                'cash_register_id' => $validated['cash_register_id'],
                'expense_id' => $expense->id,
                'user_id' => Auth::id(),
                'type' => 'expense',
                'amount' => $validated['amount'],
                'payment_method' => 'cash',
                'happened_at' => now(),
                'notes' => 'Expense: ' . $expense->reference_no,
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.expense_created_successfully')]);
        }

        return redirect()->route('expenses.index')->with('success', __('file.expense_created_successfully'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'reference_no' => ['required', 'string', Rule::unique('expenses')->ignore($expense)->whereNull('deleted_at')],
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
            'note' => 'nullable|string',
        ]);

        $oldCashRegisterId = $expense->cash_register_id;
        $oldAmount = $expense->amount;

        $expense->update([
            'reference_no' => $validated['reference_no'],
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'cash_register_id' => $validated['cash_register_id'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        // Reconcile cash register transaction
        if (($validated['cash_register_id'] ?? null) != $oldCashRegisterId || $validated['amount'] != $oldAmount) {
            CashRegisterTransaction::where('expense_id', $expense->id)->delete();

            if ($validated['cash_register_id'] ?? false) {
                CashRegisterTransaction::create([
                    'cash_register_id' => $validated['cash_register_id'],
                    'expense_id' => $expense->id,
                    'user_id' => Auth::id(),
                    'type' => 'expense',
                    'amount' => $validated['amount'],
                    'payment_method' => 'cash',
                    'happened_at' => now(),
                    'notes' => 'Expense (updated): ' . $expense->reference_no,
                ]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.expense_updated_successfully')]);
        }

        return redirect()->route('expenses.index')->with('success', __('file.expense_updated_successfully'));
    }

    public function destroy(Expense $expense)
    {
        CashRegisterTransaction::where('expense_id', $expense->id)->delete();
        $expense->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('expenses.index')->with('success', __('file.expense_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')), 'is_numeric');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_items_selected')]);
        }

        DB::beginTransaction();
        try {
            CashRegisterTransaction::whereIn('expense_id', $ids)->delete();
            $count = Expense::whereIn('id', $ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans_choice('file.Items deleted successfully', $count, ['count' => $count])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('file.Error deleting items')]);
        }
    }

    public function datatable(Request $request)
    {
        $query = Expense::with(['expenseCategory', 'user', 'cashRegister.user']);

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category);
        }

        return DataTables::of($query)
            ->addColumn('category_name', fn($e) => $e->expenseCategory?->name ?? '-')
            ->addColumn('cash_register_name', fn($e) => $e->cashRegister ? 'CR-' . str_pad($e->cashRegister->id, 4, '0', STR_PAD_LEFT) : '-')
            ->addColumn('user_name', fn($e) => $e->user?->name ?? '-')
            ->editColumn('amount', fn($e) => number_format($e->amount, 2, '.', ''))
            ->editColumn('created_at', fn($e) => $e->created_at->format('Y-m-d'))
            ->addColumn('delete_url', fn($e) => route('expenses.destroy', $e))
            ->rawColumns(['actions']) // if you later add HTML in actions
            ->make(true);
    }

    public function filters(Request $request)
    {
        if ($request->column === 'category') {
            return ExpenseCategory::where('is_active', true)
                ->pluck('name', 'id')
                ->toJson();
        }

        return response()->json([]);
    }
}