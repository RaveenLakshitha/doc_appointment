<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:expense-categories.index')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:expense-categories.create')->only(['store']);
        $this->middleware('permission:expense-categories.edit')->only(['update']);
        $this->middleware('permission:expense-categories.delete')->only(['destroy', 'bulkDelete']);
    }

    public function index()
    {
        return view('expense_categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:255', Rule::unique('expense_categories')->whereNull('deleted_at')],
            'name'      => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);

        ExpenseCategory::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.ExpenseCategory created successfully.')
            ]);
        }

        return redirect()->route('expense-categories.index')->with('success', __('file.ExpenseCategory created successfully.'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:255', Rule::unique('expense_categories')->ignore($expenseCategory->id)->whereNull('deleted_at')],
            'name'      => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);

        $expenseCategory->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('file.ExpenseCategory updated successfully.')
            ]);
        }

        return redirect()->route('expense-categories.index')->with('success', __('file.ExpenseCategory updated successfully.'));
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->trashed()) {
            return redirect()->route('expense-categories.index')
                ->with('info', __('file.Item already deleted'));
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', __('file.ExpenseCategory deleted successfully.'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => __('file.no_items_selected')]);
        }

        $idsArray = array_filter(explode(',', $ids), 'is_numeric');

        if (empty($idsArray)) {
            return response()->json(['success' => false, 'message' => __('file.invalid_selection')]);
        }

        $deletedCount = ExpenseCategory::whereIn('id', $idsArray)
            ->whereNull('deleted_at')
            ->delete();

        if ($deletedCount === 0) {
            return response()->json(['success' => false, 'message' => __('file.No items deleted')]);
        }

        return response()->json([
            'success' => true,
            'message' => trans_choice('file.Items deleted successfully', $deletedCount, ['count' => $deletedCount])
        ]);
    }

    public function datatable(Request $request)
    {
        $query = ExpenseCategory::query();

        return DataTables::of($query)
            ->addColumn('edit_url', fn($row) => Auth::user()->can('expense-categories.edit') ? route('expense-categories.edit', $row) : null)
            ->addColumn('delete_url', fn($row) => Auth::user()->can('expense-categories.delete') ? route('expense-categories.destroy', $row) : null)
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }
}
