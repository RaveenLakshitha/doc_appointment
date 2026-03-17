<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Attendance;
use App\Models\LeaveType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // ── Notifications (all roles) ─────────────────────────────────────────
        $notifications = $user->notifications()->latest()->paginate(10);
        $unreadCount = $user->unreadNotifications()->count();

        // ── Attendance & Leave (any user who has an employee record) ──────────
        $todayAttendance = null;
        $leaveTypes = collect();
        $leaveBalances = collect();
        $hasEmployee = (bool) $user->employee;

        if ($hasEmployee) {
            $todayAttendance = Attendance::where('employee_id', $user->employee->id)
                ->whereDate('date', $today)
                ->first();

            $leaveTypes = LeaveType::where('active', true)->orderBy('name')->get();
        }

        // ── Base data shared across all dashboard views ───────────────────────
        $data = [
            'user' => $user,
            'userName' => $user->name,
            'currentDate' => now()->format('l, d F Y'),
            'role' => $user->getRoleNames()->first() ?? 'user',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'hasEmployee' => $hasEmployee,
            'todayAttendance' => $todayAttendance,
            'leaveTypes' => $leaveTypes,
        ];

        // ── Doctor-specific data ──────────────────────────────────────────────
        if ($user->hasRole('doctor') && $user->doctor) {
            $todayAppointments = Appointment::where('doctor_id', $user->doctor->id)
                ->whereDate('scheduled_start', $today)
                ->orderBy('scheduled_start')
                ->with('patient:id,first_name,last_name')
                ->get();

            $data['todayAppointments'] = $todayAppointments;

            return view('dashboard.doctor', $data);
        }

        // ── All other roles use the unified default dashboard ─────────────────
        return view('dashboard.default', $data);
    }
}