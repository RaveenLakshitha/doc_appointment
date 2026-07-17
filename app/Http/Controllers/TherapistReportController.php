<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TherapistReportController extends Controller
{
    public function salesIndex()
    {
        $doctors = Doctor::orderBy('first_name')->get();
        return view('reports.therapist-sales', compact('doctors'));
    }

    public function salesData(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $doctorId = $request->doctor_id;
        $month = $request->month;

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        // Appointments per day
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('scheduled_start', [$start, $end])
            ->select(DB::raw('DATE(scheduled_start) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Payments per day per method
        $payments = Payment::whereHas('invoice', function($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId);
            })
            ->whereBetween('payment_date', [$start, $end])
            ->select(DB::raw('DATE(payment_date) as date'), 'method', DB::raw('SUM(amount) as total'))
            ->groupBy('date', 'method')
            ->get();

        $days = [];
        $totalCash = 0;
        $totalCard = 0;
        $totalTransfer = 0;
        $totalAppointments = 0;
        $totalDaysWorked = 0;

        foreach ($payments as $p) {
            $date = $p->date;
            if (!isset($days[$date])) {
                $days[$date] = ['date' => $date, 'cash' => 0, 'card' => 0, 'transfer' => 0, 'appointments' => 0];
            }
            $method = strtolower($p->method);
            if (str_contains($method, 'cash')) {
                $days[$date]['cash'] += $p->total;
                $totalCash += $p->total;
            } elseif (str_contains($method, 'card')) {
                $days[$date]['card'] += $p->total;
                $totalCard += $p->total;
            } elseif (str_contains($method, 'transfer') || str_contains($method, 'bank')) {
                $days[$date]['transfer'] += $p->total;
                $totalTransfer += $p->total;
            }
        }

        foreach ($appointments as $date => $a) {
            if (!isset($days[$date])) {
                $days[$date] = ['date' => $date, 'cash' => 0, 'card' => 0, 'transfer' => 0, 'appointments' => 0];
            }
            $days[$date]['appointments'] = $a->count;
            $totalAppointments += $a->count;
        }

        $totalDaysWorked = count($days);

        // Sort by date ascending
        ksort($days);
        
        return response()->json([
            'summary' => [
                'total_days_worked' => $totalDaysWorked,
                'total_appointments' => $totalAppointments,
                'total_cash' => $totalCash,
                'total_card' => $totalCard,
                'total_transfer' => $totalTransfer,
            ],
            'daily_data' => array_values($days)
        ]);
    }

    public function payrollsIndex()
    {
        $doctors = Doctor::orderBy('first_name')->get();
        return view('reports.therapist-payrolls', compact('doctors'));
    }

    public function payrollsData(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $doctorId = $request->doctor_id;
        $month = $request->month;

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        // Payrolls per day per method
        $payrolls = Payroll::where('payable_type', Doctor::class)
            ->where('payable_id', $doctorId)
            ->whereBetween('date', [$start, $end])
            ->select(DB::raw('DATE(date) as pay_date'), 'payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('pay_date', 'payment_method')
            ->get();

        $days = [];
        $totalCash = 0;
        $totalCard = 0;
        $totalTransfer = 0;
        $totalOverall = 0;
        $totalDaysPaid = 0;

        foreach ($payrolls as $p) {
            $date = $p->pay_date;
            if (!isset($days[$date])) {
                $days[$date] = ['date' => $date, 'cash' => 0, 'card' => 0, 'transfer' => 0, 'count' => 0];
            }
            
            $method = strtolower($p->payment_method);
            if (str_contains($method, 'cash')) {
                $days[$date]['cash'] += $p->total;
                $totalCash += $p->total;
            } elseif (str_contains($method, 'card')) {
                $days[$date]['card'] += $p->total;
                $totalCard += $p->total;
            } elseif (str_contains($method, 'transfer') || str_contains($method, 'bank')) {
                $days[$date]['transfer'] += $p->total;
                $totalTransfer += $p->total;
            } else {
                // If other method, just lump into transfer or cash? Or we could add 'other'
                // For simplicity, maybe add to transfer
                $days[$date]['transfer'] += $p->total;
                $totalTransfer += $p->total;
            }
            
            $days[$date]['count'] += $p->count;
            $totalOverall += $p->total;
        }

        $totalDaysPaid = count($days);
        ksort($days);

        return response()->json([
            'summary' => [
                'total_days_paid' => $totalDaysPaid,
                'total_overall' => $totalOverall,
                'total_cash' => $totalCash,
                'total_card' => $totalCard,
                'total_transfer' => $totalTransfer,
            ],
            'daily_data' => array_values($days)
        ]);
    }

    public function monthlyIndex()
    {
        $doctors = Doctor::orderBy('first_name')->get();
        return view('reports.monthly', compact('doctors'));
    }

    public function monthlyData(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $doctors = Doctor::all();
        $doctorData = [];
        
        foreach ($doctors as $doctor) {
            $doctorData[$doctor->id] = [
                'name' => $doctor->full_name,
                'sales' => ['cash' => 0, 'card' => 0, 'transfer' => 0, 'appointments' => 0, 'total' => 0, 'days_worked' => 0],
                'payrolls' => ['cash' => 0, 'card' => 0, 'transfer' => 0, 'count' => 0, 'total' => 0, 'days_worked' => 0],
            ];
        }

        // Appointments per doctor per day (for days worked and appointment count)
        $appointments = Appointment::whereBetween('scheduled_start', [$start, $end])
            ->select('doctor_id', DB::raw('DATE(scheduled_start) as date'), DB::raw('count(*) as count'))
            ->groupBy('doctor_id', 'date')
            ->get();

        $salesDays = [];
        foreach ($appointments as $a) {
            if (isset($doctorData[$a->doctor_id])) {
                $doctorData[$a->doctor_id]['sales']['appointments'] += $a->count;
                $salesDays[$a->doctor_id][$a->date] = true;
            }
        }
        foreach ($salesDays as $docId => $days) {
            $doctorData[$docId]['sales']['days_worked'] = count($days);
        }

        // Payments per doctor
        $payments = DB::table('payments')
            ->join('billing_invoices', 'payments.invoice_id', '=', 'billing_invoices.id')
            ->whereBetween('payments.payment_date', [$start, $end])
            ->select('billing_invoices.doctor_id', 'payments.method', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('billing_invoices.doctor_id', 'payments.method')
            ->get();

        foreach ($payments as $p) {
            $docId = $p->doctor_id;
            if (isset($doctorData[$docId])) {
                $method = strtolower($p->method);
                if (str_contains($method, 'cash')) {
                    $doctorData[$docId]['sales']['cash'] += $p->total;
                } elseif (str_contains($method, 'card')) {
                    $doctorData[$docId]['sales']['card'] += $p->total;
                } else {
                    $doctorData[$docId]['sales']['transfer'] += $p->total;
                }
                $doctorData[$docId]['sales']['total'] += $p->total;
            }
        }

        // Payrolls per doctor
        $payrolls = DB::table('payrolls')
            ->where('payable_type', Doctor::class)
            ->whereBetween('date', [$start, $end])
            ->select('payable_id as doctor_id', DB::raw('DATE(date) as pay_date'), 'payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('doctor_id', 'pay_date', 'payment_method')
            ->get();

        $payrollDays = [];
        foreach ($payrolls as $p) {
            $docId = $p->doctor_id;
            if (isset($doctorData[$docId])) {
                $method = strtolower($p->payment_method);
                if (str_contains($method, 'cash')) {
                    $doctorData[$docId]['payrolls']['cash'] += $p->total;
                } elseif (str_contains($method, 'card')) {
                    $doctorData[$docId]['payrolls']['card'] += $p->total;
                } else {
                    $doctorData[$docId]['payrolls']['transfer'] += $p->total;
                }
                $doctorData[$docId]['payrolls']['total'] += $p->total;
                
                $doctorData[$docId]['payrolls']['count'] += $p->count;
                $payrollDays[$docId][$p->pay_date] = true;
            }
        }
        foreach ($payrollDays as $docId => $days) {
            $doctorData[$docId]['payrolls']['days_worked'] = count($days);
        }

        $salesSummary = ['cash' => 0, 'card' => 0, 'transfer' => 0, 'appointments' => 0, 'total' => 0, 'days_worked' => 0];
        $payrollsSummary = ['cash' => 0, 'card' => 0, 'transfer' => 0, 'count' => 0, 'total' => 0, 'days_worked' => 0];

        foreach ($doctorData as $data) {
            $salesSummary['cash'] += $data['sales']['cash'];
            $salesSummary['card'] += $data['sales']['card'];
            $salesSummary['transfer'] += $data['sales']['transfer'];
            $salesSummary['appointments'] += $data['sales']['appointments'];
            $salesSummary['total'] += $data['sales']['total'];
            $salesSummary['days_worked'] += $data['sales']['days_worked'];

            $payrollsSummary['cash'] += $data['payrolls']['cash'];
            $payrollsSummary['card'] += $data['payrolls']['card'];
            $payrollsSummary['transfer'] += $data['payrolls']['transfer'];
            $payrollsSummary['count'] += $data['payrolls']['count'];
            $payrollsSummary['total'] += $data['payrolls']['total'];
            $payrollsSummary['days_worked'] += $data['payrolls']['days_worked'];
        }

        // Filter out doctors with no activity
        $filteredDoctorData = array_filter($doctorData, function($d) {
            return $d['sales']['total'] > 0 || $d['payrolls']['total'] > 0 || $d['sales']['appointments'] > 0 || $d['payrolls']['count'] > 0;
        });

        // Sort by doctor name
        usort($filteredDoctorData, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json([
            'summary' => [
                'total_days_worked' => $salesSummary['days_worked'],
                'total_days_paid' => $payrollsSummary['days_worked'],
                'sales' => $salesSummary,
                'payrolls' => $payrollsSummary,
            ],
            'daily_data' => array_values($filteredDoctorData)
        ]);
    }

    private function emptyDay($date) {
        return [
            'date' => $date,
            'sales' => ['cash' => 0, 'card' => 0, 'transfer' => 0, 'appointments' => 0, 'total' => 0],
            'payrolls' => ['cash' => 0, 'card' => 0, 'transfer' => 0, 'count' => 0, 'total' => 0],
        ];
    }
}
