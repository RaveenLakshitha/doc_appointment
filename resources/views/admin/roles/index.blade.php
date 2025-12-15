{{-- resources/views/admin/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', __('file.roles_management'))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('file.roles_and_permissions') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('file.manage_roles_description') }}
                </p>
            </div>

            @can('manage roles and permissions')
                <a href="{{ route('admin.roles.create') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('file.new_role') }}
                </a>
            @endcan
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-lg transition">
                <div class="p-6 pb-4">

                    <!-- Role Name + Badge -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($role->name) }}
                        </h3>
                        @if(strtolower($role->name) === 'admin')
                            <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                {{ __('file.super_admin') }}
                            </span>
                        @endif
                    </div>

                    <!-- Permissions Count -->
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $role->permissions->count() }}</span>
                        <span class="ml-1">
                            {{ __('file.permissions_assigned', ['count' => $role->permissions->count()]) }}
                        </span>
                    </div>

                    <!-- Permission Tags -->
                    @if($role->permissions->count() > 0)
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach($role->permissions->take(4) as $perm)
                                <span class="px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                                    {{ Str::limit(Str::after($perm->name, ' '), 12) }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 4)
                                <span class="px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">
                                    +{{ $role->permissions->count() - 4 }}
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-500 italic">
                            {{ __('file.no_permissions') }}
                        </p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <a href="{{ route('admin.roles.edit', $role) }}"
                       class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('file.edit_role') }}
                    </a>

                    @if(strtolower($role->name) !== 'admin')
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('{{ __('file.confirm_delete_role', ['role' => ucfirst($role->name)]) }}')"
                                    class="inline-flex items-center text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{ __('file.delete') }}
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center text-sm text-gray-400 dark:text-gray-600">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('file.protected') }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-12 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __('file.no_roles_found') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ __('file.no_roles_description') }}
                    </p>
                    @can('manage roles and permissions')
                        <a href="{{ route('admin.roles.create') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('file.create_first_role') }}
                        </a>
                    @endcan
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="mt-8">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection