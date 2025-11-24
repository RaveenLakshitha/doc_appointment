@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Create New User</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Name</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2" value="{{ old('email') }}">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Phone</label>
            <input type="text" name="phone" required class="w-full border rounded px-3 py-2" value="{{ old('phone') }}"
                   placeholder="+1234567890">
            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Password</label>
            <input type="password" name="password" required minlength="8" class="w-full border rounded px-3 py-2">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2">
        </div>

        <!-- Active Status -->
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="rounded">
                <span class="ml-2">Active</span>
            </label>
        </div>

        <!-- Roles -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Roles</label>
            @foreach($roles as $role)
                <label class="inline-flex items-center mr-4">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="rounded">
                    <span class="ml-2">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                </label>
            @endforeach
            @error('roles') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-600">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection