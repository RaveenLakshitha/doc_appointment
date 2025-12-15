<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // This method is no longer used for the table (DataTables handles it)
        // Keep it only if you have a fallback non-JS view
        return view('admin.users.index');
    }

    public function datatable(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $search = trim($request->input('search.value', ''));

        $role   = $request->role;
        $status = $request->status;
        $from   = $request->from;
        $to     = $request->to;

        $query = User::query()
            ->with('roles')
            ->when($search !== '', fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            )
            ->when($role, fn($q) => $q->role($role))
            ->when($status !== null && $status !== '', fn($q) => $q->where('is_active', $status))
            ->when($from || $to, fn($q) => $q->whereBetween('created_at', [
                $from ? $from . ' 00:00:00' : '1900-01-01',
                $to   ? $to   . ' 23:59:59' : now()
            ]))
            ->where('is_deleted', false);

        $totalRecords = User::where('is_deleted', false)->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColumn = match ((int)$orderColumnIndex) {
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            5 => 'is_active',
            6 => 'created_at',
            default => 'name'
        };
        $query->orderBy($orderColumn, $orderDir);

        $users = $query->offset($start)->limit($length)->get();

        $data = $users->map(function ($user) {
            $roleBadges = $user->roles->map(fn($r) => ucfirst($r->name))->implode(', ') ?: '-';

            $statusHtml = $user->is_active
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Inactive</span>';

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone ?? '-',
                'roles'        => $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->toArray(),
                'status_html'  => $statusHtml,
                'created_at'   => $user->created_at->format('M d, Y'),
                'edit_url'     => route('admin.users.edit', $user),
                'delete_url'   => route('admin.users.destroy', $user),
            ];
        });

        return response()->json([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data->toArray(),
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:users,phone',
            'password' => 'required|min:8|confirmed',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'is_active'  => $request->boolean('is_active', true),
            'is_deleted' => false,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'required|string|regex:/^\+?[0-9]{10,15}$/|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'is_active' => $request->boolean('is_active', $user->is_active),
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->update(['is_deleted' => true, 'is_active' => false]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        // Prevent self-deletion
        $ids = array_diff($ids, [auth()->id()]);

        if (empty($ids)) {
            return response()->json(['error' => 'No users to delete.'], 400);
        }

        User::whereIn('id', $ids)
            ->update(['is_deleted' => true, 'is_active' => false]);

        return response()->json(['success' => true]);
    }

    // Optional: Export methods (Excel, CSV, PDF)
    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new UsersExport, 'users-' . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf()
    {
        $users = User::where('is_deleted', false)->with('roles')->get();

        $pdf = Pdf::loadView('admin.users.exports.pdf', compact('users'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('users-list-' . now()->format('Y-m-d') . '.pdf');
    }
}