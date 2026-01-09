<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\OptionList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::with(['user', 'primarySpecialization', 'department', 'positionOption'])
            ->active()
            ->orderByRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name)")
            ->paginate(10)
            ->withQueryString();

        return view('doctors.index', compact('doctors'));
    }

    public function datatable(Request $request)
    {
        $draw        = $request->input('draw');
        $start       = $request->input('start', 0);
        $length      = $request->input('length', 10);
        $orderIdx    = $request->input('order.0.column');
        $orderDir    = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $genderFilter     = $request->gender;
        $specialtyFilter  = $request->specialty;
        $departmentFilter = $request->department;
        $statusFilter     = $request->status;

        $query = Doctor::query()
            ->with(['user', 'primarySpecialization', 'department', 'positionOption'])
            ->select('doctors.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$searchValue}%"])
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%")
                  ->orWhereHas('primarySpecialization', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                  ->orWhereHas('department', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                  ->orWhereHas('positionOption', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($genderFilter, fn($q) => $q->where('gender', $genderFilter))
            ->when($specialtyFilter, fn($q) => $q->where('primary_specialization_id', $specialtyFilter))
            ->when($departmentFilter, fn($q) => $q->where('department_id', $departmentFilter))
            ->when($statusFilter !== null && $statusFilter !== '', fn($q) => $q->where('is_active', $statusFilter))
            ->active();

        $totalRecords    = Doctor::active()->count();
        $filteredRecords = (clone $query)->count();

        switch ($orderIdx) {
            case 1:
                $query->orderByRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) {$orderDir}");
                break;
            case 2:
                $query->orderByRaw("FIELD(gender, 'male', 'female', 'other') {$orderDir}");
                break;
            case 3:
                $query->join('departments', 'doctors.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', $orderDir);
                break;
            case 4:
                $query->join('specializations as spec', 'doctors.primary_specialization_id', '=', 'spec.id')
                      ->orderBy('spec.name', $orderDir);
                break;
            case 5:
                $query->join('option_lists as pos', 'doctors.position_id', '=', 'pos.id')
                      ->orderBy('pos.name', $orderDir);
                break;
            case 6:
                $query->orderBy('is_active', $orderDir === 'desc' ? 'desc' : 'asc');
                break;
            case 7:
                $query->orderBy('phone', $orderDir);
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $doctors = $query->offset($start)->limit($length)->get();

        $data = $doctors->map(function ($d) {
            $statusHtml = $d->is_active
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactive</span>';

            $genderBadge = match(strtolower($d->gender ?? '')) {
                'male'   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Male</span>',
                'female' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300">Female</span>',
                default  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Other</span>'
            };

            return [
                'id'             => $d->id,
                'full_name'      => $d->getFullNameAttribute() ?? '-',
                'gender'         => $genderBadge,
                'department'     => $d->department?->name ?? '-',
                'specialty'      => $d->primarySpecialization?->name ?? '-',
                'position'       => $d->positionOption?->name ?? '-',
                'status_html'    => $statusHtml,
                'phone'          => $d->phone ?? '-',
                'show_url'       => route('doctors.show', $d),
                'edit_url'       => route('doctors.edit', $d),
                'delete_url'     => route('doctors.destroy', $d),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function show(Doctor $doctor)
    {
        $doctor->load([
            'user',
            'primarySpecialization',
            'department',
            'positionOption',
            'appointments' => fn($q) => $q->latest()->take(5),
            'schedules'    => fn($q) => $q->with('room')->where('is_active', true),
        ]);

        $doctor->appointments_count = $doctor->appointments()->count();

        return view('doctors.show', compact('doctor'));
    }

    public function create()
    {
        $departments     = Department::where('status', true)->orderBy('name')->get(['id', 'name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);
        $positions       = OptionList::getOptions('doctor_position');

        $availableUsers = User::role('doctor')
            ->whereDoesntHave('doctor')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('doctors.create', compact('departments', 'specializations', 'positions', 'availableUsers'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'                   => 'nullable|exists:users,id',
                'create_new_user'           => 'sometimes|boolean',
                'new_user_email'            => 'required_if:create_new_user,1|nullable|email|unique:users,email|unique:doctors,email',
                'new_user_password'         => 'required_if:create_new_user,1|nullable|min:8|confirmed',
                'first_name'                => 'required|string|max:255',
                'middle_name'               => 'nullable|string|max:255',
                'last_name'                 => 'required|string|max:255',
                'date_of_birth'             => 'required|date|before:today',
                'gender'                    => ['required', Rule::in(['male', 'female', 'other'])],
                'address'                   => 'nullable|string|max:1000',
                'city'                      => 'nullable|string|max:100',
                'state'                     => 'nullable|string|max:100',
                'zip_code'                  => 'nullable|string|max:20',
                'phone'                     => 'required|string|max:20',
                'email'                     => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('doctors', 'email')->when(!$request->filled('user_id'), fn($rule) => $rule),
                ],
                'emergency_contact_name'    => 'nullable|string|max:255',
                'emergency_contact_phone'   => 'nullable|string|max:20',
                'primary_specialization_id' => 'required|exists:specializations,id',
                'license_number'            => 'required|string|max:100|unique:doctors,license_number',
                'license_expiry_date'       => 'required|date|after:today',
                'qualifications'            => 'nullable|string|max:1000',
                'years_experience'          => 'required|integer|min:0|max:100',
                'education'                 => 'nullable|string|max:2000',
                'certifications'            => 'nullable|string|max:2000',
                'department_id'             => 'required|exists:departments,id',
                'position_id'               => [
                    'required',
                    'exists:option_lists,id',
                    Rule::in(array_keys(OptionList::getOptions('doctor_position'))),
                ],
                'hourly_rate'               => 'required|numeric|min:0',
                'profile_photo'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            return DB::transaction(function () use ($request, $validated) {
                $user = null;

                if ($request->filled('user_id')) {
                    $user = User::findOrFail($validated['user_id']);

                    if ($user->doctor) {
                        throw ValidationException::withMessages([
                            'user_id' => __('This user already has a doctor profile.'),
                        ]);
                    }
                }

                if ($request->boolean('create_new_user')) {
                    $user = User::create([
                        'name'              => 'Dr. ' . $validated['first_name'] . ' ' . $validated['last_name'],
                        'email'             => $validated['new_user_email'],
                        'phone'             => $validated['phone'],
                        'password'          => Hash::make($validated['new_user_password']),
                        'email_verified_at' => now(),
                    ]);

                    $user->assignRole('doctor');
                }

                if (!$user) {
                    throw ValidationException::withMessages([
                        'user_id' => __('You must select an existing user or create a new one.'),
                    ]);
                }

                $profilePhotoPath = null;
                if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
                    $profilePhotoPath = $request->file('profile_photo')->store('doctors/photos', 'public');
                }

                Doctor::create([
                    'user_id'                   => $user->id,
                    'first_name'                => $validated['first_name'],
                    'middle_name'               => $validated['middle_name'] ?? null,
                    'last_name'                 => $validated['last_name'],
                    'date_of_birth'             => $validated['date_of_birth'],
                    'gender'                    => $validated['gender'],
                    'address'                   => $validated['address'] ?? null,
                    'city'                      => $validated['city'] ?? null,
                    'state'                     => $validated['state'] ?? null,
                    'zip_code'                  => $validated['zip_code'] ?? null,
                    'phone'                     => $validated['phone'],
                    'email'                     => $validated['email'],
                    'emergency_contact_name'    => $validated['emergency_contact_name'] ?? null,
                    'emergency_contact_phone'   => $validated['emergency_contact_phone'] ?? null,
                    'license_number'            => $validated['license_number'],
                    'license_expiry_date'       => $validated['license_expiry_date'],
                    'qualifications'            => $validated['qualifications'] ?? null,
                    'years_experience'          => $validated['years_experience'],
                    'education'                 => $validated['education'] ?? null,
                    'certifications'            => $validated['certifications'] ?? null,
                    'department_id'             => $validated['department_id'],
                    'primary_specialization_id' => $validated['primary_specialization_id'],
                    'position_id'               => $validated['position_id'],
                    'hourly_rate'               => $validated['hourly_rate'],
                    'profile_photo'             => $profilePhotoPath,
                    'is_active'                 => true,
                ]);

                return redirect()->route('doctors.index')
                    ->with('success', __('file.doctor_created_successfully'));
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Doctor creation failed', [
                'exception' => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'user_id'   => auth()->id(),
                'input'     => $request->except(['new_user_password', 'new_user_password_confirmation', 'profile_photo']),
            ]);

            return back()
                ->withInput()
                ->with('error', __('An error occurred while creating the doctor. Please try again.'));
        }
    }

    public function edit(Doctor $doctor)
    {
        if (!$doctor->is_active || $doctor->trashed()) {
            abort(404);
        }

        $doctor->load('user'); // Ensures $doctor->user is available in the view

        $departments     = Department::where('status', true)->orderBy('name')->get(['id', 'name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);
        $positions       = OptionList::getOptions('doctor_position');

        return view('doctors.edit', compact('doctor', 'departments', 'specializations', 'positions'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        if (!$doctor->is_active || $doctor->trashed()) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name'                => 'required|string|max:255',
            'middle_name'               => 'nullable|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'date_of_birth'             => 'required|date|before:today',
            'gender'                    => ['required', Rule::in(['male', 'female', 'other'])],
            'address'                   => 'nullable|string|max:1000',
            'city'                      => 'nullable|string|max:100',
            'state'                     => 'nullable|string|max:100',
            'zip_code'                  => 'nullable|string|max:20',
            'phone'                     => 'required|string|max:20',
            'email'                     => ['required', 'email', 'max:255', Rule::unique('doctors', 'email')->ignore($doctor->id)],
            'emergency_contact_name'    => 'nullable|string|max:255',
            'emergency_contact_phone'   => 'nullable|string|max:20',
            'primary_specialization_id' => 'required|exists:specializations,id',
            'license_number'            => ['required', 'string', 'max:100', Rule::unique('doctors', 'license_number')->ignore($doctor->id)],
            'license_expiry_date'       => 'required|date|after:today',
            'qualifications'            => 'nullable|string|max:1000',
            'years_experience'          => 'required|integer|min:0|max:100',
            'education'                 => 'nullable|string|max:2000',
            'certifications'            => 'nullable|string|max:2000',
            'department_id'             => 'required|exists:departments,id',
            'position_id'               => [
                'required',
                'exists:option_lists,id',
                Rule::in(array_keys(OptionList::getOptions('doctor_position'))),
            ],
            'hourly_rate'               => 'required|numeric|min:0',
            'profile_photo'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle profile photo
        $profilePhotoPath = $doctor->profile_photo;
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            if ($profilePhotoPath) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('doctors/photos', 'public');
        }

        // Update doctor fields
        $doctor->update([
            'first_name'                => $validated['first_name'],
            'middle_name'               => $validated['middle_name'] ?? null,
            'last_name'                 => $validated['last_name'],
            'date_of_birth'             => $validated['date_of_birth'],
            'gender'                    => $validated['gender'],
            'address'                   => $validated['address'] ?? null,
            'city'                      => $validated['city'] ?? null,
            'state'                     => $validated['state'] ?? null,
            'zip_code'                  => $validated['zip_code'] ?? null,
            'phone'                     => $validated['phone'],
            'email'                     => $validated['email'],
            'emergency_contact_name'    => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone'   => $validated['emergency_contact_phone'] ?? null,
            'license_number'            => $validated['license_number'],
            'license_expiry_date'       => $validated['license_expiry_date'],
            'qualifications'            => $validated['qualifications'] ?? null,
            'years_experience'          => $validated['years_experience'],
            'education'                 => $validated['education'] ?? null,
            'certifications'            => $validated['certifications'] ?? null,
            'department_id'             => $validated['department_id'],
            'primary_specialization_id' => $validated['primary_specialization_id'],
            'position_id'               => $validated['position_id'],
            'hourly_rate'               => $validated['hourly_rate'],
            'profile_photo'             => $profilePhotoPath,
        ]);

        // Sync basic info to linked user (if exists) - keeps user record consistent
        if ($doctor->user) {
            $doctor->user->update([
                'name'  => 'Dr. ' . $doctor->getFullNameAttribute(),
                'email' => $doctor->email,   // Keep user email in sync with doctor's email
                'phone' => $doctor->phone,
            ]);
        }

        return redirect()->route('doctors.index')
            ->with('success', __('file.doctor_updated_successfully'));
    }

    public function filters(Request $request)
    {
        $column = $request->query('column');

        if ($column === 'specialty') {
            return Specialization::orderBy('name')->pluck('name', 'id');
        }

        if ($column === 'department') {
            return Department::orderBy('name')->pluck('name', 'id');
        }

        return response()->json([]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'No doctors selected.');
        }

        $doctors = Doctor::whereIn('id', $ids)->get();
        foreach ($doctors as $doctor) {
            if ($doctor->profile_photo) {
                Storage::disk('public')->delete($doctor->profile_photo);
            }
        }

        Doctor::whereIn('id', $ids)->update(['is_active' => false]);

        return back()->with('success', 'Selected doctors deleted successfully.');
    }
}