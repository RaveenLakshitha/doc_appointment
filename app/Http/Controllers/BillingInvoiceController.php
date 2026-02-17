<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\Service;
use App\Models\InventoryItem;
use App\Models\Treatment;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Doctor;
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

    public function pos(Request $request)
    {
        $patients = Patient::active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'medical_record_number']);

        $doctors = Doctor::active()
            ->orderByRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name)")
            ->get(['id', 'first_name', 'middle_name', 'last_name']);

        $services = Service::active()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'description']);

        $inventoryItems = InventoryItem::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'generic_name', 'unit_price', 'current_stock', 'medicine_image']);

        $preloadedItems = [];
        $preselectedPatientId = null;
        $preselectedDoctorId = null;

        $appointmentId = $request->query('appointment_id');

        if ($appointmentId) {
            $appointment = Appointment::with([
                'patient',
                'doctor',
                'treatments',
                'prescriptions.medications.inventoryItem'
            ])->find($appointmentId);

            if ($appointment) {
                $preselectedPatientId = $appointment->patient_id;
                $preselectedDoctorId = $appointment->doctor_id;

                foreach ($appointment->treatments as $treatment) {
                    $price = $treatment->pivot->price_at_time
                        ?? $treatment->doctors()->find($appointment->doctor_id)?->pivot->price
                        ?? $treatment->price ?? 0;

                    $preloadedItems[] = [
                        'type'     => 'treatment',
                        'id'       => $treatment->id,
                        'name'     => $treatment->name,
                        'price'    => $price,
                        'quantity' => $treatment->pivot->quantity ?? 1,
                        'source'   => 'appointment',
                        'doctor_id' => $appointment->doctor_id,
                    ];
                }

                $latestPrescription = $appointment->prescriptions
                    ->sortByDesc('prescription_date')
                    ->first();

                if ($latestPrescription) {
                    foreach ($latestPrescription->medications as $med) {
                        $item = $med->inventoryItem;

                        if (!$item || $item->current_stock <= 0) {
                            continue;
                        }

                        $preloadedItems[] = [
                            'type'     => 'inventory',
                            'id'       => $item->id,
                            'name'     => $med->display_name,
                            'price'    => $item->unit_price,
                            'quantity' => $med->duration_days ?? 1,
                            'source'   => 'prescription',
                        ];
                    }
                }
            }
        }

        return view('invoices.pos', compact(
            'patients',
            'doctors',
            'services',
            'inventoryItems',
            'preloadedItems',
            'preselectedPatientId',
            'preselectedDoctorId'
        ));
    }

    public function posStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id'         => 'required|exists:patients,id',
            'doctor_id'          => 'nullable|exists:doctors,id',
            'items'              => 'required|array|min:1',
            'items.*.type'       => 'required|in:service,inventory,treatment',
            'items.*.id'         => 'required|integer',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.doctor_id'  => 'required_if:items.*.type,treatment|nullable|exists:doctors,id',
            'tax_rate'           => 'nullable|numeric|min:0|max:100',
            'discount_amount'    => 'nullable|numeric|min:0',
            'payment_method'     => 'nullable|in:cash,card,bank_transfer,cheque,other',
            'payment_reference'  => 'nullable|string|max:255',
            'amount_paid_now'    => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $invoiceItems = [];

            foreach ($validated['items'] as $cartItem) {
                $type = $cartItem['type'];
                $id   = $cartItem['id'];
                $qty  = $cartItem['quantity'];

                if ($type === 'service') {
                    $item = Service::findOrFail($id);
                    $price = $item->price;
                    $name  = $item->name;
                } elseif ($type === 'treatment') {
                    $item = Treatment::findOrFail($id);
                    $price = $item->doctors()->find($cartItem['doctor_id'])?->pivot->price
                        ?? $item->price ?? 0;
                    $name  = $item->name;
                } else {
                    $item = InventoryItem::findOrFail($id);
                    if ($item->current_stock < $qty) {
                        throw new \Exception("Insufficient stock for {$item->name}");
                    }
                    $price = $item->unit_price;
                    $name  = $item->name . ($item->generic_name ? " ({$item->generic_name})" : '');
                }

                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $invoiceItems[] = [
                    'itemable_type' => $type === 'service' ? Service::class : ($type === 'treatment' ? Treatment::class : InventoryItem::class),
                    'itemable_id'   => $id,
                    'description'   => $name,
                    'quantity'      => $qty,
                    'unit_price'    => $price,
                    'total'         => $lineTotal,
                    'doctor_id'     => $cartItem['doctor_id'] ?? null,
                ];
            }

            $taxRate   = $validated['tax_rate'] ?? 0;
            $taxAmount = $subtotal * ($taxRate / 100);
            $discount  = $validated['discount_amount'] ?? 0;
            $total     = $subtotal + $taxAmount - $discount;

            $amountPaidNow = $validated['amount_paid_now'] ?? 0;

            $invoice = BillingInvoice::create([
                'invoice_number'   => $this->generateInvoiceNumber(),
                'patient_id'       => $validated['patient_id'],
                'invoice_date'     => now(),
                'due_date'         => now()->addDays(7),
                'type'             => 'POS',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'discount_amount'  => $discount,
                'total'            => $total,
                'paid_amount'      => $amountPaidNow,
                'balance_due'      => $total - $amountPaidNow,
                'status'           => $this->determineStatus($amountPaidNow, $total),
                'notes'            => $validated['notes'] ?? null,
            ]);

            foreach ($invoiceItems as $itemData) {
                $invoice->items()->create($itemData);
            }

            if ($amountPaidNow > 0) {
                $paymentMethod = $validated['payment_method'] ?? 'cash';

                $payment = $invoice->payments()->create([
                    'amount'        => $amountPaidNow,
                    'payment_date'  => now(),
                    'method'        => $paymentMethod,
                    'reference'     => $validated['payment_reference'] ?? null,
                    'notes'         => 'POS partial/full payment',
                    'user_id'       => auth()->id(),
                ]);

                $openRegister = auth()->user()
                    ->cashRegisters()
                    ->whereNull('closed_at')
                    ->latest()
                    ->first();

                if ($openRegister) {
                    $payment->update(['cash_register_id' => $openRegister->id]);

                    $transactionType = match (strtolower($paymentMethod)) {
                        'cash'          => 'cash_sale',
                        'card'          => 'card_sale',
                        'bank_transfer' => 'bank_transfer_sale',
                        'cheque'        => 'cheque_sale',
                        'other'         => 'other_sale',
                        default         => 'cash_sale',
                    };

                    $openRegister->transactions()->create([
                        'user_id'            => auth()->id(),
                        'billing_invoice_id' => $invoice->id,
                        'payment_id'         => $payment->id,
                        'type'               => $transactionType,
                        'payment_method'     => $paymentMethod,
                        'amount'             => $amountPaidNow,
                        'happened_at'        => now(),
                        'notes'              => 'POS sale - Invoice #' . $invoice->invoice_number,
                    ]);

                    $openRegister->expected_closing_balance = $openRegister->calculateExpectedClosingBalance();
                    $openRegister->save();
                }
            }

            foreach ($validated['items'] as $cartItem) {
                if ($cartItem['type'] === 'inventory') {
                    InventoryItem::where('id', $cartItem['id'])
                        ->decrement('current_stock', $cartItem['quantity']);
                }
            }

            return response()->json([
                'success'       => true,
                'invoice_id'    => $invoice->id,
                'invoice_number'=> $invoice->invoice_number,
                'total'         => $total,
                'paid_amount'   => $amountPaidNow,
                'balance_due'   => $invoice->balance_due,
                'status'        => $invoice->status,
            ]);
        });
    }

    public function getDoctorTreatments($doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);

        $treatments = $doctor->treatments()
            ->wherePivot('price', '>', 0)
            ->get(['treatments.id', 'treatments.name', 'treatments.code'])
            ->map(function ($t) use ($doctor) {
                $pivotPrice = $t->pivot->price ?? 0;

                return [
                    'id'      => $t->id,
                    'name'    => $t->name,
                    'code'    => $t->code,
                    'price'   => (float) $pivotPrice,
                    'display' => $pivotPrice > 0 ? number_format($pivotPrice, 2) : '—'
                ];
            });

        return response()->json([
            'success'    => true,
            'treatments' => $treatments,
            'doctor_name'=> $doctor->full_name
        ]);
    }

    private function determineStatus($paid, $total): string
    {
        if ($paid >= $total) {
            return 'paid';
        }
        if ($paid > 0) {
            return 'partially_paid';
        }
        return 'pending';
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

    public function printHtml(BillingInvoice $invoice)
    {
        $invoice->load(['patient', 'items.itemable', 'payments.user']);
        return view('invoices.print-html', compact('invoice'));
    }
}