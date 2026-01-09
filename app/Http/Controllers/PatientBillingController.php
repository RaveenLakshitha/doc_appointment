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
        ->editColumn('invoice_date', fn($i) => $i->invoice_date->format('M d, Y'))
        ->editColumn('total', fn($i) => '$' . number_format($i->total, 2))
        ->editColumn('paid_amount', fn($i) => '$' . number_format($i->paid_amount, 2))
        ->editColumn('balance_due', fn($i) => '$' . number_format($i->balance_due, 2))
        ->addColumn('status_html', function ($i) {
            $badge = match ($i->status) {
                'paid'           => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                'partially_paid' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                'overdue'        => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                default          => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
            };
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium ' . $badge . '">' . ucfirst(str_replace('_', ' ', $i->status)) . '</span>';
        })
        ->addColumn('actions', function ($i) {
            return '<div class="flex items-center justify-end gap-1">'
                . '<a href="' . route('invoices.show', $i) . '" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>'
                . '<a href="' . route('invoices.print', $i) . '" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400">'
                . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>'
                . '</div>';
        })
        ->rawColumns(['status_html', 'actions'])
        ->make(true);
}
}
