<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use App\Notifications\AppointmentPaid;

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
        if ($orderColumnIndex == 1)
            $query->orderBy('payment_date', $orderDir);
        elseif ($orderColumnIndex == 2)
            $query->orderBy('invoice_number', $orderDir);
        elseif ($orderColumnIndex == 3)
            $query->orderBy('amount', $orderDir);
        elseif ($orderColumnIndex == 4)
            $query->orderBy('method', $orderDir);
        else
            $query->orderBy('created_at', 'desc');

        $payments = $query->offset($start)->limit($length)->get();

        $data = $payments->map(function ($p) {
            $actions = '<div class="flex items-center justify-end gap-1">'
                . '<a href="' . route('invoices.show', $p->invoice_id) . '" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="' . __('file.show_invoice') . '">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
                . '</a>';
            if (\Auth::user()->can('payments.edit')) {
                $actions .= '<button type="button" onclick="openEditDrawer(' . $p->id . ')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="' . __('file.edit_payment') . '">'
                    . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
                    . '</button>';
            }
            if (\Auth::user()->can('payments.delete')) {
                $actions .= '<button type="button" onclick="confirmDelete(\'' . route('payments.destroy', $p) . '\')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="' . __('file.delete_payment') . '">'
                    . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
                    . '</button>';
            }
            $actions .= '</div>';

            return [
                'id' => $p->id,
                'payment_date' => $p->payment_date->format('d M Y'),
                'invoice_number' => $p->invoice->invoice_number ?? '-',
                'patient_name' => $p->invoice->patient?->getFullNameAttribute() ?? '-',
                'amount' => '$' . number_format($p->amount, 2),
                'total_amount' => $p->amount,
                'method' => ucfirst(str_replace('_', ' ', $p->method)),
                'reference' => $p->reference ?? '-',
                'recorded_by' => $p->user?->name ?? 'System',
                'actions' => $actions,
                'delete_url' => route('payments.destroy', $p),
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function store(Request $request, BillingInvoice $invoice)
    {
        if (request()->ajax()) {
            try {
                $request->validate([
                    'amount' => 'required|numeric|gt:0',
                    'payment_date' => 'required|date',
                    'method' => 'required|in:cash,card,bank_transfer,cheque,other',
                    'reference' => 'nullable|string|max:255',
                    'notes' => 'nullable|string',
                ]);

                $remaining = $invoice->balance_due;

                if ($request->amount > $remaining) {
                    return response()->json([
                        'success' => false,
                        'message' => "Amount cannot exceed balance due (" . ($currency_code ?? 'LKR') . " " . number_format($remaining, 2) . ")"
                    ], 422);
                }

                DB::transaction(function () use ($request, $invoice) {
                    $payment = Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $request->amount,
                        'payment_date' => $request->payment_date,
                        'method' => $request->input('method'),
                        'reference' => $request->reference,
                        'notes' => $request->notes,
                        'user_id' => auth()->id(),
                    ]);

                    $extraUsers = [];
                    if ($invoice->appointment && $invoice->appointment->doctor && $invoice->appointment->doctor->user) {
                        $extraUsers[] = $invoice->appointment->doctor->user;
                    }

                    NotificationService::send('appointment_paid', new AppointmentPaid($payment), $extraUsers);

                    $newPaid = $invoice->paid_amount + $request->amount;
                    $newBalance = $invoice->total - $newPaid;

                    $invoice->update([
                        'paid_amount' => $newPaid,
                        'balance_due' => $newBalance,
                        'status' => $newBalance <= 0 ? 'paid' : 'partially_paid',
                    ]);
                });

                return response()->json([
                    'success' => true,
                    'message' => __('file.payment_recorded_successfully')
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first(),
                    'errors' => $e->validator->errors()
                ], 422);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,card,bank_transfer,cheque,other',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $remaining = $invoice->balance_due;

        if ($request->amount > $remaining) {
            $errorMessage = "Amount cannot exceed balance due (" . ($currency_code ?? 'LKR') . " " . number_format($remaining, 2) . ")";
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            return back()->withErrors(['amount' => $errorMessage]);
        }

        DB::transaction(function () use ($request, $invoice) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'method' => $request->input('method'),
                'reference' => $request->reference,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            $extraUsers = [];
            if ($invoice->appointment && $invoice->appointment->doctor && $invoice->appointment->doctor->user) {
                $extraUsers[] = $invoice->appointment->doctor->user;
            }

            NotificationService::send('appointment_paid', new AppointmentPaid($payment), $extraUsers);

            $newPaid = $invoice->paid_amount + $request->amount;
            $newBalance = $invoice->total - $newPaid;

            $invoice->update([
                'paid_amount' => $newPaid,
                'balance_due' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'partially_paid',
            ]);
        });

        return back()->with('success', __('file.payment_recorded_successfully'));
    }

    public function edit(Payment $payment)
    {
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'method' => 'required|string',
        ]);

        $payment->update([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'method' => $request->input('method'),
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.payment_updated_successfully')]);
        }

        return back()->with('success', __('file.payment_updated_successfully'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.payment_deleted_successfully')]);
        }

        return back()->with('success', __('file.payment_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.no_items_selected')], 400);
            }
            return back()->with('error', __('file.no_items_selected'));
        }

        Payment::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.payments_deleted_successfully')]);
        }

        return back()->with('success', __('file.payments_deleted_successfully'));
    }
}
