@extends('layouts.app')

@section('title', __('file.edit_role') . ': ' . ucfirst($role->name))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">

    <!-- Breadcrumb + Header -->
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('roles.index') }}">
                {{ __('file.roles') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium">{{ __('file.edit_role') }}</span>
        </nav>

        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('file.edit_role_name', ['name' => ucfirst($role->name)]) }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('file.update_role_details') }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <!-- Role Name Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('file.role_information') }}
                </h2>
            </div>

            <div class="p-6">
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.role_name') }}
                    </label>

                    <!-- Always show role name as label, never as input -->
                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ ucfirst($role->name) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('file.permissions') }}
                </h2>
                <button type="button" onclick="toggleAll()" class="text-sm font-medium text-blue-600 dark:text-blue-400">
                    {{ __('file.select_all') }} / {{ __('file.deselect_all') }}
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($groupedPermissions as $group => $perms)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50/30 dark:bg-gray-900/20">
                            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize">
                                    {{ str_replace('-', ' ', $group) }}
                                </h3>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           onclick="toggleGroup('{{ $group }}')" 
                                           class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                </label>
                            </div>
                            <div class="p-3 space-y-1 max-h-80 overflow-y-auto">
                                @foreach($perms as $permission)
                                    <label class="flex items-center py-1.5 px-2 rounded cursor-pointer">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}
                                               class="group-checkbox-{{ $group }} h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-mono">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 col-span-full text-center py-8">
                            {{ __('file.no_permissions_found') }}
                        </p>
                    @endforelse
                </div>

                @error('permissions')
                    <p class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-8">
            <a href="{{ route('roles.index') }}"
               class="text-sm text-gray-600 dark:text-gray-400">
                ← {{ __('file.back_to_roles') }}
            </a>

            <div class="flex gap-4">
                <a href="{{ route('roles.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                    {{ __('file.cancel') }}
                </a>
                <button type="submit"
                        class="px-8 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg">
                    {{ __('file.update') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAll() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

function toggleGroup(group) {
    const checkboxes = document.querySelectorAll(`.group-checkbox-${CSS.escape(group)}`);
    const allInGroupChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allInGroupChecked);
    
    // Update the group toggle checkbox state
    event.target.checked = !allInGroupChecked;
}
</script>
@endsection