@extends('layouts.app')

@section('title', __('file.leave_types'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.leave_types') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_leave_types') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <div class="relative">
                    <button type="button" id="filter-toggle"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('file.filters') }}
                        <span id="filter-count"
                            class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                @can('leave-types.create')
                    <button onclick="openCreateDrawer()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">{{ __('file.add_leave_type') }}</span>
                        <span class="sm:hidden">Add</span>
                    </button>
                @endcan
            </div>
        </div>

        <div id="filter-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="filter-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.filters') }}</h3>
                <button type="button" id="close-drawer"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto max-h-[calc(85vh-140px)] md:max-h-[calc(100vh-140px)]">
                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select id="filter-active"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_statuses') }}</option>
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div
                class="bottom-0 left-0 right-0 flex gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="clear-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ __('file.clear') }}
                </button>
                <button type="button" id="apply-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    {{ __('file.apply') }}
                </button>
            </div>
        </div>

        <!-- Bulk Delete Form -->
        <div id="bulk-delete-form" class="hidden mb-6">
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count-form">0</span> {{ __('file.items_selected') }}
                </span>
                <button type="button" id="bulk-delete-btn"
                    class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </div>
        </div>

        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left no-export all">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.name') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.code') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.days_allowed') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.paid') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.requires_approval') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop no-export">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>


    </div>

    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeCreateDrawer()"></div>
        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_leave_type') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_leave_type') }}</p>
                </div>
                <button onclick="closeCreateDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-6">
                <form id="create-form" class="space-y-6">
                    @csrf
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.code') }}</label>
                        <input type="text" name="code"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.days_allowed') }}</label>
                        <input type="number" name="days_allowed" min="0" step="0.5" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_paid" id="create-is-paid" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                        <label for="create-is-paid"
                            class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.paid') }}</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="requires_approval" id="create-requires-approval" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                        <label for="create-requires-approval"
                            class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_approval') }}</label>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" rows="4"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="active"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()"
                        class="flex-1 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="create-form"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                        {{ __('file.create_leave_type') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeEditDrawer()"></div>
        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-title">
                        {{ __('file.edit_leave_type') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.update_leave_type_details') }}</p>
                </div>
                <button onclick="closeEditDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-6">
                <form id="edit-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.code') }}</label>
                        <input type="text" name="code" id="edit-code"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.days_allowed') }}</label>
                        <input type="number" name="days_allowed" id="edit-days-allowed" min="0" step="0.5" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_paid" id="edit-is-paid" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                        <label for="edit-is-paid"
                            class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.paid') }}</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="requires_approval" id="edit-requires-approval" value="1"
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                        <label for="edit-requires-approval"
                            class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_approval') }}</label>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="4"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="active" id="edit-active"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()"
                        class="flex-1 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="edit-form"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                        {{ __('file.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterToggle = document.getElementById('filter-toggle');
                const filterDrawer = document.getElementById('filter-drawer');
                const filterBackdrop = document.getElementById('filter-backdrop');
                const closeDrawer = document.getElementById('close-drawer');
                const filterCount = document.getElementById('filter-count');
                const filterActive = document.getElementById('filter-active');

                function openFilterDrawer() {
                    filterBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        filterBackdrop.classList.add('opacity-100');
                        filterBackdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) filterDrawer.classList.remove('md:translate-x-full');
                        else filterDrawer.classList.remove('translate-y-full');
                    }, 10);
                }

                function closeFilterDrawer() {
                    filterBackdrop.classList.remove('opacity-100');
                    filterBackdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) filterDrawer.classList.add('md:translate-x-full');
                    else filterDrawer.classList.add('translate-y-full');
                    setTimeout(() => {
                        filterBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                filterToggle.addEventListener('click', (e) => { e.stopPropagation(); openFilterDrawer(); });
                closeDrawer.addEventListener('click', closeFilterDrawer);
                filterBackdrop.addEventListener('click', closeFilterDrawer);
                filterDrawer.addEventListener('click', (e) => e.stopPropagation());
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !filterBackdrop.classList.contains('hidden')) closeFilterDrawer(); });

                function updateFilterCount() {
                    const count = [filterActive.value].filter(Boolean).length;
                    if (count > 0) {
                        filterCount.textContent = count;
                        filterCount.classList.remove('hidden');
                    } else {
                        filterCount.classList.add('hidden');
                    }
                }

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("leave-types.datatable") }}',
                        data: function (d) {
                            d.active = filterActive.value;
                        }
                    },
                    order: [[1, 'asc']],
                    columns: [
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'px-4 py-3',
                            render: function (data) {
                                return `<input type="checkbox" value="${data}" class="row-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">`;
                            }
                        },
                        { data: 'name', className: 'text-left' },
                        { data: 'code', className: 'text-left' },
                        { data: 'days_allowed', className: 'text-left' },
                        { data: 'paid_html', className: 'text-center' },
                        {
                            data: null,
                            className: 'text-center',
                            render: (data) => data.requires_approval
                                ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Yes</span>'
                                : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">No</span>'
                        },
                        { data: 'active_html', className: 'text-center' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            width: '100px',
                            render: (data, type, row) => `
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button 
                                                            onclick='openEditDrawer(${JSON.stringify(row)})'
                                                            class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                            title="{{ __('file.edit') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>

                                                        <button 
                                                            onclick='deleteLeaveType("${row.delete_url}", "${row.name.replace(/"/g, "\\\"").replace(/'/g, "\\'")}")'
                                                            class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                                            title="{{ __('file.delete') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                `
                        }
                    ],
                    layout: {
                        topStart: { buttons: ['pageLength', { extend: 'collection', text: '{{ __('file.Export') }}', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] }] },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_leave_types') }}",
                        emptyTable: "{{ __('file.no_leave_types_found') }}"
                    }
                });

                document.getElementById('apply-filters').addEventListener('click', () => { table.draw(); updateFilterCount(); closeFilterDrawer(); });
                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterActive.value = '';
                    table.draw(); updateFilterCount();
                });

                filterActive.addEventListener('change', updateFilterCount);
                updateFilterCount();

                // Bulk Delete Logic
                const selectAll = document.getElementById('select-all');
                const bulkDeleteForm = document.getElementById('bulk-delete-form');
                const selectedCountForm = document.getElementById('selected-count-form');
                const bulkIdsInput = document.getElementById('bulk-ids');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

                function updateBulkDelete() {
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const totalSelected = checkedBoxes.length;

                    if (totalSelected > 0) {
                        selectedCountForm.textContent = totalSelected;
                        bulkDeleteForm.classList.remove('hidden');
                        bulkIdsInput.value = Array.from(checkedBoxes).map(cb => cb.value).join(',');
                    } else {
                        bulkDeleteForm.classList.add('hidden');
                        selectAll.checked = false;
                        bulkIdsInput.value = '';
                    }
                }

                selectAll.addEventListener('change', function () {
                    const isChecked = this.checked;
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                    });
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', function () {
                    const totalCheckboxes = $('.row-checkbox').length;
                    const totalChecked = $('.row-checkbox:checked').length;
                    selectAll.checked = totalCheckboxes === totalChecked;
                    updateBulkDelete();
                });

                bulkDeleteBtn.addEventListener('click', async function () {
                    const selectedIds = bulkIdsInput.value ? bulkIdsInput.value.split(',') : [];

                    if (selectedIds.length === 0) return;

                    if (!confirm(`Are you sure you want to delete ${selectedIds.length} items?`)) return;

                    try {
                        const response = await fetch('{{ route("leave-types.bulkDelete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ ids: selectedIds })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showNotification(data.message || 'Items deleted successfully', 'success');
                            selectAll.checked = false;
                            table.draw(false);
                            updateBulkDelete();
                        } else {
                            showNotification(data.message || 'Failed to delete items', 'error');
                        }
                    } catch (err) {
                        showNotification('Error performing bulk delete', 'error');
                    }
                });

                table.on('draw', () => {
                    selectAll.checked = false;
                    updateBulkDelete();
                });

                let bodyScrollPos = 0;

                function lockBody() {
                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';
                }

                function unlockBody() {
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                }

                window.openCreateDrawer = function () {
                    document.getElementById('create-form').reset();
                    document.querySelector('#create-form select[name="active"]').value = "1";
                    lockBody();
                    document.getElementById('create-drawer').classList.remove('hidden');
                };

                window.closeCreateDrawer = function () {
                    document.getElementById('create-drawer').classList.add('hidden');
                    unlockBody();
                };

                window.openEditDrawer = function (row) {
                    document.getElementById('edit-id').value = row.id;
                    document.getElementById('edit-name').value = row.name || '';
                    document.getElementById('edit-code').value = row.code || '';
                    document.getElementById('edit-days-allowed').value = row.days_allowed || '';
                    document.getElementById('edit-is-paid').checked = row.is_paid;
                    document.getElementById('edit-requires-approval').checked = row.requires_approval;
                    document.getElementById('edit-description').value = row.description || '';
                    document.getElementById('edit-active').value = row.is_active ? "1" : "0";

                    lockBody();
                    document.getElementById('edit-drawer').classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    document.getElementById('edit-drawer').classList.add('hidden');
                    unlockBody();
                };

                document.getElementById('create-form').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    try {
                        const response = await fetch('{{ route("leave-types.store") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            table.draw(false);
                            closeCreateDrawer();
                        } else {
                            showNotification(data.message || 'Failed to create leave type', 'error');
                        }
                    } catch (err) {
                        showNotification('Error creating leave type', 'error');
                    }
                });

                document.getElementById('edit-form').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    try {
                        const response = await fetch(`{{ route("leave-types.update", ":id") }}`.replace(':id', id), {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            table.draw(false);
                            closeEditDrawer();
                        } else {
                            showNotification(data.message || 'Failed to update leave type', 'error');
                        }
                    } catch (err) {
                        showNotification('Error updating leave type', 'error');
                    }
                });

                window.deleteLeaveType = async function (url, name) {
                    if (!confirm(`Are you sure you want to delete "${name}"?`)) return;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams({ _method: 'DELETE' })
                        });

                        const data = await response.json();

                        if (data.success) {
                            table.draw(false);
                            showNotification('Leave type deleted successfully', 'success');
                        } else {
                            showNotification(data.message || 'Failed to delete leave type', 'error');
                        }
                    } catch (err) {
                        showNotification('Error deleting leave type', 'error');
                    }
                };

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!document.getElementById('create-drawer').classList.contains('hidden')) closeCreateDrawer();
                        if (!document.getElementById('edit-drawer').classList.contains('hidden')) closeEditDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection