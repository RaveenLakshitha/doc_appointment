<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Doctor;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollReportController extends Controller
{
    public function index()
    {
        $doctors = Doctor::orderBy('first_name')->get();
        $employees = Employee::orderBy('first_name')->get();
        return view('reports.payroll', compact('doctors', 'employees'));
    }

    public function summary(Request $request)
    {
        [$start, $end] = $this->parseDateRange($request);

        // Main payroll query in date range
        $payrolls = Payroll::query()
            ->with(['payable', 'creator'])
            ->whereBetween('date', [$start, $end]);

        if ($request->filled('recipient_type') && $request->filled('recipient_id')) {
            $type = $request->recipient_type === 'doctor' ? Doctor::class : Employee::class;
            $payrolls->where('payable_type', $type)
                     ->where('payable_id', $request->recipient_id);
        }

        // Total paid amount
        $totalPaid = (clone $payrolls)->sum('amount');
        
        // Total transactions
        $totalTransactions = (clone $payrolls)->count();
        
        // Total therapists paid (distinct doctor IDs)
        $totalTherapistsPaid = (clone $payrolls)->where('payable_type', Doctor::class)
            ->distinct('payable_id')
            ->count('payable_id');

        // Total employees paid
        $totalEmployeesPaid = (clone $payrolls)->where('payable_type', Employee::class)
            ->distinct('payable_id')
            ->count('payable_id');

        // Breakdown by Therapist / Employee
        $breakdown = (clone $payrolls)->get()->groupBy(function($payroll) {
            return $payroll->payable ? $payroll->payable->full_name : 'Deleted User';
        })->map(function($group) {
            $first = $group->first();
            $typeStr = $first->payable_type === Doctor::class ? __('file.doctor') : __('file.employee');
            // If they don't have localization key in their app for doctor, we'll try to fallback or just use string
            return [
                'name' => $first->payable ? $first->payable->full_name : 'Deleted User',
                'type' => $typeStr,
                'total_amount' => number_format($group->sum('amount'), 2),
                'transactions' => $group->count(),
            ];
        })->sortByDesc(function($item) {
            return (float) str_replace(',', '', $item['total_amount']);
        })->values();

        // Payment Methods distribution
        $paymentMethods = (clone $payrolls)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Recent Payments
        $recentPayments = (clone $payrolls)
            ->latest('date')
            ->take(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'recipient'      => $payment->payable ? $payment->payable->full_name : '—',
                    'type'           => $payment->payable_type === Doctor::class ? __('file.doctor') : __('file.employee'),
                    'amount'         => number_format($payment->amount, 2),
                    'date'           => $payment->date ? $payment->date->translatedFormat('M d, Y') : '—',
                    'method'         => $payment->payment_method ? __('file.' . strtolower($payment->payment_method)) : '—',
                    'user'           => $payment->creator ? $payment->creator->name : '—',
                ];
            });

        return response()->json([
            'summary' => [
                'total_paid'           => number_format($totalPaid, 2),
                'total_transactions'   => $totalTransactions,
                'total_therapists'     => $totalTherapistsPaid,
                'total_employees'      => $totalEmployeesPaid,
            ],
            'breakdown'        => $breakdown,
            'payment_methods'  => $paymentMethods,
            'recent_payments'  => $recentPayments,
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
}
