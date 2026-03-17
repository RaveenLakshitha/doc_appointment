@extends('layouts.app')

@section('title', __('file.attendance'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.attendance') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_attendance_records') }}
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

                <a href="{{ route('attendances.bulk-mark') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('file.record_attendance') }}</span>
                    <span class="sm:hidden">Add</span>
                </a>
            </div>
        </div>

        <!-- Filter Drawer -->
        <div id="filter-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="filter-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                                            bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                                            md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.filters') }}</h3>
                <button type="button" id="close-drawer"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(85vh-140px)] md:max-h-[calc(100vh-140px)]">
                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee') }}</label>
                        <select id="filter-employee"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_employees') }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }}</label>
                        <select id="filter-department"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_departments') }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_range') }}</label>
                        <input type="date" id="filter-date-from"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow mb-3">
                        <input type="date" id="filter-date-to"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select id="filter-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_statuses') }}</option>
                            <option value="present">{{ __('file.present') }}</option>
                            <option value="absent">{{ __('file.absent') }}</option>
                            <option value="late">{{ __('file.late') }}</option>
                            <option value="half_day">{{ __('file.half_day') }}</option>
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
        <div id="bulk-delete-form" class="hidden mb-6 px-4">
            <form id="bulk-delete-form-el" action="{{ route('attendances.bulkDelete') }}" method="POST"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.attendance_selected') }}
                </span>
                <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <!-- Table Container -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.date') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.employee') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.department') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.clock_in') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.clock_out') }}
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

    <!-- View Drawer -->
    <div id="show-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="show-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeShowDrawer()"></div>
        <div id="show-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="show-title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.attendance_details') }}</p>
                </div>
                <button onclick="closeShowDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <div class="space-y-6">
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.employee_info') }}
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                                <div class="text-gray-900 dark:text-white font-medium" id="show-employee"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }}</label>
                                <div class="text-gray-900 dark:text-white" id="show-department"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.attendance_details') }}
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date') }}</label>
                                <div class="text-gray-900 dark:text-white" id="show-date"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                                <div id="show-status"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clock_in') }}</label>
                                <div class="text-gray-900 dark:text-white" id="show-clock-in"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clock_out') }}</label>
                                <div class="text-gray-900 dark:text-white" id="show-clock-out"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.notes') }}
                        </h4>
                        <div class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700 min-h-[80px]"
                            id="show-notes"></div>
                    </div>
                </div>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeShowDrawer()"
                    class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Drawer -->
    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="edit-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeEditDrawer()"></div>
        <div id="edit-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.edit_attendance') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="edit-subtitle"></p>
                </div>
                <button onclick="closeEditDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="edit-form" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="employee_id" id="edit-employee-id">

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date') }}</label>
                        <input type="date" name="date" id="edit-date" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="status" id="edit-status" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="present">{{ __('file.present') }}</option>
                            <option value="absent">{{ __('file.absent') }}</option>
                            <option value="late">{{ __('file.late') }}</option>
                            <option value="half_day">{{ __('file.half_day') }}</option>
                            <option value="leave">{{ __('file.leave') }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clock_in') }}</label>
                            <input type="time" name="clock_in" id="edit-clock-in"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clock_out') }}</label>
                            <input type="time" name="clock_out" id="edit-clock-out"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.notes') }}</label>
                        <textarea name="notes" id="edit-notes" rows="3"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()"
                        class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="edit-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
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

                const filterEmployee = document.getElementById('filter-employee');
                const filterDepartment = document.getElementById('filter-department');
                const filterDateFrom = document.getElementById('filter-date-from');
                const filterDateTo = document.getElementById('filter-date-to');
                const filterStatus = document.getElementById('filter-status');

                function openDrawer() {
                    filterBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        filterBackdrop.classList.add('opacity-100');
                        filterBackdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) filterDrawer.classList.remove('md:translate-x-full');
                        else filterDrawer.classList.remove('translate-y-full');
                    }, 10);
                }

                function closeDrawerFunc() {
                    filterBackdrop.classList.remove('opacity-100');
                    filterBackdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) filterDrawer.classList.add('md:translate-x-full');
                    else filterDrawer.classList.add('translate-y-full');
                    setTimeout(() => {
                        filterBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                filterToggle.addEventListener('click', (e) => { e.stopPropagation(); openDrawer(); });
                closeDrawer.addEventListener('click', closeDrawerFunc);
                filterBackdrop.addEventListener('click', closeDrawerFunc);
                filterDrawer.addEventListener('click', (e) => e.stopPropagation());
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !filterBackdrop.classList.contains('hidden')) closeDrawerFunc(); });

                function updateFilterCount() {
                    const count = [filterEmployee.value, filterDepartment.value, filterDateFrom.value, filterDateTo.value, filterStatus.value].filter(Boolean).length;
                    if (count > 0) {
                        filterCount.textContent = count;
                        filterCount.classList.remove('hidden');
                    } else {
                        filterCount.classList.add('hidden');
                    }
                }

                $.get('{{ route("attendances.filters") }}', { column: 'employee' }, data => {
                    $.each(data, (id, name) => $('#filter-employee').append(`<option value="${id}">${name}</option>`));
                });

                $.get('{{ route("attendances.filters") }}', { column: 'department' }, data => {
                    $.each(data, (id, name) => $('#filter-department').append(`<option value="${id}">${name}</option>`));
                });

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("attendances.datatable") }}',
                        data: function (d) {
                            d.employee = filterEmployee.value;
                            d.department = filterDepartment.value;
                            d.date_from = filterDateFrom.value;
                            d.date_to = filterDateTo.value;
                            d.status = filterStatus.value;
                        }
                    },
                    order: [[1, 'desc']],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center',
                            orderable: false
                        },
                        { data: 'date', className: 'text-left' },
                        { data: 'employee_name' },
                        { data: 'department_name', className: 'text-left' },
                        { data: 'clock_in', className: 'text-center' },
                        { data: 'clock_out', className: 'text-center' },
                        { data: 'status_html', className: 'text-center' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => {
                                const rowJson = JSON.stringify(row).replace(/'/g, "\\'");
                                return `
                                                    <div class="flex items-center justify-end gap-1">
                                                        <button onclick='openShowDrawer(${rowJson})' class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        </button>
                                                        <button onclick='openEditDrawer(${rowJson})' class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ __('file.edit') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </button>
                                                        <button type="button" onclick="confirmDelete('${row.delete_url}')" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                `;
                            }
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: ['pageLength', {
                                extend: 'collection', text: 'Export', buttons: [
                                    { extend: 'copy', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'excel', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'csv', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'pdf', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'print', exportOptions: { columns: ':not(.no-export)' } }
                                ]
                            }]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_attendance') }}",
                        emptyTable: "{{ __('file.no_attendance_records') }}"
                    }
                });

                document.getElementById('apply-filters').addEventListener('click', () => { table.draw(); updateFilterCount(); closeDrawerFunc(); });
                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterEmployee.value = ''; filterDepartment.value = ''; filterDateFrom.value = ''; filterDateTo.value = ''; filterStatus.value = '';
                    table.draw(); updateFilterCount();
                });

                [filterEmployee, filterDepartment, filterDateFrom, filterDateTo, filterStatus].forEach(el => el.addEventListener('change', updateFilterCount));

                // Drawer Logic
                const showDrawer = document.getElementById('show-drawer');
                const editDrawer = document.getElementById('edit-drawer');
                let bodyScrollPos = 0;

                function lockScroll() {
                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';
                }

                function unlockScroll() {
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                }

                window.openShowDrawer = function (row) {
                    document.getElementById('show-title').textContent = row.date;
                    document.getElementById('show-employee').textContent = row.employee_name;
                    document.getElementById('show-department').textContent = row.department_name;
                    document.getElementById('show-date').textContent = row.date;
                    document.getElementById('show-status').innerHTML = row.status_html;
                    document.getElementById('show-clock-in').textContent = row.clock_in || '—';
                    document.getElementById('show-clock-out').textContent = row.clock_out || '—';
                    document.getElementById('show-notes').textContent = row.notes || '—';

                    lockScroll();
                    showDrawer.classList.remove('hidden');
                };

                window.closeShowDrawer = function () {
                    showDrawer.classList.add('hidden');
                    unlockScroll();
                };

                window.openEditDrawer = function (row) {
                    document.getElementById('edit-id').value = row.id;
                    document.getElementById('edit-employee-id').value = row.employee_id;
                    document.getElementById('edit-subtitle').textContent = row.employee_name + ' — ' + row.date;

                    // Format date for input type=date (YYYY-MM-DD)
                    // Row.date is like "07 Mar 2026", we might need the raw date from the server
                    // Let's assume the controller returns a 'raw_date' for the edit form if needed,
                    // but usually we can parse it or get it from the row data.
                    // Improving controller to return 'raw_date' would be safer.
                    // For now, let's try to extract it or expect it.
                    document.getElementById('edit-date').value = row.raw_date || '';
                    document.getElementById('edit-status').value = row.status;
                    document.getElementById('edit-clock-in').value = (row.clock_in && row.clock_in !== '-') ? row.clock_in : '';
                    document.getElementById('edit-clock-out').value = (row.clock_out && row.clock_out !== '-') ? row.clock_out : '';
                    document.getElementById('edit-notes').value = (row.notes && row.notes !== '-') ? row.notes : '';

                    lockScroll();
                    editDrawer.classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    editDrawer.classList.add('hidden');
                    unlockScroll();
                };

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = document.getElementById('edit-id').value;

                    fetch(`{{ route('attendances.update', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                            } else {
                                alert(data.message || 'Update failed');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            showNotification('{{ __("file.error") }}: ' + err.message, 'error');
                        });
                });

                // Bulk delete logic
                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;
                    document.getElementById('bulk-delete-form').classList.toggle('hidden', count === 0);
                    document.getElementById('selected-count').textContent = count;
                    
                    const container = document.getElementById('bulk-ids-container');
                    container.innerHTML = '';
                    checked.each(function () {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = this.value;
                        container.appendChild(input);
                    });
                }

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete") }}')) return;
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: (res) => {
                            table.draw(false);
                            if (res.success) {
                                if (typeof showNotification === 'function') 
                                    showNotification('{{ __("file.success") }}', res.message, 'success');
                                else
                                    alert(res.message);
                            } else {
                                if (typeof showNotification === 'function') 
                                    showNotification('{{ __("file.error") }}', res.message, 'error');
                                else
                                    alert(res.message || '{{ __("file.failed_to_delete") }}');
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || '{{ __("file.error_deleting") }}';
                            if (typeof showNotification === 'function') 
                                showNotification('{{ __("file.error") }}', msg, 'error');
                            else 
                                alert(msg);
                        }
                    });
                };

                $('#bulk-delete-form-el').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_bulk_delete") }}')) return;

                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize() + '&_method=DELETE',
                        success: (res) => {
                            table.draw(false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            
                            if (res.success) {
                                if (typeof showNotification === 'function') 
                                    showNotification('{{ __("file.success") }}', res.message, 'success');
                                else
                                    alert(res.message);
                            } else {
                                if (typeof showNotification === 'function') 
                                    showNotification('{{ __("file.error") }}', res.message, 'error');
                                else
                                    alert(res.message || '{{ __("file.failed_to_delete") }}');
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || '{{ __("file.error_deleting") }}';
                            if (typeof showNotification === 'function') 
                                showNotification('{{ __("file.error") }}', msg, 'error');
                            else 
                                alert(msg);
                        }
                    });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        closeShowDrawer();
                        closeEditDrawer();
                    }
                });

                updateFilterCount();
            });
        </script>
    @endpush
@endsection