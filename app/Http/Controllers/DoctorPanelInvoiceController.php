<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Models\Appointment;
use App\Models\BillingInvoiceItem;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorPanelInvoiceController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return redirect()->route('home')
                ->with('error', __('file.no_doctor_profile_found') ?? 'No doctor profile found.');
        }

        // Summary stats
        $stats = DB::table('billing_invoices')
            ->join('appointments', 'billing_invoices.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->whereNull('billing_invoices.deleted_at')
            ->select([
                DB::raw('SUM(billing_invoices.total) as total_invoiced'),
                DB::raw('SUM(billing_invoices.paid_amount) as total_paid'),
                DB::raw('SUM(billing_invoices.balance_due) as balance_due'),
            ])
            ->first();

        // Treatment charges specific summary
        // Use the actual class name string for polymorphic relation
        $treatmentType = Treatment::class;
        
        $treatmentCharges = DB::table('billing_invoice_items')
            ->join('billing_invoices', 'billing_invoice_items.invoice_id', '=', 'billing_invoices.id')
            ->join('appointments', 'billing_invoices.appointment_id', '=', 'appointments.id')
            ->where('appointments.doctor_id', $doctor->id)
            ->where('billing_invoice_items.itemable_type', $treatmentType)
            ->whereNull('billing_invoices.deleted_at')
            ->sum('billing_invoice_items.total');

        return view('doctor-panel.invoices.index', compact('stats', 'treatmentCharges'));
    }

    public function datatable(Request $request)
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return response()->json(['error' => 'No doctor profile found'], 403);
        }

        $query = BillingInvoice::query()
            ->whereHas('appointment', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })
            ->with(['patient', 'appointment']);

        // DataTables logic
        $totalRecords = (clone $query)->count();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($sq) use ($search) {
                      $sq->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $filteredRecords = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'desc');

        $columns = ['id', 'invoice_number', 'patient_id', 'invoice_date', 'total', 'balance_due', 'status'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'invoice_date';
        
        $query->orderBy($orderColumn, $orderDir);

        $invoices = $query->offset($start)->limit($length)->get();

        $treatmentType = Treatment::class;

        $data = $invoices->map(function ($invoice) use ($treatmentType) {
            // Calculate treatment charges for this specific invoice
            $treatmentCharges = $invoice->items()
                ->where('itemable_type', $treatmentType)
                ->sum('total');

            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'patient_name' => $invoice->patient?->full_name ?? '-',
                'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '-',
                'total' => number_format($invoice->total, 2),
                'balance_due' => number_format($invoice->balance_due, 2),
                'treatment_charges' => number_format($treatmentCharges, 2),
                'status_html' => $this->getStatusHtml($invoice),
                'show_url' => route('invoices.show', $invoice),
                'print_url' => route('invoices.print', $invoice),
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    private function getStatusHtml($invoice)
    {
        $color = $invoice->status_color;
        $label = $invoice->status_label;
        
        // Tailwind color mapping for rounded labels
        $bgClasses = [
            'green' => 'bg-green-100 text-green-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'red' => 'bg-red-100 text-red-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'gray' => 'bg-gray-100 text-gray-800',
        ];

        $classes = $bgClasses[$color] ?? $bgClasses['gray'];
        
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $classes . '">' . $label . '</span>';
    }
}
