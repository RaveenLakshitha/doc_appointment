<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Department;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $specializations = \App\Models\Specialization::query()
            ->with(['department'])
            ->withCount(['doctors' => fn($q) => $q->where('is_active', true)])
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when(in_array($sort, ['name', 'department_id', 'doctors_count']), fn($q) => $q->orderBy($sort, $direction))
            ->paginate(15)
            ->appends($request->query());

        return view('specializations.index', compact('specializations', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $departments = Department::where('status', true)->orderBy('name')->get();
        return view('specializations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        Specialization::create($request->all());

        return redirect()->route('departments.index')
            ->with('success', __('file.specialization_created'));
    }
}