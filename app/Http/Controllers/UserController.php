<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.index', ['only' => ['index', 'show', 'datatable']]);
        $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    public function index(Request $request)
    {
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
            $statusHtml = $user->is_active
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">' . __('file.active') . '</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">' . __('file.inactive') . '</span>';

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone ?? '-',
                'roles'        => $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->toArray(),
                'status_html'  => $statusHtml,
                'created_at'   => $user->created_at->format('M d, Y'),
                'edit_url'     => \Auth::user()->can('users.edit') ? route('users.edit', $user) : null,
                'delete_url'   => \Auth::user()->can('users.delete') ? route('users.destroy', $user) : null,
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
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => [
                'nullable',
                Rule::unique('users', 'email')->where(function ($q) {
                    return $q->where('is_deleted', false)->where('is_active', true);
                }),
            ],
            'phone'         => [
                'nullable',
                Rule::unique('users', 'phone')->where(function ($q) {
                    return $q->where('is_deleted', false)->where('is_active', true);
                }),
            ],
            'password'      => 'required|min:8|confirmed',
            'role'          => 'required|string|exists:roles,name',
            'is_active'     => 'sometimes|boolean',
        ]);

        $user = User::withTrashed()->where(function($q) use ($request) {
            $q->where('email', $request->email)
              ->orWhere('phone', $request->phone);
        })->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
            $user->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => Hash::make($request->password),
                'is_active'  => $request->boolean('is_active', true),
                'is_deleted' => false,
                'deleted_at' => null,
            ]);
        } else {
            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => Hash::make($request->password),
                'is_active'  => $request->boolean('is_active', true),
                'is_deleted' => false,
            ]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', __('file.record_created'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $currentRole = $user->roles->first()?->name ?? null;

        return view('admin.users.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => [
                'nullable',
                Rule::unique('users', 'email')->ignore($user->id)->where(function ($q) {
                    return $q->where('is_deleted', false)->where('is_active', true);
                }),
            ],
            'phone'     => [
                'nullable',
                Rule::unique('users', 'phone')->ignore($user->id)->where(function ($q) {
                    return $q->where('is_deleted', false)->where('is_active', true);
                }),
            ],
            'password'  => 'nullable|min:8|confirmed',
            'role'      => 'required|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'is_active' => $request->boolean('is_active', $user->is_active),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', __('file.record_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_delete_yourself')], 403);
            }
            return redirect()->back()->with('error', __('file.cannot_delete_yourself'));
        }

        if ($user->is_deleted) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.already_deleted')], 422);
            }
            return redirect()->back()->with('error', __('file.already_deleted'));
        }

        $user->update([
            'is_deleted' => true,
            'is_active'  => false,
            'deleted_at' => now(),
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.record_deleted')]);
        }

        return redirect()->route('users.index')
            ->with('success', __('file.record_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:users,id',
        ]);

        $ids = $validated['ids'];

        $currentUserId = auth()->id();

        if (in_array($currentUserId, $ids)) {
            $ids = array_diff($ids, [$currentUserId]);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('file.cannot_delete_yourself_bulk'),
                ], 403);
            }
        }

        $deletedCount = User::whereIn('id', $ids)
            ->where('id', '!=', $currentUserId)
            ->where('is_deleted', false)
            ->update([
                'is_deleted' => true,
                'is_active'  => false,
                'deleted_at' => now(),
            ]);

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => __('file.no_valid_records_deleted'),
            ], 422);
        }

        return response()->json([
            'success'       => true,
            'message'       => __('file.bulk_deleted', ['count' => $deletedCount]),
            'deleted_count' => $deletedCount,
            'deleted_ids'   => $ids,
        ]);
    }
}