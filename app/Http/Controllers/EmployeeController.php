<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\OptionList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employees.index', ['only' => ['index', 'show', 'datatablefilters']]);
        $this->middleware('permission:employees.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:employees.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:employees.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'user', 'supervisor'])
            ->when($request->filled('department'), fn($q) => $q->where('department_id', $request->department))
            ->when($request->filled('position'), fn($q) => $q->where('position', $request->position))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $departmentFilter = $request->department;
        $positionFilter = $request->position;
        $statusFilter = $request->status;

        $query = Employee::query()
            ->with(['department', 'user'])
            ->select('employees.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,' '), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$searchValue}%"])
                    ->orWhere('employee_code', 'like', "%{$searchValue}%")
                    ->orWhere('position', 'like', "%{$searchValue}%")
                    ->orWhereHas('department', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                    ->orWhereHas('user', fn($sq) => $sq->where('phone', 'like', "%{$searchValue}%"));
            })
            ->when($departmentFilter, fn($q) => $q->where('department_id', $departmentFilter))
            ->when($positionFilter, fn($q) => $q->where('position', $positionFilter))
            ->when($statusFilter !== null && $statusFilter !== '', fn($q) => $q->where('status', $statusFilter));

        $totalRecords = Employee::count();
        $filteredRecords = (clone $query)->count();

        switch ($orderIdx) {
            case 1:
                $query->orderBy('first_name', $orderDir)
                    ->orderBy('middle_name', $orderDir)
                    ->orderBy('last_name', $orderDir);
                break;
            case 2:
                $query->orderBy('employee_code', $orderDir);
                break;
            case 3:
                $query->join('departments', 'employees.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', $orderDir);
                break;
            case 4:
                $query->orderBy('position', $orderDir);
                break;
            case 5:
                $query->leftJoin('users', 'employees.user_id', '=', 'users.id')
                    ->orderBy('users.phone', $orderDir);
                break;
            case 6:
                $query->orderBy('status', $orderDir === 'desc' ? 'desc' : 'asc');
                break;
            default:
                $query->orderBy('first_name', 'asc')
                    ->orderBy('middle_name', 'asc')
                    ->orderBy('last_name', 'asc');
                break;
        }

        $employees = $query->offset($start)->limit($length)->get();

        $data = $employees->map(function ($e) {
            $statusHtml = $e->status
                ? '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Active</span>'
                : '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Inactive</span>';

            return [
                'id' => $e->id,
                'name' => $e->full_name ?? '-',
                'employee_code' => $e->employee_code ?? '-',
                'department_name' => $e->department?->name ?? '-',
                'position' => $e->position ?? '-',
                'phone' => $e->user?->phone ?? '-',
                'status' => $e->status ? 1 : 0,
                'status_html' => $statusHtml,
                'show_url' => route('employees.show', $e),
                'edit_url' => \Auth::user()->can('employees.edit') ? route('employees.edit', $e) : null,
                'delete_url' => \Auth::user()->can('employees.delete') ? route('employees.destroy', $e) : null,
            ];
        });

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->toArray(),
        ]);
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $users = User::where('is_active', true)->with('employee')->orderBy('name')->get();
        $supervisors = Employee::orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('last_name')
            ->get();
        $genders = OptionList::where('type', 'gender')
            ->where('status', true)
            ->orderBy('order')
            ->get();

        return view('employees.create', compact('departments', 'users', 'supervisors', 'genders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->whereNull('deleted_at')
            ],
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'professional_bio' => 'nullable|string',
            'reporting_to' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'employment_type' => 'nullable|string|max:50',
            'work_schedule' => 'nullable|string|max:100',
            'work_hours_weekly' => 'nullable|integer|min:1|max:168',
            'salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string|min:7|max:15',
            'degree' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'year_completed' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'license_type' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'license_issue_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date|after:license_issue_date',
            'license_issuing_authority' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employees/photos', 'public');
            }

            Employee::create([
                'user_id' => $validated['user_id'] ?? null,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'photo' => $photoPath,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'department_id' => $validated['department_id'],
                'position' => $validated['position'],
                'profession' => $validated['profession'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'professional_bio' => $validated['professional_bio'] ?? null,
                'employee_code' => Employee::generateEmployeeCode(),
                'reporting_to' => $validated['reporting_to'] ?? null,
                'hire_date' => $validated['hire_date'],
                'termination_date' => null,
                'employment_type' => $validated['employment_type'] ?? null,
                'work_schedule' => $validated['work_schedule'] ?? null,
                'work_hours_weekly' => $validated['work_hours_weekly'] ?? null,
                'salary' => $validated['salary'] ?? null,
                'payment_frequency' => $validated['payment_frequency'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'degree' => $validated['degree'] ?? null,
                'institution' => $validated['institution'] ?? null,
                'year_completed' => $validated['year_completed'] ?? null,
                'license_type' => $validated['license_type'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'license_issue_date' => $validated['license_issue_date'] ?? null,
                'license_expiry_date' => $validated['license_expiry_date'] ?? null,
                'license_issuing_authority' => $validated['license_issuing_authority'] ?? null,
                'status' => true,
            ]);
        });

        return redirect()->route('employees.index')
            ->with('success', __('file.record_created'));
    }


    public function show(Employee $employee)
    {
        $employee->load(['department', 'user', 'supervisor']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        $users = User::where('is_active', true)
            ->where(function($q) use ($employee) {
                $q->doesntHave('employee')
                  ->orWhere('id', $employee->user_id);
            })
            ->orderBy('name')
            ->get();
        $supervisors = Employee::where('id', '!=', $employee->id)->orderBy('first_name')->orderBy('middle_name')->orderBy('last_name')->get();
        $genders = OptionList::where('type', 'gender')
            ->where('status', true)
            ->orderBy('order')
            ->get();

        return view('employees.edit', compact('employee', 'departments', 'users', 'supervisors', 'genders'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($employee->id)->whereNull('deleted_at')
            ],
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'professional_bio' => 'nullable|string',
            'reporting_to' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'employment_type' => 'nullable|string|max:50',
            'work_schedule' => 'nullable|string|max:100',
            'work_hours_weekly' => 'nullable|integer|min:1|max:168',
            'salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string|min:7|max:15',
            'degree' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'year_completed' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'license_type' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'license_issue_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date|after:license_issue_date',
            'license_issuing_authority' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $employee, $validated) {
            if ($request->hasFile('photo')) {
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $validated['photo'] = $request->file('photo')->store('employees/photos', 'public');
            }

            $employee->update($validated);
        });


        return redirect()->route('employees.index')
            ->with('success', __('file.record_updated'));
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['user_id' => null]);
        $employee->delete();

        return back()->with('success', __('file.record_deleted'));
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        if ($column === 'department') {
            return Department::orderBy('name')->pluck('name', 'id');
        }

        if ($column === 'position') {
            return Employee::select('position')
                ->whereNotNull('position')
                ->distinct()
                ->orderBy('position')
                ->pluck('position', 'position');
        }

        return response()->json([]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', __('file.no_valid_users_deleted'));
        }

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        Employee::whereIn('id', $ids)->update(['user_id' => null]);
        $count = Employee::whereIn('id', $ids)->delete();

        if ($count === 0) {
            return back()->with('error', __('file.no_valid_users_deleted'));
        }

        return back()->with('success', __('file.bulk_deleted', ['count' => $count]));
    }
}