@extends('layouts.app')

@section('title', __('file.roles_management'))

@section('content')

<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">

    @php
        $systemRoles = ['admin', 'doctor', 'primary_care_provider'];
    @endphp

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.roles_and_permissions') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ __('file.manage_roles_description') }}
            </p>
        </div>
        @can('roles.create')
            <a href="{{ route('roles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.create_role') }}
            </a>
        @endcan
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('file.role_name') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('file.permissions') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('file.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                    @php
                        $isSystem = in_array($role->name, $systemRoles);
                        $roleLabelKey = 'file.role_' . $role->name;
                        $roleLabel = __($roleLabelKey) !== $roleLabelKey
                            ? __($roleLabelKey)
                            : ucfirst(str_replace('_', ' ', $role->name));
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $roleLabel }}
                                </span>
                                @if($isSystem)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        {{ __('file.system_role') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $role->permissions->count() }} {{ Str::plural(__('file.permissions'), $role->permissions->count()) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Edit / Manage Permissions --}}
                                @can('roles.edit')
                                    <a href="{{ route('roles.edit', $role) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                       title="{{ $isSystem ? __('file.manage_permissions') : __('file.edit') }}">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ $isSystem ? __('file.manage_permissions') : __('file.edit') }}
                                    </a>
                                @endcan

                                {{-- Delete – only for non-system roles --}}
                                @if(!$isSystem)
                                    @can('roles.delete')
                                        <button type="button" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')"
                                                class="inline-flex items-center px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            {{ __('file.delete') }}
                                        </button>
                                    @endcan
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('file.no_roles_found') }}
                            </p>
                            @can('roles.create')
                                <a href="{{ route('roles.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('file.create_role') }}
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="mt-6">
            {{ $roles->links() }}
        </div>
    @endif
</div>
    @push('scripts')
        <script>
            window.deleteRole = function(id, name) {
                if (!confirm(`{{ __('file.confirm_delete_role', ['role' => ':name']) }}`.replace(':name', name))) return;

                const url = `{{ route('roles.destroy', ':id') }}`.replace(':id', id);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ '_method': 'DELETE' })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showNotification === 'function') {
                            showNotification('Success', data.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    } else {
                        if (typeof showNotification === 'function') {
                            showNotification('Error', data.message, 'error');
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred');
                });
            };
        </script>
    @endpush
@endsection