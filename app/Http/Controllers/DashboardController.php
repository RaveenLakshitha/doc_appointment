<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Attendance;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveEntitlement;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(30)
            ->get();

        $unreadCount = $user->unreadNotifications()->count();

        $data = [
            'user'          => $user,
            'userName'      => $user->name,
            'currentDate'   => now()->format('l, d F Y'),
            'role'          => $user->getRoleNames()->first() ?? 'user',
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ];

        if ($user->hasRole('doctor')) {

            $today = Carbon::today();

            // ── Appointments ───────────────────────────────────────────────
            $todayAppointments = Appointment::where('doctor_id', $user->id)
                ->whereDate('scheduled_start', $today)
                ->orderBy('scheduled_start')
                ->with('patient:id,first_name,last_name')
                ->get();

            $upcomingAppointments = Appointment::where('doctor_id', $user->id)
                ->where('scheduled_start', '>', now())
                ->where('scheduled_start', '<=', now()->addDays(7))
                ->orderBy('scheduled_start')
                ->with('patient:id,first_name,last_name')
                ->limit(12)
                ->get();

            // ── Attendance (today's record for self check-in/out) ─────────
            $todayAttendance = null;
            if ($user->employee) {
                $todayAttendance = Attendance::where('employee_id', $user->employee->id)
                    ->whereDate('date', $today)
                    ->first();
            }

            // ── Leave related data for the request form ───────────────────
            $leaveTypes = LeaveType::where('active', true)
                ->orderBy('name')
                ->get();

            $leaveBalances = collect();
            if ($user->employee) {
                $leaveBalances = EmployeeLeaveEntitlement::with('leaveType')
                    ->where('employee_id', $user->employee->id)
                    ->where('year', now()->year)
                    ->get()
                    ->map(function ($ent) {
                        return [
                            'type'      => $ent->leaveType->name ?? 'Unknown',
                            'entitled'  => $ent->entitled_days,
                            'used'      => $ent->used_days,
                            'remaining' => max(0, $ent->entitled_days - $ent->used_days),
                        ];
                    });
            }

            $data = array_merge($data, [
                'todayAppointments'    => $todayAppointments,
                'upcomingAppointments' => $upcomingAppointments,
                'todayAttendance'      => $todayAttendance,
                'leaveTypes'           => $leaveTypes,
                'leaveBalances'        => $leaveBalances,
            ]);

            return view('dashboard.doctor', $data);
        }

        // Other roles (you can extend similarly later)
        if ($user->hasRole('admin')) {
            return view('dashboard.admin', $data);
        }

        if ($user->hasRole('hr')) {
            return view('dashboard.hr', $data);
        }

        if ($user->hasRole('receptionist')) {
            return view('dashboard.receptionist', $data);
        }

        if ($user->hasRole('nurse')) {
            return view('dashboard.nurse', $data);
        }

        if ($user->hasRole('patient')) {
            return view('dashboard.patient', $data);
        }

        return view('dashboard.default', $data);
    }
}