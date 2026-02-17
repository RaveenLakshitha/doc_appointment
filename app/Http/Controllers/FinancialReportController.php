<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index()
    {
        return view('reports.financial');
    }

    public function summary(Request $request)
    {
        [$start, $end] = $this->parseDateRange($request);

        // Main invoice query in date range
        $invoices = BillingInvoice::query()
            ->whereBetween('invoice_date', [$start, $end]);

        // Total Sales (gross revenue = sum of total)
        $totalSales = (clone $invoices)->sum('total');

        // Total Paid
        $totalPaid = (clone $invoices)->sum('paid_amount');

        // Outstanding / Balance Due
        $outstanding = (clone $invoices)->sum('balance_due');

        // Overdue count (past due date and not fully paid)
        $overdueCount = (clone $invoices)
            ->where('due_date', '<', Carbon::today())
            ->where('balance_due', '>', 0)
            ->count();

        // Revenue by day (for line chart)
        $revenueByDay = (clone $invoices)
            ->select(DB::raw('DATE(invoice_date) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date')
            ->toArray();

        // Payment methods distribution (for doughnut chart)
        $paymentMethods = Payment::query()
            ->whereBetween('payment_date', [$start, $end])
            ->select('method', DB::raw('SUM(amount) as total'))
            ->groupBy('method')
            ->pluck('total', 'method')
            ->toArray();

        // Recent Invoices (last 10)
        $recentInvoices = (clone $invoices)
            ->with('patient')
            ->latest('invoice_date')
            ->take(10)
            ->get()
            ->map(function ($inv) {
                return [
                    'invoice_number' => $inv->invoice_number,
                    'patient'        => $inv->patient?->full_name ?? '—',
                    'date'           => $inv->invoice_date?->format('M d, Y'),
                    'total'          => number_format($inv->total, 2),
                    'paid'           => number_format($inv->paid_amount, 2),
                    'balance'        => number_format($inv->balance_due, 2),
                    'status'         => $inv->status,
                    'status_class'   => $this->getInvoiceStatusClass($inv->status),
                ];
            });

        // Recent Payments (last 10)
        $recentPayments = Payment::with(['invoice.patient', 'user'])
            ->whereBetween('payment_date', [$start, $end])
            ->latest('payment_date')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'invoice_number' => $payment->invoice?->invoice_number ?? '—',
                    'patient'        => $payment->invoice?->patient?->full_name ?? '—',
                    'amount'         => number_format($payment->amount, 2),
                    'date'           => $payment->payment_date?->format('M d, Y'),
                    'method'         => $payment->method ?? '—',
                    'user'           => $payment->user?->name ?? '—',
                ];
            });

        // Overdue Invoices (top 10)
        $overdueInvoices = BillingInvoice::with('patient')
            ->where('due_date', '<', Carbon::today())
            ->where('balance_due', '>', 0)
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get()
            ->map(function ($inv) {
                $daysOverdue = Carbon::today()->diffInDays($inv->due_date);
                return [
                    'invoice_number' => $inv->invoice_number,
                    'patient'        => $inv->patient?->full_name ?? '—',
                    'due_date'       => $inv->due_date?->format('M d, Y'),
                    'days_overdue'   => $daysOverdue,
                    'balance'        => number_format($inv->balance_due, 2),
                ];
            });

        return response()->json([
            'summary' => [
                'total_sales'   => number_format($totalSales, 2),
                'total_paid'    => number_format($totalPaid, 2),
                'outstanding'   => number_format($outstanding, 2),
                'overdue_count' => $overdueCount,
            ],
            'revenue_by_day'   => $revenueByDay,
            'payment_methods'  => $paymentMethods,
            'recent_invoices'  => $recentInvoices,
            'recent_payments'  => $recentPayments,
            'overdue_invoices' => $overdueInvoices,
        ]);
    }

    private function parseDateRange(Request $request): array
    {
        $range = $request->string('date_range', '');

        if (str_contains($range, ' to ')) {
            [$startStr, $endStr] = explode(' to ', $range);
            $start = Carbon::parse($startStr)->startOfDay();
            $end   = Carbon::parse($endStr)->endOfDay();
        } else {
            $start = Carbon::today()->subMonths(3)->startOfDay();
            $end   = Carbon::now()->endOfDay();
        }

        return [$start, $end];
    }

    private function getInvoiceStatusClass(string $status): string
    {
        return match ($status) {
            'paid'       => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'partial'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'overdue'    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default      => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}