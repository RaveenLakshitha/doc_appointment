@extends('layouts.app')

@section('title', __('file.create_new_role'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">

    <!-- Breadcrumb + Header -->
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('roles.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200 transition">
                {{ __('file.roles') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium">{{ __('file.create_role') }}</span>
        </nav>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('file.create_new_role') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                {{ __('file.set_up_role') }}
            </p>
        </div>
    </div>

    <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
        @csrf

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
                    <label for="role_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.role_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="role_name"
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="{{ __('file.role_name_placeholder') }}"
                           required autofocus>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('file.role_name_hint') }}</p>
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
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ __('file.optional') }})</span>
                </h2>
                <div class="flex items-center gap-3">
                    <button type="button"
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                {{ __('file.perm_all') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($permissionsMatrix as $resource => $actions)
                            @php
                                $groupKey   = 'file.perm_group_' . str_replace(['-', '.'], '_', $resource);
                                $groupLabel = __($groupKey) !== $groupKey
                                    ? __($groupKey)
                                    : ucwords(str_replace(['-', '_'], ' ', $resource));
                                $oldPerms = old('permissions', []);
                            @endphp
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $groupLabel }}
                                    </span>
                                </td>
                                @foreach($allowedActions as $action)
                                    <td class="px-4 py-3 text-center">
                                        @if(isset($actions[$action]))
                                            <label class="inline-flex items-center justify-center cursor-pointer">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $actions[$action]->name }}"
                                                       data-action="{{ $action }}"
                                                       data-resource="{{ $resource }}"
                                                       {{ in_array($actions[$action]->name, $oldPerms) ? 'checked' : '' }}
                                                       class="perm-cb h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer transition">
                                            </label>
                                        @else
                                            <span class="inline-block w-5 h-5 rounded bg-gray-100 dark:bg-gray-700/50 border border-dashed border-gray-200 dark:border-gray-600"
                                                  title="{{ __('file.perm_not_available') }}"></span>
                                        @endif
                                    </td>
                                @endforeach
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
                    {{ __('file.create_role') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAllMatrix(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}

function toggleRow(resource) {
    const cbs = document.querySelectorAll(`.perm-cb[data-resource="${resource}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}

function toggleColumn(action) {
    const cbs = document.querySelectorAll(`.perm-cb[data-action="${action}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection
