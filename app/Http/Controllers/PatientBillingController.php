<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientBillingController extends Controller
{
    public function data(Patient $patient)
    {
        $query = $patient->invoices()->with('payments')->latest();

        return datatables()->of($query)
            ->editColumn('invoice_date', fn($i) => $i->invoice_date?->format('M d, Y') ?? '—')
            ->editColumn('total', fn($i) => '$' . number_format($i->total, 2))
            ->editColumn('paid_amount', fn($i) => '$' . number_format($i->paid_amount, 2))
            ->editColumn('balance_due', fn($i) => '$' . number_format($i->balance_due, 2))
            ->addColumn('status_html', function ($i) {
                $badge = match ($i->status) {
                    'paid' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                    'partially_paid' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                    'overdue' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                    default => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                };
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium ' . $badge . '">' . ucfirst(str_replace('_', ' ', $i->status)) . '</span>';
            })
            ->rawColumns(['status_html'])
            ->make(true);
    }
}
