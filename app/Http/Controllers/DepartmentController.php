<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $departments = Department::query()
            ->with(['headDoctor', 'specializations'])
            ->withCount(['specializations', 'doctors as staff_count' => fn($q) => $q->where('is_active', true)])
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when(in_array($sort, ['name', 'head_doctor', 'staff_count', 'specializations_count', 'status']), fn($q) => $q->orderBy($sort, $direction))
            ->paginate(15)
            ->appends($request->query());

        return view('departments.index', compact('departments', 'search', 'sort', 'direction'));
    }
    
    public function create()
    {
        $doctors = Doctor::where('is_active', true)->orderBy('first_name')->get();
        return view('departments.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'head_doctor_id' => 'nullable|exists:doctors,id',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Department::create($request->all());

        return redirect()->route('departments.index')
            ->with('success', __('file.department_created'));
    }

    public function edit(Department $department)
    {
        $doctors = Doctor::where('is_active', true)->orderBy('first_name')->get();
        return view('departments.edit', compact('department', 'doctors'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'head_doctor_id' => 'nullable|exists:doctors,id',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $department->update($request->all());

        return redirect()->route('departments.index')
            ->with('success', __('file.department_updated'));
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('success', __('file.department_deleted'));
    }
}