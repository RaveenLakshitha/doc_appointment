<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage roles and permissions|manage staff');
    }

    public function index()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.users.roles-index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.assign-role', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array'
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Roles assigned successfully.');
    }
}