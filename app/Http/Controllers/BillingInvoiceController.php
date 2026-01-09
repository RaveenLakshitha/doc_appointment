<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\Service;
use App\Models\InventoryItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingInvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('invoices.index');
    }

    public function datatable(Request $request)
{
    $draw = $request->input('draw');
    $start = $request->input('start', 0);
    $length = $request->input('length', 10);
    $orderColumnIndex = $request->input('order.0.column');
    $orderDir = $request->input('order.0.dir', 'asc');
    $search = trim($request->input('search.value', ''));

    $status = $request->status;
    $from = $request->from;
    $to = $request->to;

    $query = BillingInvoice::query()
        ->with('patient')
        ->select('billing_invoices.*')
        ->when($search !== '', function ($q) use ($search) {
            $q->whereHas('patient', function ($sq) use ($search) {
                $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                   ->orWhere('medical_record_number', 'like', "%{$search}%");
            })
            ->orWhere('invoice_number', 'like', "%{$search}%");
        })
        ->when($status !== null && $status !== '', fn($q) => $q->where('status', $status))
        ->when($from || $to, function ($q) use ($from, $to) {
            if ($from && $to) {
                $q->whereBetween('invoice_date', [$from, $to]);
            } elseif ($from) {
                $q->where('invoice_date', '>=', $from);
            } elseif ($to) {
                $q->where('invoice_date', '<=', $to);
            }
        });

    $totalRecords = BillingInvoice::count();
    $filteredRecords = (clone $query)->count();

    if ($orderColumnIndex == 0) {
        $query->orderBy('invoice_number', $orderDir);
    } elseif ($orderColumnIndex == 1) {
        $query->join('patients', 'billing_invoices.patient_id', '=', 'patients.id')
              ->orderBy('patients.first_name', $orderDir)
              ->orderBy('patients.last_name', $orderDir)
              ->select('billing_invoices.*');
    } elseif ($orderColumnIndex == 2) {
        $query->orderBy('invoice_date', $orderDir);
    } elseif ($orderColumnIndex == 3) {
        $query->orderBy('total', $orderDir);
    } elseif ($orderColumnIndex == 4) {
        $query->orderBy('balance_due', $orderDir);
    } elseif ($orderColumnIndex == 5) {
        $query->orderByRaw("FIELD(status, 'paid', 'partially_paid', 'sent', 'overdue') {$orderDir}");
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $invoices = $query->offset($start)->limit($length)->get();

    $data = $invoices->map(function ($i) {
        $statusBadge = match ($i->status) {
            'paid'           => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Paid</span>',
            'partially_paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Partially Paid</span>',
            'overdue'        => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Overdue</span>',
            default          => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Sent</span>',
        };

        $actions = '<div class="flex items-center justify-end gap-1">'
            . '<a href="' . route('invoices.show', $i) . '" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">'
            . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
            . '</a>'
            . '<a href="' . route('invoices.print', $i) . '" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">'
            . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>'
            . '</a>'
            . '</div>';

        return [
            'invoice_number' => $i->invoice_number,
            'patient_name'   => $i->patient?->getFullNameAttribute() ?? 'N/A',
            'invoice_date'   => $i->invoice_date->format('M d, Y'),
            'total'          => '$' . number_format($i->total, 2),
            'balance_due'    => '$' . number_format($i->balance_due, 2),
            'status_html'    => $statusBadge,
            'actions'        => $actions,
        ];
    });

    return response()->json([
        'draw'            => (int)$draw,
        'recordsTotal'    => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data'            => $data->toArray(),
    ]);
}

    public function filters(Request $request)
    {
        $column = (int) $request->get('column');

        return match ($column) {
            2 => $this->uniqueValues(
                raw: "TRIM(CONCAT(COALESCE(p.first_name,''), ' ', COALESCE(p.middle_name,''), ' ', COALESCE(p.last_name,'')))",
                alias: 'patient_name',
                join: 'patients p ON p.id = billing_invoices.patient_id'
            ),
            6 => $this->uniqueValues('status'),
            default => response()->json([]),
        };
    }

    private function uniqueValues(string $field = null, ?string $raw = null, string $alias = null, string $join = null)
    {
        $query = BillingInvoice::query();

        if ($join) {
            $query->join(DB::raw($join), fn($j) => $j);
        }

        if ($raw) {
            $query->selectRaw("$raw AS `$alias`");
            $orderBy = $alias;
        } else {
            $query->select("billing_invoices.$field");
            $orderBy = $field;
        }

        return $query
            ->distinct()
            ->orderBy($orderBy)
            ->pluck($orderBy)
            ->filter()
            ->values()
            ->toArray();
    }

    public function pos()
    {
        $patients = Patient::active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'medical_record_number']);

        $services = Service::active()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'description']);

        $inventoryItems = InventoryItem::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'generic_name', 'unit_price', 'current_stock', 'medicine_image']);

        return view('invoices.pos', compact('patients', 'services', 'inventoryItems'));
    }

    public function posStore(Request $request)
    {
        $request->validate([
            'patient_id'          => 'required|exists:patients,id',
            'items'               => 'required|array|min:1',
            'items.*.type'        => 'required|in:service,inventory',
            'items.*.id'          => 'required|integer',
            'items.*.quantity'    => 'required|integer|min:1',
            'tax_rate'            => 'nullable|numeric|min:0|max:100',
            'discount_amount'     => 'nullable|numeric|min:0',
            'payment_method'      => 'required|in:cash,card,bank_transfer,cheque,other',
            'payment_reference'   => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($request->items as $cartItem) {
                $type = $cartItem['type'];
                $id = $cartItem['id'];
                $quantity = $cartItem['quantity'];

                if ($type === 'service') {
                    $model = Service::findOrFail($id);
                    $price = $model->price;
                    $name = $model->name;
                } else {
                    $model = InventoryItem::where('is_active', true)->findOrFail($id);
                    if ($model->current_stock < $quantity) {
                        throw new \Exception("Not enough stock for {$model->name}");
                    }
                    $price = $model->unit_price;
                    $name = $model->name . ($model->generic_name ? " ({$model->generic_name})" : '');
                }

                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'itemable_type' => $type === 'service' ? Service::class : InventoryItem::class,
                    'itemable_id'   => $model->id,
                    'description'   => $name,
                    'quantity'      => $quantity,
                    'unit_price'    => $price,
                    'total'         => $lineTotal,
                ];
            }

            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = $subtotal * ($taxRate / 100);
            $discount = $request->discount_amount ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            $invoice = BillingInvoice::create([
                'invoice_number'   => $this->generateInvoiceNumber(),
                'patient_id'       => $request->patient_id,
                'invoice_date'     => now()->format('Y-m-d'),
                'due_date'         => now()->format('Y-m-d'),
                'type'             => 'POS Invoice',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'discount_amount'  => $discount,
                'total'            => $total,
                'paid_amount'      => $total,
                'balance_due'      => 0,
                'status'           => 'paid',
                'notes'            => $request->notes,
            ]);

            foreach ($itemsToCreate as $item) {
                BillingInvoiceItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
            }

            Payment::create([
                'invoice_id'    => $invoice->id,
                'amount'        => $total,
                'payment_date'  => now()->format('Y-m-d'),
                'method'        => $request->payment_method,
                'reference'     => $request->payment_reference,
                'notes'         => $request->notes ?? 'POS payment',
                'user_id'       => auth()->id(),
            ]);

            foreach ($request->items as $cartItem) {
                if ($cartItem['type'] === 'inventory') {
                    InventoryItem::find($cartItem['id'])->decrement('current_stock', $cartItem['quantity']);
                }
            }

            return response()->json([
                'success'        => true,
                'message'        => 'Sale and payment completed successfully!',
                'invoice_id'     => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total'          => number_format($total, 2),
            ]);
        });
    }

    public function show(BillingInvoice $invoice)
    {
        $invoice->load(['patient', 'items.itemable', 'payments.user']);
        return view('invoices.show', compact('invoice'));
    }

    public function print(BillingInvoice $invoice)
    {
        $invoice->load(['patient', 'items.itemable', 'payments.user']);
        $pdf = \PDF::loadView('invoices.print', compact('invoice'))
            ->setPaper('a4')
            ->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . Carbon::now()->format('Ymd');
        $last = BillingInvoice::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();
        $seq = $last ? ((int) substr($last->invoice_number, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}