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
    public function __construct()
    {
        $this->middleware('permission:attendance.index', ['only' => ['index', 'show', 'datatable', 'filters']]);
        $this->middleware('permission:attendance.create', ['only' => ['create', 'store', 'bulkMarkForm', 'bulkMarkStore', 'selfCheckIn', 'selfCheckOut']]);
        $this->middleware('permission:attendance.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:attendance.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
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
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $employeeFilter = $request->employee;
        $departmentFilter = $request->department;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $statusFilter = $request->status;

        $query = Attendance::query()
            ->with([
                'employee.department',           // ← important: load department through employee
                'leaveRequest.leaveType'         // keep if you're using it
            ])
            ->select('attendances.*', 'attendances.id as id')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereHas('employee', function ($sq) use ($searchValue) {
                    $sq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchValue}%"])
                        ->orWhere('employee_code', 'like', "%{$searchValue}%");
                })
                    ->orWhere('notes', 'like', "%{$searchValue}%");
            })
            ->when($employeeFilter, fn($q) => $q->where('employee_id', $employeeFilter))
            ->when($departmentFilter, function ($q) use ($departmentFilter) {
                $q->whereHas('employee', fn($sq) => $sq->where('department_id', $departmentFilter));
            })
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter));

        $totalRecords = Attendance::count();
        $filteredRecords = (clone $query)->count();

        // Improved sorting logic (including department name)
        $sortColumn = match ((int) $orderIdx) {
            0 => 'date',                    // date
            1 => function ($q) use ($orderDir) {            // employee name
                    $q->join('employees', 'attendances.employee_id', '=', 'employees.id')
                    ->orderBy('employees.first_name', $orderDir)
                    ->orderBy('employees.last_name', $orderDir);
                },
            2 => function ($q) use ($orderDir) {            // department name ← new/updated
                    $q->join('employees', 'attendances.employee_id', '=', 'employees.id')
                    ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', $orderDir);
                },
            3 => 'clock_in',
            4 => 'clock_out',
            5 => 'status',
            default => 'date',
        };

        if (is_string($sortColumn)) {
            $query->orderBy($sortColumn, $orderDir);
        } else {
            $sortColumn($query);  // execute the closure for complex sorts
        }

        $attendances = $query->offset($start)->limit($length)->get();

        $data = $attendances->map(function ($attendance) {
            $statusClasses = [
                'present' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'absent' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                'leave' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'late' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'half_day' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            ];

            $statusHtml = '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full ' .
                ($statusClasses[$attendance->status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') .
                '">' . ucfirst($attendance->status) . '</span>';

            $edit_url = Auth::user()->can('attendance.edit') ? route('attendances.edit', $attendance->id) : null;
            $delete_url = Auth::user()->can('attendance.delete') ? route('attendances.destroy', $attendance->id) : null;

            return [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'date' => $attendance->date?->format('d M Y') ?? '-',
                'employee_name' => $attendance->employee ? $attendance->employee->full_name : '-',
                'department_name' => $attendance->employee?->department?->name ?? '-',   // ← this is what you want
                'clock_in' => $attendance->clock_in?->format('H:i') ?? '-',
                'clock_out' => $attendance->clock_out?->format('H:i') ?? '-',
                'status' => $attendance->status,
                'status_html' => $statusHtml,
                'notes' => $attendance->notes ?? '-',
                'raw_date' => $attendance->date?->format('Y-m-d') ?? '',
                'show_url' => route('attendances.show', $attendance),
                'edit_url' => $edit_url,
                'delete_url' => $delete_url,
            ];
        })->toArray();

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        if ($column === 'employee') {
            return Employee::orderBy('first_name')
                ->get(['id', 'first_name', 'last_name'])
                ->mapWithKeys(fn($emp) => [$emp->id => trim($emp->first_name . ' ' . $emp->last_name)])
                ->toArray();
        }

        if ($column === 'department') {
            return \App\Models\Department::orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }


        return [];
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
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in'],
            'status' => ['required', Rule::in(['present', 'absent', 'late', 'leave', 'half_day'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (\App\Models\Holiday::isHoliday($validated['date'])) {
            return back()->withErrors(['date' => __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.'])->withInput();
        }

        $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('status', 'approved')
            ->where('start_date', '<=', $validated['date'])
            ->where('end_date', '>=', $validated['date'])
            ->exists();

        if ($isOnLeave) {
            return back()->withErrors(['date' => __('file.cannot_mark_attendance_on_leave') ?? 'Cannot mark attendance on an approved leave day.'])->withInput();
        }

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
        if (!Auth::user()->can('attendance.index')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to view this record.');
        }

        $attendance->load(['employee', 'leaveRequest.leaveType']);

        return view('attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.edit')) {
            return redirect()->route('attendances.index')
                ->with('error', 'Sorry! You are not allowed to edit attendance.');
        }

        $employees = Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'employee_code']);

        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.edit')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in'],
            'status' => ['required', Rule::in(['present', 'absent', 'late', 'leave', 'half_day'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (\App\Models\Holiday::isHoliday($validated['date'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.'], 422);
            }
            return back()->withErrors(['date' => __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.'])->withInput();
        }

        $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('status', 'approved')
            ->where('start_date', '<=', $validated['date'])
            ->where('end_date', '>=', $validated['date'])
            ->exists();

        if ($isOnLeave) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_mark_attendance_on_leave') ?? 'Cannot mark attendance on an approved leave day.'], 422);
            }
            return back()->withErrors(['date' => __('file.cannot_mark_attendance_on_leave') ?? 'Cannot mark attendance on an approved leave day.'])->withInput();
        }

        // Check for duplicate (excluding current record)
        $duplicate = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $attendance->id)
            ->exists();

        if ($duplicate) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Another attendance record already exists for this date.'], 422);
            }
            return back()->withErrors(['date' => 'Another attendance record already exists for this date.'])
                ->withInput();
        }

        $attendance->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
                'data' => $attendance
            ]);
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        if (!Auth::user()->can('attendance.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return redirect()->route('attendances.index')
                ->with('error', __('file.permission_denied'));
        }

        $attendance->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.record_deleted')]);
        }

        return redirect()->route('attendances.index')
            ->with('success', __('file.record_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('attendance.delete')) {
            return response()->json([
                'success' => false,
                'message' => __('file.permission_denied')
            ], 403);
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_matching_records')
            ], 400);
        }

        $count = Attendance::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('file.bulk_deleted', ['count' => $count])
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
            'date' => 'required|date|date_format:Y-m-d',
            'default_clock_in' => 'nullable|date_format:H:i',
            'default_clock_out' => 'nullable|date_format:H:i|after_or_equal:default_clock_in',
            'status' => 'required|array',
            'status.*' => ['required', Rule::in(['present', 'absent', 'late', 'half_day', 'leave'])],
            'clock_in' => 'nullable|array',
            'clock_in.*' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|array',
            'clock_out.*' => 'nullable|date_format:H:i',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
        ]);

        if (\App\Models\Holiday::isHoliday($validated['date'])) {
            return back()->withErrors(['date' => __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.'])->withInput();
        }

        $date = $validated['date'];
        $defaultIn = $validated['default_clock_in'] ?? null;
        $defaultOut = $validated['default_clock_out'] ?? null;
        $markedBy = Auth::id();

        $successCount = 0;
        $skipped = [];

        foreach ($validated['status'] as $employeeId => $status) {
            if (!Employee::where('id', $employeeId)->exists()) {
                continue;
            }

            $exists = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $date)
                ->exists();

            if ($exists) {
                $skipped[] = $employeeId;
                continue;
            }

            $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->exists();

            if ($isOnLeave) {
                $skipped[] = $employeeId; // skip if on leave
                continue;
            }

            $clockIn = null;
            $clockOut = null;

            // Only save clock times for statuses that make sense
            if (in_array($status, ['present', 'late'])) {
                $clockIn = $validated['clock_in'][$employeeId] ?? $defaultIn;
                $clockOut = $validated['clock_out'][$employeeId] ?? $defaultOut;
            }

            Attendance::create([
                'employee_id' => $employeeId,
                'date' => $date,
                'status' => $status,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'notes' => $validated['notes'][$employeeId] ?? null,
                'marked_by' => $markedBy,
                'marked_at' => now(),
            ]);

            $successCount++;
        }

        $message = "Successfully marked {$successCount} employees.";
        if (!empty($skipped)) {
            $message .= " " . count($skipped) . " skipped (already exists).";
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

        if (\App\Models\Holiday::isHoliday($today)) {
            return back()->with('error', __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.');
        }

        $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if ($isOnLeave) {
            return back()->with('error', __('file.cannot_mark_attendance_on_leave') ?? 'Cannot mark attendance on an approved leave day.');
        }

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'status' => 'present',
                'marked_by' => $request->user()->id,
                'marked_at' => now(),
            ]
        );

        if ($attendance->clock_in) {
            return back()->with('warning', 'You have already checked in today.');
        }

        $attendance->update([
            'clock_in' => now()->format('H:i:s'),
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

        if (\App\Models\Holiday::isHoliday($today)) {
            return back()->with('error', __('file.cannot_mark_attendance_on_holiday') ?? 'Cannot mark attendance on a holiday.');
        }

        $isOnLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if ($isOnLeave) {
            return back()->with('error', __('file.cannot_mark_attendance_on_leave') ?? 'Cannot mark attendance on an approved leave day.');
        }

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