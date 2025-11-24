{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}!</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Total Users</h3>
            <p class="text-3xl font-bold text-primary">{{ \App\Models\User::count() }}</p>
        </div>

        @role('admin')
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold">Admin Access</h3>
                <a href="{{ route('admin.users.index') }}" class="text-primary hover:underline">Go to Admin Panel</a>
            </div>
        @endrole
    </div>
</div>
@endsection