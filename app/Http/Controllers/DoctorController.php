<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::query()
            ->withCount('appointments')
            ->with('specialization');

        // Global Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('license_number', 'LIKE', "%{$search}%")
                ->orWhere('primary_specialty', 'LIKE', "%{$search}%")
                ->orWhereHas('specialization', function ($sq) use ($search) {
                    $sq->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        $sort = $request->input('sort', 'name'); 
        $direction = $request->input('direction', 'asc'); 

        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        if ($sort === 'name') {
            $query->orderBy('last_name', $direction)
                ->orderBy('first_name', $direction);
        } elseif ($sort === 'primary_specialty') {
            $query->orderBy('primary_specialty', $direction);
        } elseif ($sort === 'phone') {
            $query->orderBy('phone', $direction);
        } elseif ($sort === 'is_active') {
            $query->orderBy('is_active', $direction === 'desc' ? 'asc' : 'desc');
        } elseif ($sort === 'appointments_count') {
            $query->orderBy('appointments_count', $direction);
        } else {
            $query->latest();
        }

        $doctors = $query->paginate(10)->withQueryString();

        return view('doctors.index', compact(
            'doctors',
            'sort', 
            'direction'
        ));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal
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

            // Professional
            'primary_specialty'        => 'required|string|max:255',
            'secondary_specialty'      => 'nullable|string|max:255',
            'license_number'           => 'required|string|max:100|unique:doctors,license_number',
            'license_expiry_date'      => 'required|date|after:today',
            'qualifications'           => 'nullable|string',
            'years_experience'         => 'required|integer|min:0|max:60',
            'education'                => 'nullable|string',
            'certifications'           => 'nullable|string',
            'department'               => 'required|string|max:255',
            'position'                 => 'required|string|max:255',
            'hourly_rate'              => 'required|numeric|min:0',

            // File
            'profile_photo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('doctors', 'public');
        }

        Doctor::create($validated);

        return redirect()->route('doctors.index')
            ->with('success', __('file.doctor_created'));
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

            'primary_specialty'        => 'required|string|max:255',
            'secondary_specialty'      => 'nullable|string|max:255',
            'license_number'           => 'required|string|max:100|unique:doctors,license_number,' . $doctor->id,
            'license_expiry_date'      => 'required|date|after:today',
            'qualifications'           => 'nullable|string',
            'years_experience'         => 'required|integer|min:0|max:60',
            'education'                => 'nullable|string',
            'certifications'           => 'nullable|string',
            'department'               => 'required|string|max:255',
            'position'                 => 'required|string|max:255',
            'hourly_rate'              => 'required|numeric|min:0',
            'is_active'                => 'sometimes|boolean',

            'profile_photo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
            'address', 'city', 'state', 'zip_code', 'email', 'phone',
            'emergency_contact_name', 'emergency_contact_phone',
            'primary_specialty', 'secondary_specialty', 'license_number',
            'license_expiry_date', 'qualifications', 'years_experience',
            'education', 'certifications', 'department', 'position',
            'hourly_rate', 'is_active'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($doctor->profile_photo) {
                \Storage::disk('public')->delete($doctor->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('doctors', 'public');
        }

        $doctor->update($data);

        return redirect()->route('doctors.index')
            ->with('success', __('file.doctor_updated'));
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