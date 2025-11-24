<?php
// app/Http/Controllers/PatientController.php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $sort       = $request->get('sort', 'name');        // default sort by name
        $direction  = $request->get('direction', 'asc');

        $query = Patient::query()
            ->when($search, function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('medical_record_number', 'like', "%{$search}%");
            })
            ->when($sort === 'name', fn($q) => $q->orderBy('last_name', $direction)->orderBy('first_name', $direction))
            ->when($sort === 'email', fn($q) => $q->orderBy('email', $direction))
            ->when($sort === 'phone', fn($q) => $q->orderBy('phone', $direction))
            ->when($sort === 'medical_record_number', fn($q) => $q->orderBy('medical_record_number', $direction))
            ->active(); // uses your scopeActive()

        $patients = $query->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                     => 'required|string|max:255',
            'email'                    => 'required|email|unique:patients,email',
            'phone'                    => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:patients,phone',
            'medical_record_number'    => 'required|string|unique:patients,medical_record_number',
            'date_of_birth'            => 'nullable|date',
            'gender'                   => 'nullable|in:male,female,other',
            'address'                  => 'nullable|string',
            'emergency_contact_name'   => 'nullable|string',
            'emergency_contact_phone'  => 'nullable|string',
        ]);

        Patient::create($request->only([
            'name', 'email', 'phone', 'medical_record_number',
            'date_of_birth', 'gender', 'address',
            'emergency_contact_name', 'emergency_contact_phone',
        ]) + ['is_active' => true, 'is_deleted' => false]);

        return redirect()->route('patients.index')->with('success', 'Patient created.');
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name'                     => 'required|string|max:255',
            'email'                    => 'required|email|unique:patients,email,' . $patient->id,
            'phone'                    => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:patients,phone,' . $patient->id,
            'medical_record_number'    => 'required|string|unique:patients,medical_record_number,' . $patient->id,
            'date_of_birth'            => 'nullable|date',
            'gender'                   => 'nullable|in:male,female,other',
            'address'                  => 'nullable|string',
            'emergency_contact_name'   => 'nullable|string',
            'emergency_contact_phone'  => 'nullable|string',
            'is_active'                => 'sometimes|boolean',
        ]);

        $patient->update($request->only([
            'name', 'email', 'phone', 'medical_record_number',
            'date_of_birth', 'gender', 'address',
            'emergency_contact_name', 'emergency_contact_phone',
            'is_active',
        ]));

        return redirect()->route('patients.index')->with('success', 'Patient updated.');
    }

    public function destroy(Patient $patient)
    {
        $patient->update(['is_deleted' => true, 'is_active' => false]);
        return back()->with('success', 'Patient soft-deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:patients,id',
        ]);

        Patient::whereIn('id', $request->ids)
            ->update(['is_deleted' => true, 'is_active' => false]);

        Patient::whereIn('id', $request->ids)->delete(); // Soft delete

        return back()->with('success', 'Selected patients soft-deleted.');
    }
}