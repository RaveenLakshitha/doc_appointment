@extends('layouts.app')

@section('title', __('file.edit_role') . ': ' . __('file.role_' . $role->name, [], null, $role->name))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">

    @php
        $roleLabelKey = 'file.role_' . $role->name;
        $roleLabel = __($roleLabelKey) !== $roleLabelKey
            ? __($roleLabelKey)
            : ucfirst(str_replace('_', ' ', $role->name));
    @endphp

    <!-- Breadcrumb + Header -->
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('roles.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200 transition">
                {{ __('file.roles') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium">{{ __('file.edit_role') }}</span>
        </nav>

        <div class="flex items-center gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('file.edit_role_name', ['name' => $roleLabel]) }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    @if($isSystemRole)
                        {{ __('file.system_role_permissions_only') }}
                    @else
                        {{ __('file.update_role_details') }}
                    @endif
                </p>
            </div>
            @if($isSystemRole)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-700">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ __('file.system_role') }}
                </span>
            @endif
        </div>
    </div>

    @if($isSystemRole)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-amber-800 dark:text-amber-300">
                {{ __('file.system_role_locked_notice') }}
            </p>
        </div>
    @endif

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
                    @if($isSystemRole)
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $roleLabel }}</span>
                            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $role->name }}</span>
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded">{{ __('file.locked') }}</span>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('file.system_role_name_cannot_change') }}</p>
                    @else
                        <input type="text"
                               name="name"
                               id="role_name"
                               value="{{ old('name', $role->name) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="{{ __('file.role_name_placeholder') }}"
                               required>
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>
        </div>

        <!-- Permissions Matrix Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('file.permissions') }}
                </h2>
                <div class="flex items-center gap-3">
                    <button type="button" id="btn-select-all"
                            onclick="toggleAllMatrix(true)"
                            class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                        {{ __('file.select_all') }}
                    </button>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <button type="button"
                            onclick="toggleAllMatrix(false)"
                            class="text-xs font-medium text-red-500 dark:text-red-400 hover:underline px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        {{ __('file.deselect_all') }}
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-48">
                                {{ __('file.perm_resource') }}
                            </th>
                            @foreach($allowedActions as $action)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <div class="flex flex-col items-center gap-1">
                                        <span>{{ __('file.perm_action_' . $action) }}</span>
                                        {{-- Column select-all toggle --}}
                                        <button type="button"
                                                onclick="toggleColumn('{{ $action }}')"
                                                title="{{ __('file.toggle_column') }}"
                                                class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition mt-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </th>
                            @endforeach
                            {{-- Row select-all column header --}}
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                {{ __('file.perm_all') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($permissionsMatrix as $resource => $actions)
                            @php
                                $groupKey = 'file.perm_group_' . str_replace(['-', '.'], '_', $resource);
                                $groupLabel = __($groupKey) !== $groupKey
                                    ? __($groupKey)
                                    : ucwords(str_replace(['-', '_'], ' ', $resource));
                            @endphp
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                {{-- Resource Name --}}
                                <td class="px-5 py-3">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $groupLabel }}
                                    </span>
                                </td>
                                {{-- Action Checkboxes --}}
                                @foreach($allowedActions as $action)
                                    <td class="px-4 py-3 text-center">
                                        @if(isset($actions[$action]))
                                            <label class="inline-flex items-center justify-center cursor-pointer">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $actions[$action]->name }}"
                                                       data-action="{{ $action }}"
                                                       data-resource="{{ $resource }}"
                                                       {{ $role->hasPermissionTo($actions[$action]) ? 'checked' : '' }}
                                                       class="perm-cb h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer transition">
                                            </label>
                                        @else
                                            <span class="inline-block w-5 h-5 rounded bg-gray-100 dark:bg-gray-700/50 border border-dashed border-gray-200 dark:border-gray-600" title="{{ __('file.perm_not_available') }}"></span>
                                        @endif
                                    </td>
                                @endforeach
                                {{-- Row toggle --}}
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            onclick="toggleRow('{{ $resource }}')"
                                            class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                            title="{{ __('file.toggle_row') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('file.no_permissions_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @error('permissions')
                <div class="px-6 pb-4">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                </div>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-8">
            <a href="{{ route('roles.index') }}"
               class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                ← {{ __('file.back_to_roles') }}
            </a>
            <div class="flex gap-4">
                <a href="{{ route('roles.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('file.cancel') }}
                </a>
                <button type="submit"
                        class="px-8 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    {{ __('file.update') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Toggle all checkboxes on/off
function toggleAllMatrix(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}

// Toggle all checkboxes in a single resource row
function toggleRow(resource) {
    const cbs = document.querySelectorAll(`.perm-cb[data-resource="${resource}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}

// Toggle all checkboxes in an action column
function toggleColumn(action) {
    const cbs = document.querySelectorAll(`.perm-cb[data-action="${action}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection