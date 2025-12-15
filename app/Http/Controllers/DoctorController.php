<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Department;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DoctorsExport;
use Maatwebsite\Excel\Facades\Excel;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::with(['primarySpecialization', 'department'])
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
            ->with(['primarySpecialization', 'department'])
            ->select('doctors.*')
            ->when($searchValue !== '', function ($q) use ($searchValue) {
                $q->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$searchValue}%"])
                ->orWhere('email', 'like', "%{$searchValue}%")
                ->orWhere('phone', 'like', "%{$searchValue}%")
                ->orWhereHas('primarySpecialization', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"))
                ->orWhereHas('department', fn($sq) => $sq->where('name', 'like', "%{$searchValue}%"));
            })
            ->when($genderFilter, fn($q) => $q->where('gender', $genderFilter))
            ->when($specialtyFilter, fn($q) => $q->where('primary_specialization_id', $specialtyFilter))
            ->when($departmentFilter, fn($q) => $q->where('department_id', $departmentFilter))
            ->when($statusFilter !== null && $statusFilter !== '', fn($q) => $q->where('is_active', $statusFilter))
            ->active();

        $totalRecords    = Doctor::active()->count();
        $filteredRecords = (clone $query)->count();

        // Adjusted column indices after removing License (old index 2)
        switch ($orderIdx) {
            case 1: // Name
                $query->orderByRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) {$orderDir}");
                break;
            case 2: // Gender
                $query->orderByRaw("FIELD(gender, 'male', 'female', 'other') {$orderDir}");
                break;
            case 3: // Department
                $query->join('departments', 'doctors.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', $orderDir);
                break;
            case 4: // Specialty
                $query->join('specializations as spec', 'doctors.primary_specialization_id', '=', 'spec.id')
                    ->orderBy('spec.name', $orderDir);
                break;
            case 5: // Status
                $query->orderBy('is_active', $orderDir === 'desc' ? 'desc' : 'asc');
                break;
            case 6: // Phone
                $query->orderBy('phone', $orderDir);
                break;
            default:
                $query->orderByRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) asc");
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
        $doctor->loadCount('appointments');
        return view('doctors.show', compact('doctor'));
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

        Doctor::whereIn('id', $ids)->update(['is_deleted' => true, 'is_active' => false]);

        return back()->with('success', 'Selected doctors deleted successfully.');
    }

    private function uniqueValues(?string $field = null, ?string $raw = null, ?string $alias = null)
    {
        $query = Doctor::query()->active();

        if ($raw) {
            $query->selectRaw("$raw AS `$alias`");
            $orderBy = $alias;
        } else {
            $query->select($field);
            $orderBy = $field;
        }

        return $query
            ->distinct()
            ->orderBy($orderBy)
            ->pluck($orderBy)
            ->filter()
            ->values()
            ->toArray();
    }
}