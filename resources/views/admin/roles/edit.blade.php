@extends('layouts.app')

@section('title', __('file.edit_role') . ': ' . ucfirst($role->name))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">

    <!-- Header -->
    <div class="mb-5">
        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-1">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-600 dark:text-gray-400">
                {{ __('file.roles') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>{{ __('file.edit_role') }}</span>
        </div>

        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                {{ strtoupper(substr($role->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('file.edit_role_name', ['name' => ucfirst($role->name)]) }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('file.update_role_details') }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <!-- Role Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ __('file.role_information') }}
                </h2>
            </div>
            <div class="p-5">
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">
                    {{ __('file.role_name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $role->name) }}"
                       required
                       {{ $role->name === 'admin' ? 'disabled' : '' }}
                       class="w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 disabled:bg-gray-100 dark:disabled:bg-gray-900 disabled:cursor-not-allowed"
                       placeholder="{{ __('file.role_name_placeholder') }}">

                @if($role->name === 'admin')
                    <p class="mt-2 text-sm text-amber-600 dark:text-amber-400 flex items-center">
                        Warning: {{ __('file.admin_protected') }}
                    </p>
                @endif

                @error('name')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ __('file.permissions') }}
                </h2>
                <button type="button"
                        onclick="toggleAllPermissions()"
                        class="text-sm font-medium text-green-600">
                    {{ __('file.select_all') }}
                </button>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($permissions as $group => $items)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
                            <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900/30">
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 capitalize">
                                    {{ Str::title($group) }}
                                </h3>
                            </div>
                            <div class="p-3 space-y-1">
                                @foreach($items as $permission)
                                    <label class="flex items-center py-1.5 px-3 cursor-pointer">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}
                                               class="permission-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-3">
            <a href="{{ route('admin.roles.index') }}"
               class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('file.back_to_roles') }}
            </a>
            <div class="flex space-x-3">
                <a href="{{ route('admin.roles.index') }}"
                   class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md">
                    {{ __('file.cancel') }}
                </a>
                <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-md">
                    {{ __('file.update_role') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection