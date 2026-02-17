<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    /**
     * Display a listing of cash registers (for DataTables)
     */
    public function index()
    {
        return view('cash-registers.index');
    }

    /**
     * DataTables server-side response
     */
    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $search      = trim($request->input('search.value', ''));
        $orderColumn = $request->input('order.0.column', 2);
        $orderDir    = $request->input('order.0.dir', 'desc');

        $query = CashRegister::query()
            ->with('user')
            ->select('cash_registers.*');

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $totalRecords    = CashRegister::count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        switch ($orderColumn) {
            case 1: // user
                $query->join('users', 'cash_registers.user_id', '=', 'users.id')
                      ->orderBy('users.name', $orderDir);
                break;
            case 2: // opened_at
                $query->orderBy('opened_at', $orderDir);
                break;
            case 6: // status
                $query->orderByRaw("FIELD(status, 'open', 'closed', 'reconciled', 'discrepancy') {$orderDir}");
                break;
            default:
                $query->orderBy('opened_at', 'desc');
        }

        $registers = $query->offset($start)->limit($length)->get();

        $data = $registers->map(function ($register) {
            $statusBadge = match ($register->status) {
                'open'       => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Open</span>',
                'closed'     => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Closed</span>',
                'reconciled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Reconciled</span>',
                default      => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Discrepancy</span>',
            };

            return [
                'id'                      => $register->id,
                'user_name'               => $register->user?->name ?? '—',
                'opened_at_formatted'     => $register->opened_at->format('Y-m-d H:i'),
                'opening_balance_formatted' => number_format($register->opening_balance, 2),
                'expected_closing_formatted' => $register->expected_closing_balance ? number_format($register->expected_closing_balance, 2) : '—',
                'actual_closing_formatted'  => $register->actual_closing_balance ? number_format($register->actual_closing_balance, 2) : '—',
                'status_html'             => $statusBadge,
                'status'                  => $register->status,
                'difference'              => $register->difference ? number_format($register->difference, 2) : null,
            ];
        });

        return response()->json([
            'draw'            => (int) $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    /**
     * Show single cash register details
     */
    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load([
            'user',
            'transactions' => fn($q) => $q->orderByDesc('happened_at'),
            'invoices.patient',
            'payments',
        ]);

        // Calculate expected if not already set
        if ($cashRegister->isOpen() || !$cashRegister->expected_closing_balance) {
            $cashRegister->expected_closing_balance = $cashRegister->calculateExpectedClosingBalance();
        }

        return view('cash-registers.show', compact('cashRegister'));
    }

    /**
     * Open a new cash register
     */
    public function open(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        if ($user->cashRegisters()->whereNull('closed_at')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an open cash register. Please close it first.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $register = CashRegister::create([
                'user_id'         => $user->id,
                'opening_balance' => $request->opening_balance,
                'status'          => 'open',
                'opened_at'       => now(),
                'notes'           => $request->notes,
            ]);

            // Optional: record the opening balance as transaction
            $register->transactions()->create([
                'user_id'    => $user->id,
                'type'       => 'opening_balance',
                'amount'     => $request->opening_balance,
                'notes'      => 'Initial cash counted',
                'happened_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash register opened successfully.',
                // optionally return the new register data if you want to auto-open drawer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to open register: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close and reconcile cash register
     */
    public function close(Request $request, CashRegister $cashRegister)
    {
        // Authorization: only owner or admin
        if ($cashRegister->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You are not authorized to close this register.');
        }

        if (!$cashRegister->isOpen()) {
            return back()->withErrors(['This register is already closed.']);
        }

        $request->validate([
            'actual_closing_balance' => 'required|numeric|min:0',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $expected = $cashRegister->calculateExpectedClosingBalance();

            $difference = round($request->actual_closing_balance - $expected, 2);

            $status = $difference == 0 ? 'closed' : 'discrepancy';

            $cashRegister->update([
                'actual_closing_balance'   => $request->actual_closing_balance,
                'expected_closing_balance' => $expected,
                'difference'               => $difference,
                'closed_at'                => now(),
                'status'                   => $status,
                'notes'                    => $request->notes ?: $cashRegister->notes,
            ]);

            // Log closing transaction
            $cashRegister->transactions()->create([
                'user_id' => Auth::id(),
                'type'    => 'close_register',
                'amount'  => $request->actual_closing_balance,
                'notes'   => "Closed register - Difference: {$difference}",
            ]);

            DB::commit();

            return redirect()->route('cash-registers.show', $cashRegister)
                ->with('success', 'Cash register closed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['Failed to close register: ' . $e->getMessage()]);
        }
    }

    public function current()
{
    $register = auth()->user()->cashRegisters()
        ->whereNull('closed_at')
        ->latest('opened_at')
        ->first();

    if (!$register) {
        return response()->json(['open' => false]);
    }

    // Calculate real stats (example)
    $cashSales = $register->transactions()
        ->where('type', 'cash_sale')
        ->sum('amount');

    $cardSales = $register->transactions()
        ->whereIn('type', ['card_sale', 'card_payment'])
        ->sum('amount');

    $transactionCount = $register->transactions()->count();

    // Expected = opening + cash in - cash out (adjust according to your logic)
    $expected = $register->calculateExpectedClosingBalance() ?? $register->opening_balance;

    return response()->json([
        'open' => true,
        'register' => [
            'id' => $register->id,
            'opened_at_formatted' => $register->opened_at->format('M d, Y h:i A'),
            'opening_balance_formatted' => number_format($register->opening_balance, 2),
            'expected_closing_formatted' => number_format($expected, 2),
            'cash_sales_formatted' => number_format($cashSales, 2),
            'card_sales_formatted' => number_format($cardSales, 2),
            'transaction_count' => $transactionCount,
            // Add more as needed
        ]
    ]);
}
}