<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.index', ['only' => ['index', 'show']]);
        $this->middleware('permission:roles.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:roles.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:roles.delete', ['only' => ['destroy']]);
    }

    /**
     * Roles that are considered "system" roles.
     * Their names cannot be changed and they cannot be deleted.
     * Only their permissions may be edited.
     */
    protected array $systemRoles = ['admin', 'doctor', 'primary_care_provider'];

    /**
     * Only these CRUD actions are shown in the permissions matrix.
     */
    protected array $allowedActions = ['index', 'create', 'edit', 'delete'];

    /**
     * Build the permissions matrix:
     * [ 'resource' => [ 'index' => Permission|null, 'create' => ..., 'edit' => ..., 'delete' => ... ] ]
     */
    protected function getPermissionsMatrix(): \Illuminate\Support\Collection
    {
        return Permission::orderBy('name')
            ->get()
            ->filter(fn($p) => in_array(Str::after($p->name, '.'), $this->allowedActions))
            ->groupBy(fn($p) => Str::before($p->name, '.'))
            ->map(fn($group) => $group->keyBy(fn($p) => Str::after($p->name, '.')))
            ->sortKeys();
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        $systemRoles = $this->systemRoles;
        return view('admin.roles.index', compact('roles', 'systemRoles'));
    }

    public function create()
    {
        $permissionsMatrix = $this->getPermissionsMatrix();
        $allowedActions = $this->allowedActions;

        return view('admin.roles.create', compact('permissionsMatrix', 'allowedActions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')
            ->with('success', __('file.role_created_successfully'));
    }

    public function edit(Role $role)
    {
        $permissionsMatrix = $this->getPermissionsMatrix();
        $allowedActions = $this->allowedActions;

        $role->load('permissions');
        $isSystemRole = in_array($role->name, $this->systemRoles);

        return view('admin.roles.edit', compact('role', 'permissionsMatrix', 'allowedActions', 'isSystemRole'));
    }

    public function update(Request $request, Role $role)
    {
        $isSystemRole = in_array($role->name, $this->systemRoles);

        if ($isSystemRole) {
            $request->validate(['permissions' => 'nullable|array']);
        }
        else {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'permissions' => 'nullable|array',
            ]);
            $role->update(['name' => $request->name]);
        }

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')
            ->with('success', __('file.role_updated_successfully'));
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if (in_array($role->name, $this->systemRoles)) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => __('file.cannot_delete_system_role')], 403);
            }
            return redirect()->route('roles.index')
                ->with('error', __('file.cannot_delete_system_role'));
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('file.role_deleted_successfully')]);
        }

        return redirect()->route('roles.index')
            ->with('success', __('file.role_deleted_successfully'));
    }
}