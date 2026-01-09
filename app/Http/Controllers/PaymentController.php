<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index');
    }

    
    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $search = trim($request->input('search.value', ''));

        $query = Payment::query()
            ->with(['invoice.patient', 'user'])
            ->select('payments.*')
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('invoice.patient', function ($sq) use ($search) {
                    $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                       ->orWhere('medical_record_number', 'like', "%{$search}%");
                })
                ->orWhereHas('invoice', fn($sq) => $sq->where('invoice_number', 'like', "%{$search}%"))
                ->orWhere('reference', 'like', "%{$search}%");
            });

        $totalRecords = Payment::count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        if ($orderColumnIndex == 1) $query->orderBy('payment_date', $orderDir);
        elseif ($orderColumnIndex == 2) $query->orderBy('invoice_number', $orderDir);
        elseif ($orderColumnIndex == 3) $query->orderBy('amount', $orderDir);
        elseif ($orderColumnIndex == 4) $query->orderBy('method', $orderDir);
        else $query->orderBy('created_at', 'desc');

        $payments = $query->offset($start)->limit($length)->get();

        $data = $payments->map(function ($p) {
            return [
                'id'             => $p->id,
                'payment_date'   => $p->payment_date->format('d M Y'),
                'invoice_number' => $p->invoice->invoice_number ?? '-',
                'patient_name'   => $p->invoice->patient?->getFullNameAttribute() ?? '-',
                'amount'         => '$' . number_format($p->amount, 2),
                'method'         => ucfirst(str_replace('_', ' ', $p->method)),
                'reference'      => $p->reference ?? '-',
                'recorded_by'    => $p->user?->name ?? 'System',
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function store(Request $request, BillingInvoice $invoice)
    {
        $request->validate([
            'amount'        => 'required|numeric|gt:0',
            'payment_date'  => 'required|date',
            'method'        => 'required|in:cash,card,bank_transfer,cheque,other',
            'reference'     => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
        ]);

        $remaining = $invoice->balance_due;

        if ($request->amount > $remaining) {
            return back()->withErrors(['amount' => "Amount cannot exceed balance due (\$" . number_format($remaining, 2) . ")"]);
        }

        DB::transaction(function () use ($request, $invoice) {
            Payment::create([
                'invoice_id'    => $invoice->id,
                'amount'        => $request->amount,
                'payment_date'  => $request->payment_date,
                'method'        => $request->method,
                'reference'     => $request->reference,
                'notes'         => $request->notes,
                'user_id'       => auth()->id(),
            ]);

            $newPaid = $invoice->paid_amount + $request->amount;
            $newBalance = $invoice->total - $newPaid;

            $invoice->update([
                'paid_amount' => $newPaid,
                'balance_due' => $newBalance,
                'status'      => $newBalance <= 0 ? 'paid' : 'partially_paid',
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
