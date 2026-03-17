@extends('layouts.app')

@section('title', __('file.notification_settings'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">

    <!-- Breadcrumb + Header -->
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <span class="hover:text-gray-700 dark:hover:text-gray-200 transition">
                {{ __('file.administration') }}
            </span>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-900 dark:text-white">{{ __('file.notification_settings') }}</span>
        </nav>

        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('file.notification_settings') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Configure which roles receive automated notifications for system events.
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('admin.notification-settings.update') }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <!-- Settings Matrix Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{ __('file.notification_settings') }}
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-64">
                                {{ __('file.event') }}
                            </th>
                            @foreach($roles as $role)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                    <div class="flex flex-col items-center gap-1">
                                        @php
                                            $roleLabelKey = 'file.role_' . $role->name;
                                            $roleLabel = __($roleLabelKey) !== $roleLabelKey
                                                ? __($roleLabelKey)
                                                : ucfirst(str_replace('_', ' ', $role->name));
                                        @endphp
                                        <span>{{ $roleLabel }}</span>
                                        <button type="button"
                                                onclick="toggleColumn('{{ $role->name }}')"
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
                        @foreach($events as $eventKey => $eventLabel)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                <td class="px-5 py-3">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $eventLabel }}
                                    </span>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $isEnabled = isset($settings[$eventKey]) && $settings[$eventKey]->where('role_name', $role->name)->first()?->is_enabled;
                                        @endphp
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="settings[{{ $eventKey }}][{{ $role->name }}]"
                                                   value="1"
                                                   data-role="{{ $role->name }}"
                                                   data-event="{{ $eventKey }}"
                                                   {{ $isEnabled ? 'checked' : '' }}
                                                   class="setting-cb h-5 w-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer transition">
                                        </label>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            onclick="toggleRow('{{ $eventKey }}')"
                                            class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                            title="{{ __('file.toggle_row') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end pt-4 gap-4 mt-8">
            <button type="reset"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                {{ __('file.cancel') }}
            </button>
            <button type="submit"
                    class="px-8 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-200 dark:shadow-none transition-all active:scale-[0.98]">
                {{ __('file.save_settings') }}
            </button>
        </div>
    </form>
</div>

<script>
// Toggle all checkboxes on/off
function toggleAllMatrix(state) {
    document.querySelectorAll('.setting-cb').forEach(cb => cb.checked = state);
}

// Toggle all checkboxes in a single event row
function toggleRow(eventKey) {
    const cbs = document.querySelectorAll(`.setting-cb[data-event="${eventKey}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}

// Toggle all checkboxes in an role column
function toggleColumn(roleName) {
    const cbs = document.querySelectorAll(`.setting-cb[data-role="${roleName}"]`);
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection