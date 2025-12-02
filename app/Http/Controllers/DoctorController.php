<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Department;
use App\Models\Specialization;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
{
    $query = Doctor::query()
        ->with(['primarySpecialization', 'department'])
        ->withCount('appointments');

    // Search
    if ($search = $request->filled('search') ? $request->string('search')->trim() : null) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('license_number', 'like', "%{$search}%")
              ->orWhereHas('primarySpecialization', fn($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }

    // Filters
    if ($request->filled('specialty')) {
        $query->where('primary_specialization_id', $request->specialty);
    }

    if ($request->filled('department')) {
        $query->where('department_id', $request->department);
    }

    if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
        $query->where('is_active', $request->status === 'active');
    }

    if ($request->filled('gender') && in_array($request->gender, ['male', 'female', 'other'])) {
        $query->where('gender', $request->gender);
    }

    // Sorting - SAFE (no joins that break filters)
    $sort = $request->get('sort', 'name');
    $direction = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';

    match ($sort) {
        'name' => $query->orderByRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) {$direction}"),

        'specialty' => $query->orderBy(
            Specialization::select('name')
                ->whereColumn('specializations.id', 'doctors.primary_specialization_id')
                ->limit(1),
            $direction
        ),

        'department' => $query->orderBy(
            Department::select('name')
                ->whereColumn('departments.id', 'doctors.department_id')
                ->limit(1),
            $direction
        ),

        default => $query->orderBy($sort, $direction),
    };

    $doctors = $query->paginate(10)->withQueryString();

    $specialties = Specialization::orderBy('name')->pluck('name', 'id');
    $departments = Department::orderBy('name')->pluck('name', 'id');

    // THIS IS THE ONLY LINE THAT MATTERS FOR AJAX
    if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('doctors.partials.table', compact('doctors'));
    }

    return view('doctors.index', compact('doctors', 'specialties', 'departments'));
}

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'               => 'required|string|max:255',
            'middle_name'              => 'nullable|string|max:255',
            'last_name'                => 'required|string|max:255',
            'date_of_birth'            => 'required|date',
            'gender'                   => 'required|in:male,female,other',
            'address'                  => 'nullable|string',
            'city'                     => 'nullable|string|max:255',
            'state'                    => 'nullable|string|max:255',
            'zip_code'                 => 'nullable|string|max:20',
            'email'                    => 'required|email|unique:doctors,email',
            'phone'                    => 'required|string|max:20',
            'emergency_contact_name'   => 'nullable|string|max:255',
            'emergency_contact_phone'  => 'nullable|string|max:20',
            'primary_specialization_id'=> 'required|exists:specializations,id',
            'secondary_specialty'      => 'nullable|string|max:255',
            'license_number'           => 'required|string|max:100|unique:doctors,license_number',
            'license_expiry_date'      => 'required|date|after:today',
            'qualifications'           => 'nullable|string',
            'years_experience'         => 'required|integer|min:0|max:60',
            'education'                => 'nullable|string',
            'certifications'           => 'nullable|string',
            'department_id'            => 'required|exists:departments,id',
            'position'                 => 'required|string|max:255',
            'hourly_rate'              => 'required|numeric|min:0',
            'profile_photo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('doctors', 'public');
        }

        Doctor::create($validated);

        return redirect()->route('doctors.index')->with('success', __('file.doctor_created'));
    }

    public function show(Doctor $doctor)
    {
        $doctor->loadCount('appointments');
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'first_name'               => 'required|string|max:255',
            'middle_name'              => 'nullable|string|max:255',
            'last_name'                => 'required|string|max:255',
            'date_of_birth'            => 'required|date',
            'gender'                   => 'required|in:male,female,other',
            'address'                  => 'nullable|string',
            'city'                     => 'nullable|string|max:255',
            'state'                    => 'nullable|string|max:255',
            'zip_code'                 => 'nullable|string|max:20',
            'email'                    => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone'                    => 'required|string|max:20',
            'emergency_contact_name'   => 'nullable|string|max:255',
            'emergency_contact_phone'  => 'nullable|string|max:20',
            'primary_specialization_id'=> 'required|exists:specializations,id',
            'secondary_specialty'      => 'nullable|string|max:255',
            'license_number'           => 'required|string|max:100|unique:doctors,license_number,' . $doctor->id,
            'license_expiry_date'      => 'required|date|after:today',
            'qualifications'           => 'nullable|string',
            'years_experience'         => 'required|integer|min:0|max:60',
            'education'                => 'nullable|string',
            'certifications'           => 'nullable|string',
            'department_id'            => 'required|exists:departments,id',
            'position'                 => 'required|string|max:255',
            'hourly_rate'              => 'required|numeric|min:0',
            'is_active'                => 'sometimes|boolean',
            'profile_photo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'first_name','middle_name','last_name','date_of_birth','gender',
            'address','city','state','zip_code','email','phone',
            'emergency_contact_name','emergency_contact_phone',
            'primary_specialization_id','secondary_specialty','license_number',
            'license_expiry_date','qualifications','years_experience',
            'education','certifications','department_id','position',
            'hourly_rate','is_active'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($doctor->profile_photo) {
                \Storage::disk('public')->delete($doctor->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('doctors', 'public');
        }

        $doctor->update($data);

        return redirect()->route('doctors.index')->with('success', __('file.doctor_updated'));
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->profile_photo) {
            \Storage::disk('public')->delete($doctor->profile_photo);
        }

        $doctor->delete();

        return back()->with('success', __('file.doctor_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:doctors,id',
        ]);

        $doctors = Doctor::whereIn('id', $request->ids)->get();

        foreach ($doctors as $doctor) {
            if ($doctor->profile_photo) {
                \Storage::disk('public')->delete($doctor->profile_photo);
            }
            $doctor->delete();
        }

        return back()->with('success', __('file.doctors_deleted'));
    }
}