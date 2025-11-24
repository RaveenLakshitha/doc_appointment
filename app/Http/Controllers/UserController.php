<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::query()
            ->when($search, fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            )
            ->with('roles')
            ->where('is_deleted', false) // hide soft-deleted
            ->paginate(15);

        return view('admin.users.index', compact('users'));
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
            'roles'    => 'array',
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

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
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
            'roles'    => 'array',
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

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        // Soft-delete instead of hard delete
        $user->update(['is_deleted' => true]);

        return back()->with('success', 'User soft-deleted.');
    }
}