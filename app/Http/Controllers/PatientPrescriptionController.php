<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Str;

class PatientPrescriptionController extends Controller
{
    public function data(Patient $patient)
    {
        $query = $patient->prescriptions()->with('doctor')->withCount('medications')->latest('prescription_date');

        return datatables()->of($query)
            ->editColumn('prescription_date', fn($p) => $p->prescription_date?->format('M d, Y') ?? '—')
            ->addColumn('doctor_name', fn($p) => $p->doctor?->full_name ?? '-')
            ->editColumn('diagnosis', fn($p) => $p->diagnosis ? Str::limit($p->diagnosis, 50) : '-')
            ->addColumn('actions', function ($p) {
                return '<div class="flex items-center justify-end gap-1">'
                    . '<a href="' . route('prescriptions.print', $p) . '" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400" title="' . __('file.print') . '">'
                    . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>'
                    . '<a href="' . route('prescriptions.show', $p) . '" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400" title="' . __('file.view') . '">'
                    . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>'
                    . '</div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
