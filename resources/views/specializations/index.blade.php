{{-- resources/views/specializations/index.blade.php --}}
@extends('layouts.app')
@section('title', __('file.specializations'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.specializations') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_specialization_records') }}</p>
        </div>
        <a href="{{ route('specializations.create') }}"
           class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="sm:inline">{{ __('file.add_specialization') }}</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-5 sm:mb-6">
        <form method="GET" id="search-form" class="flex flex-col gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('file.search_placeholder') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('file.search') }}</span>
                </button>
                <a href="{{ route('specializations.index') }}"
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    {{ __('file.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="sm:hidden text-sm text-gray-600 dark:text-gray-400 mb-3">
        {{ __('file.showing_results', ['from' => $specializations->firstItem(), 'to' => $specializations->lastItem(), 'total' => $specializations->total()]) }}
    </div>

    <div class="sm:hidden mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
            <x-sort-link field="department_id" :sort="$sort" :direction="$direction">{{ __('file.department') }}</x-sort-link>
            <x-sort-link field="doctors_count" :sort="$sort" :direction="$direction">{{ __('file.doctors') }}</x-sort-link>
        </div>
    </div>

    <div class="space-y-4 sm:hidden">
        @forelse($specializations as $spec)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $spec->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $spec->department->name }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-4">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.doctors') }}</div>
                        <div>{{ $spec->doctors_count }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.description') }}</div>
                        <div class="truncate text-xs">{{ Str::limit($spec->description, 50) }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button onclick="openProfileDrawer({{ $spec->toJson() }})"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('specializations.destroy', $spec) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('{{ __('file.confirm_delete_specialization') }}')"
                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_specializations_found') }}</p>
            </div>
        @endforelse
    </div>

    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('file.description') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="doctors_count" :sort="$sort" :direction="$direction">{{ __('file.doctors') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="department_id" :sort="$sort" :direction="$direction">{{ __('file.department') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('file.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($specializations as $spec)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $spec->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ Str::limit($spec->description, 80) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $spec->doctors_count }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $spec->department->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button onclick="openProfileDrawer({{ $spec->toJson() }})"
                                            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                                            title="{{ __('file.view_profile') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('specializations.destroy', $spec) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('{{ __('file.confirm_delete_specialization') }}')"
                                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                                title="{{ __('file.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_specializations_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 sm:hidden">
        {{ $specializations->appends(request()->query())->links() }}
    </div>

    <div class="hidden sm:block mt-6">
        {{ $specializations->appends(request()->query())->links() }}
    </div>
</div>

<div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="drawer-overlay"
         class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"
         onclick="closeProfileDrawer()"></div>

    <div id="drawer-panel"
         class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                transform transition-transform duration-300 ease-out
                translate-y-full md:translate-y-0 md:doctors-x-full
                h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
        
        <div class="md:hidden flex justify-center pt-4 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-name"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.specialization_details') }}</p>
            </div>
            <button onclick="closeProfileDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
            <div class="space-y-5">
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('file.department') }}</h4>
                    <div class="text-gray-900 dark:text-white" id="drawer-department"></div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('file.doctors') }}</h4>
                    <div class="text-gray-900 dark:text-white" id="drawer-doctors"></div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('file.description') }}</h4>
                    <div class="text-gray-900 dark:text-white" id="drawer-description"></div>
                </div>
            </div>
        </div>

        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                {{ __('file.close') }}
            </button>
        </div>
    </div>
</div>

<style>
    html, body { overscroll-behavior-y: contain; }
</style>

<script>
    const drawer = document.getElementById('profile-drawer');
    const panel = document.getElementById('drawer-panel');
    const drawerOverlay = document.getElementById('drawer-overlay');
    let bodyScrollPosition = 0;

    function openProfileDrawer(spec) {
        document.getElementById('drawer-name').textContent = spec.name;
        document.getElementById('drawer-department').textContent = spec.department.name;
        document.getElementById('drawer-doctors').textContent = spec.doctors_count;
        document.getElementById('drawer-description').textContent = spec.description || '—';

        bodyScrollPosition = window.pageYOffset;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${bodyScrollPosition}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        drawer.classList.remove('hidden');
        drawerOverlay.classList.remove('opacity-0');
        drawerOverlay.classList.add('opacity-100');
        panel.classList.remove('translate-y-full', 'md:translate-x-full');
    }

    function closeProfileDrawer() {
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';
        window.scrollTo(0, bodyScrollPosition);

        drawerOverlay.classList.remove('opacity-100');
        drawerOverlay.classList.add('opacity-0');
        panel.classList.add(window.innerWidth < 640 ? 'translate-y-full' : 'md:translate-x-full');
        setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !drawer.classList.contains('hidden')) closeProfileDrawer();
    });

    let startY = 0;
    panel.addEventListener('touchstart', e => {
        if (window.innerWidth >= 640) return;
        startY = e.touches[0].clientY;
    }, {passive: true});
    panel.addEventListener('touchmove', e => {
        if (window.innerWidth >= 640) return;
        const delta = e.touches[0].clientY - startY;
        if (delta > 0) panel.style.transform = `translateY(${delta}px)`;
    }, {passive: true});
    panel.addEventListener('touchend', e => {
        if (window.innerWidth >= 640) return;
        const delta = e.changedTouches[0].clientY - startY;
        if (delta > 100) closeProfileDrawer();
        else panel.style.transform = '';
    });
</script>
@endsection