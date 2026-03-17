<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Patient;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\Service;
use App\Models\InventoryItem;
use App\Models\Treatment;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Notifications\AppointmentPaid;
use App\Services\NotificationService;
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
                'paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Paid</span>',
                'partially_paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Partially Paid</span>',
                'overdue' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Overdue</span>',
                default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Sent</span>',
            };

            $actions = '<div class="flex items-center justify-end gap-1">'
                . '<a href="' . route('invoices.show', $i) . '" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
                . '</a>'
                . '<a href="' . route('invoices.print', $i) . '" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>'
                . '</a>'
                . '<button type="button" onclick="confirmDelete(\'' . route('invoices.destroy', $i) . '\')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
                . '</button>'
                . '</div>';

            $setting = cache('settings') ?? \App\Models\Setting::first() ?? new \App\Models\Setting();
            $currency = $setting->currency ?? 'USD';

            return [
                'id' => $i->id,
                'invoice_number' => $i->invoice_number,
                'patient_name' => $i->patient?->getFullNameAttribute() ?? 'N/A',
                'invoice_date' => $i->invoice_date->format('M d, Y'),
                'total' => $currency . number_format($i->total, 2),
                'balance_due' => $currency . number_format($i->balance_due, 2),
                'balance_due_raw' => (float)$i->balance_due,
                'status_html' => $statusBadge,
                'is_printed_html' => $i->is_printed 
                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">' . trans('file.printed') . '</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">' . trans('file.not_printed') . '</span>',
                'show_url' => route('invoices.show', $i),
                'print_url' => route('invoices.print', $i) . '?redirect=invoices',
                'delete_url' => \Auth::user()->can('invoices.delete') ? route('invoices.destroy', $i) : null,
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
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
            ->orderBy('name')
            ->get(['id', 'name', 'generic_name', 'unit_price', 'current_stock', 'minimum_stock_level', 'medicine_image']);

        $preloadedItems = [];
        $preselectedPatientId = null;
        $preselectedDoctorId = null;

        $appointmentId = $request->query('appointment_id');
        $appointment = null;

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
                        'type' => 'treatment',
                        'id' => $treatment->id,
                        'name' => $treatment->name,
                        'price' => $price,
                        'quantity' => $treatment->pivot->quantity ?? 1,
                        'source' => 'appointment',
                        'doctor_id' => $appointment->doctor_id,
                    ];
                }

                $latestPrescription = $appointment->prescriptions
                    ->sortByDesc('prescription_date')
                    ->first();

                if ($latestPrescription) {
                    foreach ($latestPrescription->medications as $med) {
                        $item = $med->inventoryItem;

                        if (!$item) {
                            continue;
                        }

                        // We still only auto-add to cart if there's stock, 
                        // but the summary modal will show everything.
                        if ($item->current_stock > $item->minimum_stock_level) {
                            $preloadedItems[] = [
                                'type' => 'inventory',
                                'id' => $item->id,
                                'name' => $med->name ?? ($item->generic_name ?: $item->name),
                                'price' => $item->unit_price,
                                'quantity' => ($med->per_day ?? 1) * ($med->duration_days ?? 1),
                                'source' => 'prescription',
                            ];
                        }
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
            'preselectedDoctorId',
            'appointmentId',
            'appointment'
        ));
    }

    public function posStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:service,inventory,treatment',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.doctor_id' => 'required_if:items.*.type,treatment|nullable|exists:doctors,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,cheque,other',
            'payment_reference' => 'nullable|string|max:255',
            'amount_paid_now' => 'nullable|numeric|min:0',
            'appointment_id' => 'nullable|exists:appointments,id',
            'notes' => 'nullable|string',
            'is_printed' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $invoiceItems = [];

            foreach ($validated['items'] as $cartItem) {
                $type = $cartItem['type'];
                $id = $cartItem['id'];
                $qty = $cartItem['quantity'];

                if ($type === 'service') {
                    $item = Service::findOrFail($id);
                    $price = $item->price;
                    $name = $item->name;
                } elseif ($type === 'treatment') {
                    $item = Treatment::findOrFail($id);
                    $price = $item->doctors()->find($cartItem['doctor_id'])?->pivot->price
                        ?? $item->price ?? 0;
                    $name = $item->name;
                } else {
                    $item = InventoryItem::findOrFail($id);
                    if ($item->current_stock - $qty < $item->minimum_stock_level) {
                        throw new \Exception("Cannot fulfill request: Minimum stock level reached for {$item->name}");
                    }
                    $price = $item->unit_price;
                    $name = $item->name . ($item->generic_name ? " ({$item->generic_name})" : '');
                }

                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $invoiceItems[] = [
                    'itemable_type' => $type === 'service' ? Service::class : ($type === 'treatment' ? Treatment::class : InventoryItem::class),
                    'itemable_id' => $id,
                    'description' => $name,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $lineTotal,
                    'doctor_id' => $cartItem['doctor_id'] ?? null,
                ];
            }

            $taxRate = $validated['tax_rate'] ?? 0;
            $taxAmount = $subtotal * ($taxRate / 100);
            $discount = $validated['discount_amount'] ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            $amountPaidNow = $validated['amount_paid_now'] ?? 0;

            $invoice = BillingInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'patient_id' => $validated['patient_id'],
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'appointment_id' => $validated['appointment_id'] ?? null,
                'type' => 'POS',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'total' => $total,
                'paid_amount' => $amountPaidNow,
                'balance_due' => $total - $amountPaidNow,
                'status' => $this->determineStatus($amountPaidNow, $total),
                'notes' => $validated['notes'] ?? null,
                'is_printed' => $validated['is_printed'] ?? true,
            ]);

            foreach ($invoiceItems as $itemData) {
                $invoice->items()->create($itemData);
            }

            if ($amountPaidNow > 0) {
                $paymentMethod = $validated['payment_method'] ?? 'cash';

                $payment = $invoice->payments()->create([
                    'amount' => $amountPaidNow,
                    'payment_date' => now(),
                    'method' => $paymentMethod,
                    'reference' => $validated['payment_reference'] ?? null,
                    'notes' => 'POS partial/full payment',
                    'user_id' => auth()->id(),
                ]);

                $openRegister = auth()->user()
                    ->cashRegisters()
                    ->whereNull('closed_at')
                    ->latest()
                    ->first();

                if ($openRegister) {
                    $payment->update(['cash_register_id' => $openRegister->id]);

                    $transactionType = match (strtolower($paymentMethod)) {
                        'cash' => 'cash_sale',
                        'card' => 'card_sale',
                        'bank_transfer' => 'bank_transfer_sale',
                        'cheque' => 'cheque_sale',
                        'other' => 'other_sale',
                        default => 'cash_sale',
                    };

                    $openRegister->transactions()->create([
                        'user_id' => auth()->id(),
                        'billing_invoice_id' => $invoice->id,
                        'payment_id' => $payment->id,
                        'type' => $transactionType,
                        'payment_method' => $paymentMethod,
                        'amount' => $amountPaidNow,
                        'happened_at' => now(),
                        'notes' => 'POS sale - Invoice #' . $invoice->invoice_number,
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

            if ($invoice->isFullyPaid() && $invoice->appointment_id) {
                $appointment = Appointment::find($invoice->appointment_id);
                if ($appointment) {
                    $appointment->update(['status' => Appointment::STATUS_PAID]);
                    
                    // Trigger Payment Notification
                    $payment = $invoice->payments()->latest()->first();
                    if ($payment) {
                        NotificationService::send('appointment_paid', new AppointmentPaid($payment), array_filter([$appointment->doctor?->user, $appointment->patient?->user]));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total' => $total,
                'paid_amount' => $amountPaidNow,
                'balance_due' => $invoice->balance_due,
                'status' => $invoice->status,
                'is_printed' => (bool)$invoice->is_printed,
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
                    'id' => $t->id,
                    'name' => $t->name,
                    'code' => $t->code,
                    'price' => (float) $pivotPrice,
                    'display' => $pivotPrice > 0 ? number_format($pivotPrice, 2) : '—'
                ];
            });

        return response()->json([
            'success' => true,
            'treatments' => $treatments,
            'doctor_name' => $doctor->full_name
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
        $pdf = Pdf::loadView('invoices.print', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function destroy(BillingInvoice $invoice)
    {
        $invoice->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.invoice_deleted_successfully')]);
        }

        return back()->with('success', __('file.invoice_deleted_successfully'));
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

        BillingInvoice::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.invoices_deleted_successfully')]);
        }

        return back()->with('success', __('file.invoices_deleted_successfully'));
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

    public function lastTransaction()
    {
        $lastInvoice = BillingInvoice::with('patient')
            ->where('type', 'POS')
            ->latest()
            ->first();

        if (!$lastInvoice) {
            return response()->json([
                'success' => false,
                'message' => 'No transactions found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'invoice_number' => $lastInvoice->invoice_number,
            'patient_name' => $lastInvoice->patient?->full_name ?? 'N/A',
            'time' => $lastInvoice->created_at->format('h:i A'),
            'payment_method' => $lastInvoice->payments()->latest()->first()?->method ?? 'N/A',
            'total' => number_format($lastInvoice->total, 2),
            'id' => $lastInvoice->id,
        ]);
    }

    public function completedAppointments()
    {
        $appointments = \App\Models\Appointment::with(['patient', 'doctor'])
            ->where('status', \App\Models\Appointment::STATUS_COMPLETED)
            ->whereDoesntHave('invoices')
            ->latest('completed_at')
            ->get();

        $data = $appointments->map(function ($app) {
            return [
                'id' => $app->id,
                'patient_name' => $app->patient?->full_name ?? 'N/A',
                'doctor_name' => $app->doctor?->full_name ?? 'N/A',
                'completed_at' => $app->completed_at ? $app->completed_at->format('M d, Y h:i A') : ($app->updated_at ? $app->updated_at->format('M d, Y h:i A') : 'N/A'),
                'appointment_number' => $app->appointment_number ?? $app->id,
            ];
        });

        return response()->json([
            'success' => true,
            'appointments' => $data
        ]);
    }

    public function printHtml(BillingInvoice $invoice)
    {
        $invoice->load(['patient', 'items.itemable', 'payments.user']);
        $redirect = request('redirect', 'invoices');
        
        $settings = \App\Models\Setting::first();
        $paperSize = $settings->invoice_paper_size ?? 'A4';
        $viewName = $paperSize === '80mm' ? 'invoices.print-html-80mm' : 'invoices.print-html-a4';
        
        return view($viewName, compact('invoice', 'redirect'));
    }
}