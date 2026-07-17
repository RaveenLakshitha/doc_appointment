<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        return view('hr.payrolls.index');
    }

    public function datatable(Request $request)
    {
        $query = Payroll::with(['payable']);

        if ($request->recipient_type) {
            $type = $request->recipient_type === 'doctor' ? Doctor::class : Employee::class;
            $query->where('payable_type', $type);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        return datatables()->of($query)
            ->addColumn('recipient_name', function ($row) {
                if ($row->payable) {
                    return $row->payable->full_name;
                }
                return 'Deleted User (#' . $row->payable_id . ')';
            })
            ->editColumn('date', function ($row) {
                return $row->date->format('Y-m-d');
            })
            ->addColumn('actions', function ($row) {
                return [
                    'show_url' => route('payrolls.show', $row->id),
                    'edit_url' => route('payrolls.edit', $row->id),
                    'delete_url' => route('payrolls.destroy', $row->id),
                ];
            })
            ->make(true);
    }

    public function filters(Request $request)
    {
        $column = $request->column;
        if ($column === 'payment_method') {
            return Payroll::distinct()->pluck('payment_method', 'payment_method');
        }
        return response()->json([]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Payroll::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => __('file.record_deleted')]);
    }

    public function create()
    {
        $doctors = Doctor::orderBy('first_name')->get();
        $employees = Employee::orderBy('first_name')->get();

        return view('hr.payrolls.create', compact('doctors', 'employees'));
    }

    public function getAppointments(Request $request)
    {
        $doctorId = $request->doctor_id;
        if (!$doctorId) {
            return response()->json([]);
        }

        $month = $request->month;
        if (!$month) {
            return response()->json([]);
        }

        $payrollId = $request->payroll_id;

        // 1. Unpaid appointments for the selected month (not linked to any payroll)
        $invoices = \App\Models\BillingInvoice::with(['appointment.patient', 'appointment.treatments', 'appointment.services', 'payments'])
            ->where('doctor_id', $doctorId)
            ->where('status', 'paid')
            ->whereYear('invoice_date', substr($month, 0, 4))
            ->whereMonth('invoice_date', substr($month, 5, 2))
            ->whereDoesntHave('appointment.payrolls')
            ->orderBy('invoice_date', 'desc')
            ->get();

        // 2. When editing, always include already-linked appointments (regardless of month)
        if ($payrollId) {
            $linkedInvoices = \App\Models\BillingInvoice::with(['appointment.patient', 'appointment.treatments', 'appointment.services', 'payments'])
                ->where('doctor_id', $doctorId)
                ->where('status', 'paid')
                ->whereHas('appointment.payrolls', function ($sq) use ($payrollId) {
                    $sq->where('payrolls.id', $payrollId);
                })
                ->orderBy('invoice_date', 'desc')
                ->get();

            // Merge and deduplicate by invoice id
            $invoices = $invoices->merge($linkedInvoices)->unique('id')->values();
        }

        $data = $invoices->map(function ($invoice) {
            $appointment = $invoice->appointment;
            if (!$appointment) return null;

            return [
                'id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'patient_name' => $appointment->patient ? $appointment->patient->full_name : '-',
                'date' => $appointment->scheduled_start ? $appointment->scheduled_start->format('Y-m-d H:i') : '-',
                'amount' => $invoice->total,
                'status' => 'Paid',
                'payment_method' => $invoice->payment_method_label,
                'is_invoiced' => (bool)$invoice->is_printed,
                'invoice_id' => $invoice->id,
            ];
        })->filter()->values();

        return response()->json($data);
    }

    public function getInvoiceSummary($id)
    {
        $invoice = \App\Models\BillingInvoice::with('items.itemable')->findOrFail($id);

        $currency = \App\Models\Setting::getCurrencySymbol();

        $html  = '<div class="space-y-4">';
        $html .= '<div class="flex justify-between border-b pb-2"><span class="font-bold">' . __('file.invoice_no') . '</span><span>' . $invoice->invoice_number . '</span></div>';
        $html .= '<div class="flex justify-between border-b pb-2"><span class="font-bold">' . __('file.bill_date') . '</span><span>' . $invoice->invoice_date->format('Y-m-d') . '</span></div>';
        $html .= '<div class="flex justify-between border-b pb-2"><span class="font-bold">' . __('file.total_amount') . '</span><span class="text-indigo-600 font-bold">' . $currency . number_format($invoice->total, 2) . '</span></div>';
        $html .= '<div class="mt-4"><h4 class="font-bold mb-2">' . __('file.items') . '</h4><ul class="space-y-2">';
        foreach ($invoice->items as $item) {
            $html .= '<li class="flex justify-between text-sm"><span>' . $item->description . ' (x' . $item->quantity . ')</span><span>' . $currency . number_format($item->total, 2) . '</span></li>';
        }
        $html .= '</ul></div>';
        $html .= '</div>';

        return response($html);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:doctor,employee',
            'recipient_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'therapist_amount' => 'nullable|numeric|min:0',
            'caped_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'appointments' => 'nullable|array',
            'appointments.*' => 'exists:appointments,id'
        ]);

        DB::transaction(function () use ($validated) {
            $payableType = $validated['recipient_type'] === 'doctor' ? Doctor::class : Employee::class;

            $payroll = Payroll::create([
                'payable_type' => $payableType,
                'payable_id' => $validated['recipient_id'],
                'amount' => $validated['amount'],
                'therapist_amount' => $validated['therapist_amount'] ?? null,
                'caped_amount' => $validated['caped_amount'] ?? null,
                'payment_method' => $validated['payment_method'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
                'status' => 'paid',
                'created_by' => auth()->id(),
            ]);

            if ($validated['recipient_type'] === 'doctor' && !empty($validated['appointments'])) {
                // Attach appointments
                $attachments = [];
                $appointments = Appointment::with(['invoices' => function($q) {
                    $q->where('status', 'paid')->orderBy('id', 'desc');
                }])->whereIn('id', $validated['appointments'])->get();
                
                foreach ($appointments as $appointment) {
                    $invoice = $appointment->invoices->first();
                    $amount = $invoice ? $invoice->total : ($appointment->total_treatment_price + $appointment->total_services_price);
                    $attachments[$appointment->id] = ['amount' => $amount];
                }

                $payroll->appointments()->sync($attachments);
            }
        });

        return redirect()->route('payrolls.index')->with('success', __('file.record_created'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['payable', 'appointments', 'creator']);
        return view('hr.payrolls.show', compact('payroll'));
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', __('file.record_deleted'));
    }

    public function edit(Payroll $payroll)
    {
        $payroll->load(['payable', 'appointments']);
        $doctors = Doctor::orderBy('first_name')->get();
        $employees = Employee::orderBy('first_name')->get();
        
        $selectedAppointments = $payroll->appointments->pluck('id')->toArray();

        return view('hr.payrolls.edit', compact('payroll', 'doctors', 'employees', 'selectedAppointments'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:doctor,employee',
            'recipient_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'therapist_amount' => 'nullable|numeric|min:0',
            'caped_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'appointments' => 'nullable|array',
            'appointments.*' => 'exists:appointments,id'
        ]);

        DB::transaction(function () use ($validated, $payroll) {
            $payableType = $validated['recipient_type'] === 'doctor' ? Doctor::class : Employee::class;

            $payroll->update([
                'payable_type' => $payableType,
                'payable_id' => $validated['recipient_id'],
                'amount' => $validated['amount'],
                'therapist_amount' => $validated['therapist_amount'] ?? null,
                'caped_amount' => $validated['caped_amount'] ?? null,
                'payment_method' => $validated['payment_method'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            if ($validated['recipient_type'] === 'doctor') {
                $attachments = [];
                $appointmentIds = $validated['appointments'] ?? [];
                
                $appointments = \App\Models\Appointment::with(['invoices' => function($q) {
                    $q->where('status', 'paid')->orderBy('id', 'desc');
                }])->whereIn('id', $appointmentIds)->get();
                
                foreach ($appointments as $appointment) {
                    $invoice = $appointment->invoices->first();
                    $amount = $invoice ? $invoice->total : ($appointment->total_treatment_price + $appointment->total_services_price);
                    $attachments[$appointment->id] = ['amount' => $amount];
                }

                $payroll->appointments()->sync($attachments);
            } else {
                $payroll->appointments()->detach();
            }
        });

        return redirect()->route('payrolls.index')->with('success', __('file.record_updated'));
    }

    public function print(Payroll $payroll)
    {
        $payroll->load(['payable', 'appointments', 'creator']);

        $clinic_name = \App\Models\Setting::getSetting('clinic_name', config('app.name', 'Clinic'));
        $clinic_address = \App\Models\Setting::getSetting('clinic_address', '123 Clinic Ave');
        $clinic_phone = \App\Models\Setting::getSetting('clinic_phone', '+1 234 567 8900');
        $clinic_email = \App\Models\Setting::getSetting('clinic_email', 'contact@clinic.com');
        $currency_code = \App\Models\Setting::getCurrencySymbol();

        return view('hr.payrolls.print', compact(
            'payroll',
            'clinic_name',
            'clinic_address',
            'clinic_phone',
            'clinic_email',
            'currency_code'
        ));
    }

    public function sendEmail(Request $request, Payroll $payroll)
    {
        $notifiable = $payroll->payable;

        if (!$notifiable || !$notifiable->email) {
            return response()->json([
                'success' => false,
                'message' => __('file.patient_customer_no_email') ?? 'The recipient does not have an email address.'
            ], 422);
        }

        try {
            $notifiable->notify(new \App\Notifications\PayrollSent($payroll));

            return response()->json([
                'success' => true,
                'message' => __('file.email_sent_successfully') ?? 'Email sent successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Payroll Email Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('file.failed_to_send_email') ?? 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}
