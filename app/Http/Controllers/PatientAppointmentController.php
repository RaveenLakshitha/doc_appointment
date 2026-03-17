<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientAppointmentController extends Controller
{
    public function data(Patient $patient)
    {
        $query = $patient->appointments()->with('doctor')->latest('scheduled_start');

        return datatables()->of($query)
            ->editColumn('scheduled_start', fn($a) => $a->scheduled_start?->format('M d, Y') ?? '—')
            ->addColumn('time', fn($a) => $a->scheduled_start?->format('h:i A') ?? '—')
            ->addColumn('doctor_name', fn($a) => $a->doctor?->full_name ?? '-')
            ->addColumn('status_html', function ($a) {
                $badge = match ($a->status) {
                    'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                    'approved' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                    'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                    'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                    'rejected' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                    'running' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300',
                    default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                };
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium ' . $badge . '">' . ucfirst($a->status) . '</span>';
            })
            ->rawColumns(['status_html'])
            ->make(true);
    }
}
