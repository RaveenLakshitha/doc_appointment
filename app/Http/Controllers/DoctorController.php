<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\OptionList;
use App\Models\AgeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:doctors.index', ['only' => ['index', 'show', 'datatable', 'currentQueue']]);
        $this->middleware('permission:doctors.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:doctors.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:doctors.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
        if (!Auth::user()->can('doctors.index')) {
            return redirect()->route('home')
                ->with('error', __('file.module_access_denied'));
        }

        $doctors = Doctor::with(['user', 'specializations', 'department', 'positionOption', 'ageGroups', 'languages'])
            ->orderByRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name)")
            ->paginate(10)
            ->withQueryString();

        return view('doctors.index', compact('doctors'));
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $orderIdx = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = trim($request->input('search.value', ''));

        $genderFilter = $request->gender;
        $specialtyFilter = $request->specialty;
        $departmentFilter = $request->department;
        $statusFilter = $request->status;

        $query = Doctor::query()
            ->with(['user', 'specializations', 'department', 'positionOption', 'ageGroups', 'languages'])
            ->select('doctors.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$searchValue}%"])
                    ->orWhere('email', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%")
                    ->orWhereHas('specializations', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                    ->orWhereHas('department', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                    ->orWhereHas('positionOption', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($genderFilter, fn($q) => $q->where('gender', $genderFilter))
            ->when($specialtyFilter, fn($q) => $q->whereHas('specializations', fn($sq) => $sq->where('specialization_id', $specialtyFilter)))
            ->when($departmentFilter, fn($q) => $q->where('department_id', $departmentFilter))
            ->when($statusFilter !== null && $statusFilter !== '', fn($q) => $q->where('is_active', $statusFilter));

        $totalRecords = Doctor::count();
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
                $query->join('doctor_specialization', 'doctors.id', '=', 'doctor_specialization.doctor_id')
                    ->join('specializations as spec', 'doctor_specialization.specialization_id', '=', 'spec.id')
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
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">' . __('file.active') . '</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">' . __('file.inactive') . '</span>';

            $genderBadge = match (strtolower($d->gender ?? '')) {
                'male' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">' . __('file.male') . '</span>',
                'female' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300">' . __('file.female') . '</span>',
                default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">' . __('file.other') . '</span>'
            };

            $edit_url = Auth::user()->can('doctors.edit') ? route('doctors.edit', $d) : '';
            $delete_url = Auth::user()->can('doctors.delete') ? route('doctors.destroy', $d) : '';

            return [
                'id' => $d->id,
                'full_name' => $d->full_name ?? '-',
                'gender' => $genderBadge,
                'department' => $d->department?->name ?? '-',
                'specialty' => $d->specializations->pluck('name')->join(', ') ?: '-',
                'position' => $d->positionOption?->name ?? '-',
                'age_groups' => $d->ageGroups->pluck('name')->join(', ') ?: '-',
                'languages' => $d->languages->pluck('name')->join(', ') ?: '-',
                'status_html' => $statusHtml,
                'phone' => $d->phone ?? '-',
                'show_url' => route('doctors.show', $d),
                'edit_url' => $edit_url,
                'delete_url' => $delete_url,
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
        if (!Auth::user()->can('doctors.create')) {
            return redirect()->route('doctors.index')
                ->with('error', __('file.doctors_create_denied'));
        }

        $departments = Department::where('status', true)->orderBy('name')->get(['id', 'name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);
        $positions = OptionList::getOptions('doctor_position');
        $ageGroups = AgeGroup::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $languages = OptionList::getOptions('language');

        $availableUsers = User::role('doctor')
            ->where('is_active', true)
            ->doesntHave('employee')
            ->with('doctor')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        $treatments = \App\Models\Treatment::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('doctors.create', compact(
            'departments',
            'specializations',
            'positions',
            'ageGroups',
            'languages',
            'availableUsers',
            'treatments'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('doctors.create')) {
            abort(403);
        }

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => ['required', Rule::in(['male', 'female', 'other'])],
                'address' => 'nullable|string|max:1000',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|min:7|max:15',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('doctors', 'email')->whereNull('deleted_at')
                        ->when($request->filled('user_id'), function ($rule) use ($request) {
                            $existingDoctor = Doctor::where('user_id', $request->user_id)->first();
                            return $existingDoctor ? $rule->ignore($existingDoctor->id) : $rule;
                        }),
                ],
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|min:7|max:15',
                'specialization_ids' => 'required|array',
                'specialization_ids.*' => 'exists:specializations,id',
                'license_number' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('doctors', 'license_number')->whereNull('deleted_at'),
                ],
                'license_expiry_date' => 'nullable|date|after:today',
                'qualifications' => 'nullable|string|max:1000',
                'years_experience' => 'nullable|integer|min:0|max:100',
                'education' => 'nullable|string|max:2000',
                'certifications' => 'nullable|string|max:2000',
                'department_id' => 'required|exists:departments,id',
                'position_id' => [
                    'required',
                    'exists:option_lists,id',
                    Rule::in(array_keys(OptionList::getOptions('doctor_position'))),
                ],
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'age_group_ids' => 'nullable|array',
                'age_group_ids.*' => 'exists:age_groups,id',
                'language_ids' => 'nullable|array',
                'language_ids.*' => 'exists:option_lists,id',
                'treatments' => 'nullable|array',
                'treatments.*.id' => 'nullable|exists:treatments,id',
                'treatments.*.price' => 'nullable|numeric|min:0',
            ]);

            return DB::transaction(function () use ($request, $validated) {
                // Restoration logic for soft-deleted doctor
                $doctor = Doctor::withTrashed()
                    ->where(function ($q) use ($request) {
                        if ($request->filled('email')) {
                            $q->where('email', $request->email);
                        }
                        if ($request->filled('license_number')) {
                            $q->orWhere('license_number', $request->license_number);
                        }
                    })
                    ->first();

                if ($doctor && $doctor->trashed()) {
                    $doctor->restore();

                    // Also restore user if trashed
                    if ($doctor->user_id) {
                        $user = User::withTrashed()->find($doctor->user_id);
                        if ($user && ($user->trashed() || $user->is_deleted)) {
                            $user->restore();
                            $user->update(['is_active' => true, 'is_deleted' => false]);
                        }
                    }

                    $doctor->update($validated + ['is_active' => true]);
                } else {
                    $user = null;

                    if ($request->filled('user_id')) {
                        $user = User::findOrFail($validated['user_id']);

                        if ($user->doctor) {
                            throw ValidationException::withMessages([
                                'user_id' => __('file.user_already_has_doctor_profile'),
                            ]);
                        }
                    }

                if (!$user && !isset($doctor)) {
                    throw ValidationException::withMessages([
                        'user_id' => __('file.must_select_user'),
                    ]);
                }

                $profilePhotoPath = null;
                if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
                    $profilePhotoPath = $request->file('profile_photo')->store('doctors/photos', 'public');
                }

                if (!isset($doctor) || !$doctor->wasRecentlyRestored) {
                    $doctor = Doctor::create([
                        'user_id' => $user->id,
                        'first_name' => $validated['first_name'],
                        'middle_name' => $validated['middle_name'] ?? null,
                        'last_name' => $validated['last_name'],
                        'date_of_birth' => $validated['date_of_birth'] ?? null,
                        'gender' => $validated['gender'],
                        'address' => $validated['address'] ?? null,
                        'city' => $validated['city'] ?? null,
                        'state' => $validated['state'] ?? null,
                        'zip_code' => $validated['zip_code'] ?? null,
                        'phone' => $validated['phone'] ?? null,
                        'email' => $validated['email'] ?? null,
                        'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                        'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                        'license_number' => $validated['license_number'] ?? null,
                        'license_expiry_date' => $validated['license_expiry_date'] ?? null,
                        'qualifications' => $validated['qualifications'] ?? null,
                        'years_experience' => $validated['years_experience'] ?? null,
                        'education' => $validated['education'] ?? null,
                        'certifications' => $validated['certifications'] ?? null,
                        'department_id' => $validated['department_id'],
                        'primary_specialization_id' => $validated['specialization_ids'][0] ?? null,
                        'position_id' => $validated['position_id'],
                        'profile_photo' => $profilePhotoPath,
                        'is_active' => true,
                    ]);
                }
            }

            $doctor->specializations()->sync($request->input('specialization_ids', []));
            $doctor->ageGroups()->sync($request->input('age_group_ids', []));
            $doctor->languages()->sync($request->input('language_ids', []));

                if ($request->has('treatments') && is_array($request->input('treatments'))) {
                    $syncData = [];
                    foreach ($request->input('treatments') as $item) {
                        if (!empty($item['id']) && is_numeric($item['price'])) {
                            $syncData[$item['id']] = ['price' => $item['price']];
                        }
                    }
                    $doctor->treatments()->sync($syncData);
                }

                return redirect()->route('doctors.index')
                    ->with('success', __('file.doctor_created_successfully'));
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Doctor creation failed', [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'input' => $request->except(['new_user_password', 'new_user_password_confirmation', 'profile_photo']),
            ]);

            return back()
                ->withInput()
                ->with('error', __('file.doctor_creation_failed'));
        }
    }

    public function show(Doctor $doctor)
    {
        if (!Auth::user()->can('doctors.index')) {
            return redirect()->route('doctors.index')
                ->with('error', __('file.doctors_show_denied'));
        }

        $doctor->load([
            'user',
            'specializations',
            'department',
            'positionOption',
            'ageGroups',
            'languages',
            'treatments',
            'appointments' => fn($q) => $q->latest()->take(5),
            'schedules' => fn($q) => $q->with('room')->where('is_active', true),
        ]);

        $doctor->loadCount('appointments');

        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        if (!Auth::user()->can('doctors.edit')) {
            return redirect()->route('doctors.index')
                ->with('error', __('file.doctors_edit_denied'));
        }

        if (!$doctor->is_active || $doctor->trashed()) {
            abort(404);
        }

        $doctor->load(['user', 'ageGroups', 'languages', 'specializations']);

        $departments = \App\Models\Department::where('status', true)->orderBy('name')->get(['id', 'name']);
        $specializations = \App\Models\Specialization::orderBy('name')->get(['id', 'name']);
        $positions = \App\Models\OptionList::getOptions('doctor_position');
        $ageGroups = \App\Models\AgeGroup::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $languages = \App\Models\OptionList::getOptions('language');

        $treatments = \App\Models\Treatment::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $availableUsers = User::role('doctor')
            ->where('is_active', true)
            ->where(function($q) use ($doctor) {
                $q->doesntHave('employee')
                  ->orWhere('id', $doctor->user_id);
            })
            ->with('doctor')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('doctors.edit', compact(
            'doctor',
            'departments',
            'specializations',
            'positions',
            'ageGroups',
            'languages',
            'treatments',
            'availableUsers'
        ));
    }

    public function update(Request $request, Doctor $doctor)
    {
        if (!Auth::user()->can('doctors.edit')) {
            abort(403, __('file.unauthorized_action'));
        }

        if (!$doctor->is_active || $doctor->trashed()) {
            abort(404);
        }

        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('doctors', 'user_id')->ignore($doctor->id)
            ],
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|min:7|max:15',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('doctors', 'email')->ignore($doctor->id)->whereNull('deleted_at'),
            ],
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|min:7|max:15',
            'specialization_ids' => 'required|array',
            'specialization_ids.*' => 'exists:specializations,id',
            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('doctors', 'license_number')->ignore($doctor->id)->whereNull('deleted_at'),
            ],
            'license_expiry_date' => 'nullable|date|after:today',
            'qualifications' => 'nullable|string|max:1000',
            'years_experience' => 'nullable|integer|min:0|max:100',
            'education' => 'nullable|string|max:2000',
            'certifications' => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
            'position_id' => [
                'required',
                'exists:option_lists,id',
                Rule::in(array_keys(OptionList::getOptions('doctor_position'))),
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_profile_photo' => 'sometimes|boolean',
            'age_group_ids' => 'nullable|array',
            'age_group_ids.*' => 'exists:age_groups,id',
            'language_ids' => 'nullable|array',
            'language_ids.*' => 'exists:option_lists,id',
            'treatments' => 'nullable|array',
            'treatments.*.id' => 'nullable|exists:treatments,id',
            'treatments.*.price' => 'nullable|numeric|min:0',
        ]);

        $profilePhotoPath = $doctor->profile_photo;

        if ($request->boolean('remove_profile_photo') && $profilePhotoPath) {
            Storage::disk('public')->delete($profilePhotoPath);
            $profilePhotoPath = null;
        }

        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            if ($profilePhotoPath) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('doctors/photos', 'public');
        }

        DB::transaction(function () use ($request, $doctor, $validated, &$profilePhotoPath) {
            $user = null;

            if ($request->filled('user_id')) {
                $user = User::findOrFail($validated['user_id']);
            }

            $doctor->update([
                'user_id' => $user ? $user->id : $doctor->user_id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'license_expiry_date' => $validated['license_expiry_date'] ?? null,
                'qualifications' => $validated['qualifications'] ?? null,
                'years_experience' => $validated['years_experience'] ?? null,
                'education' => $validated['education'] ?? null,
                'certifications' => $validated['certifications'] ?? null,
                'department_id' => $validated['department_id'],
                'primary_specialization_id' => $validated['specialization_ids'][0] ?? $doctor->primary_specialization_id,
                'position_id' => $validated['position_id'],
                'profile_photo' => $profilePhotoPath,
            ]);

            $doctor->specializations()->sync($request->input('specialization_ids', []));

            if ($doctor->user) {
                $doctor->user->update([
                    'name' => 'Dr. ' . trim($doctor->first_name . ' ' . ($doctor->middle_name ?? '') . ' ' . $doctor->last_name),
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                ]);
            }

            $doctor->ageGroups()->sync($request->input('age_group_ids', []));
            $doctor->languages()->sync($request->input('language_ids', []));

            if ($request->has('treatments') && is_array($request->input('treatments'))) {
                $syncData = [];
                foreach ($request->input('treatments') as $item) {
                    if (!empty($item['id']) && is_numeric($item['price'])) {
                        $syncData[$item['id']] = ['price' => $item['price']];
                    }
                }
                $doctor->treatments()->sync($syncData);
            }
        });

        return redirect()->route('doctors.index')
            ->with('success', __('file.doctor_updated_successfully'));
    }

    public function destroy(Doctor $doctor)
    {
        if (!Auth::user()->can('doctors.delete')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return redirect()->route('doctors.index')
                ->with('error', __('file.permission_denied'));
        }

        // Note: Soft deleting. Profile photo is kept in case of restoration.
        $doctor->update(['user_id' => null]);
        $doctor->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.doctor_deleted_successfully')]);
        }

        return back()->with('success', __('file.doctor_deleted_successfully'));
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('doctors.delete')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.permission_denied')], 403);
            }
            return back()->with('error', __('file.permission_denied'));
        }

        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids ?? '')));
        }

        if (!is_array($ids) || empty($ids)) {
            $msg = __('file.no_items_selected');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        $validator = Validator::make(['ids' => $ids], [
            'ids'   => 'required|array',
            'ids.*' => 'exists:doctors,id'
        ]);

        if ($validator->fails()) {
            $msg = 'Validation failed';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $validator->errors()], 422);
            }
            return back()->with('error', $msg);
        }

        Doctor::whereIn('id', $ids)->update(['user_id' => null]);
        Doctor::whereIn('id', $ids)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.selected_doctors_deleted')]);
        }

        return back()->with('success', __('file.selected_doctors_deleted'));
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
}