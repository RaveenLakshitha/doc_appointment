<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('attendance.index')) {
            return redirect()->route('home')
                ->with('error', 'Sorry! You are not allowed to access this module.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('attendances.index', compact('employees'));
    }

    public function datatable(Request $request)
    {
        // Optional: protect datatable too
        // if (!Auth::user()->can('attendance.index')) {
        //     abort(403);
        // }

        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $employeeFilter = $request->employee_id;
        $dateFrom       = $request->date_from;
        $dateTo         = $request->date_to;
        $statusFilter   = $request->status;

        $query = Attendance::with(['employee', 'leaveRequest.leaveType'])
            ->select('attendances.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('employee', function ($sq) use ($searchValue) {
                    $sq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchValue}%"])
                       ->orWhere('employee_code', 'like', "%{$searchValue}%");
                })
                  ->orWhere('notes', 'like', "%{$searchValue}%");
            })
            ->when($employeeFilter, fn($q) => $q->where('employee_id', $employeeFilter))
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter));

        $totalRecords    = Attendance::count();
        $filteredRecords = (clone $query)->count();

        // Sorting logic
        $sortColumn = match ((int)$orderIdx) {
            1 => 'date',
            2 => function ($q) { $q->orderBy('employee.first_name'); }, // will need join for name
            3 => 'clock_in',
            4 => 'clock_out',
            5 => 'status',
            default => 'date',
        };

        if (is_string($sortColumn)) {
            $query->orderBy($sortColumn, $orderDir);
        } else {
            // For complex sorting like employee name
            $query->join('employees', 'attendances.employee_id', '=', 'employees.id')
                  ->orderBy('employees.first_name', $orderDir);
        }

        $attendances = $query->offset($start)->limit($length)->get();

        $data = $attendances->map(function ($attendance) {
            $statusClasses = [
                'present'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'absent'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                'leave'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'late'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'half_day' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            ];

            $statusHtml = '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ' .
                ($statusClasses[$attendance->status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') .
                '">' . ucfirst($attendance->status) . '</span>';

            $edit_url   = Auth::user()->can('attendance.update') ? route('attendances.edit', $attendance) : null;
            $delete_url = Auth::user()->can('attendance.delete') ? route('attendances.destroy', $attendance) : null;

            return [
                'id'             => $attendance->id,
                'date'           => $attendance->date->format('d M Y'),
                'employee_name'  => $attendance->employee ? $attendance->employee->full_name : '-',
                'clock_in'       => $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-',
                'clock_out'      => $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-',
                'status_html'    => $statusHtml,
                'notes'          => $attendance->notes ?? '-',
                'show_url'       => route('attendances.show', $attendance),
                'edit_url'       => $edit_url,
                'delete_url'     => $delete_url,
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('attendance.create')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to mark attendance.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('attendance.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date'        => ['required', 'date', 'date_format:Y-m-d'],
            'clock_in'    => ['nullable', 'date_format:H:i'],
            'clock_out'   => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in'],
            'status'      => ['required', Rule::in(['present', 'absent', 'late', 'leave', 'half_day'])],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        // Prevent duplicate attendance for same employee + date
        $exists = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Attendance already exists for this employee on selected date.'])
                ->withInput();
        }

        Attendance::create([
            ...$validated,
            'marked_by' => Auth::id(),
            'marked_at' => now(),
        ]);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance marked successfully.');
    }

    public function show(Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.show')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to view this record.');
        }

        $attendance->load(['employee', 'leaveRequest.leaveType']);

        return view('attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.update')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to edit attendance.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.update')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date'        => ['required', 'date', 'date_format:Y-m-d'],
            'clock_in'    => ['nullable', 'date_format:H:i'],
            'clock_out'   => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in'],
            'status'      => ['required', Rule::in(['present', 'absent', 'late', 'leave', 'half_day'])],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        // Check for duplicate (excluding current record)
        $duplicate = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $attendance->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['date' => 'Another attendance record already exists for this date.'])
                ->withInput();
        }

        $attendance->update($validated);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.delete')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to delete attendance records.');
        }

        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('attendance.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete attendance records.'
            ], 403);
        }

        $ids = $request->input('ids', '');
        $ids = is_string($ids) ? array_filter(explode(',', $ids)) : [];

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No records selected'
            ]);
        }

        Attendance::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected attendance records deleted successfully.'
        ]);
    }

    /**
 * Show bulk marking form (select date + multiple employees)
 */
public function bulkMarkForm()
{
    if (!Auth::user()->can('attendance.create')) {
        return redirect()->route('attendances.index')
            ->with('error', 'You are not allowed to mark attendance.');
    }

    $employees = Employee::where('status', true) // only active
        ->orderBy('first_name')
        ->get(['id', 'first_name', 'last_name', 'employee_code']);

    $today = Carbon::today()->format('Y-m-d');

    return view('attendances.bulk-mark', compact('employees', 'today'));
}

/**
 * Process bulk attendance marking
 */
public function bulkMarkStore(Request $request)
{
    if (!Auth::user()->can('attendance.create')) {
        abort(403);
    }

    $validated = $request->validate([
        'date'          => 'required|date|date_format:Y-m-d',
        'employee_ids'  => 'required|array|min:1',
        'employee_ids.*'=> 'exists:employees,id',
        'status'        => ['required', Rule::in(['present', 'absent', 'late', 'leave', 'half_day'])],
        'clock_in'      => ['nullable', 'date_format:H:i'],
        'clock_out'     => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in'],
        'notes'         => ['nullable', 'string', 'max:500'],
    ]);

    $date = $validated['date'];
    $status = $validated['status'];
    $markedBy = Auth::id();

    $successCount = 0;
    $skipped = [];

    foreach ($validated['employee_ids'] as $employeeId) {
        // Skip if already exists for this employee + date
        $exists = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            $skipped[] = $employeeId;
            continue;
        }

        Attendance::create([
            'employee_id' => $employeeId,
            'date'        => $date,
            'status'      => $status,
            'clock_in'    => $validated['clock_in'] ?? null,
            'clock_out'   => $validated['clock_out'] ?? null,
            'notes'       => $validated['notes'] ?? null,
            'marked_by'   => $markedBy,
            'marked_at'   => now(),
        ]);

        $successCount++;
    }

    $message = "Successfully marked attendance for {$successCount} employees.";
    if (!empty($skipped)) {
        $message .= " " . count($skipped) . " employees were skipped (already have record).";
    }

    return redirect()->route('attendances.index')
        ->with('success', $message);
}

/**
 * Employee self check-in
 */
public function selfCheckIn(Request $request)
{
    $employee = $request->user()->employee;

    if (!$employee) {
        return back()->with('error', 'No employee profile linked to your account.');
    }

    $today = Carbon::today()->format('Y-m-d');

    $attendance = Attendance::firstOrCreate(
        ['employee_id' => $employee->id, 'date' => $today],
        [
            'status'    => 'present',
            'marked_by' => $request->user()->id,
            'marked_at' => now(),
        ]
    );

    if ($attendance->clock_in) {
        return back()->with('warning', 'You have already checked in today.');
    }

    $attendance->update([
        'clock_in'  => now()->format('H:i:s'),
        'marked_at' => now(),
    ]);

    return back()->with('success', 'Checked in successfully at ' . now()->format('H:i'));
}

/**
 * Employee self check-out
 */
public function selfCheckOut(Request $request)
{
    $employee = $request->user()->employee;

    if (!$employee) {
        return back()->with('error', 'No employee profile linked to your account.');
    }

    $today = Carbon::today()->format('Y-m-d');

    $attendance = Attendance::where('employee_id', $employee->id)
        ->whereDate('date', $today)
        ->first();

    if (!$attendance) {
        return back()->with('error', 'No attendance record found for today. Please check-in first.');
    }

    if ($attendance->clock_out) {
        return back()->with('warning', 'You have already checked out today.');
    }

    $attendance->update([
        'clock_out' => now()->format('H:i:s'),
        'marked_at' => now(),
    ]);

    return back()->with('success', 'Checked out successfully at ' . now()->format('H:i'));
}
}