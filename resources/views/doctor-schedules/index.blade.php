@extends('layouts.app')

@section('title', __('file.doctor_schedules'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.doctor_schedules') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_doctor_schedules') }}
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
                        {{ __('file.Filters') }}
                        <span id="filter-count"
                            class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <a href="{{ route('doctor-schedules.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('file.add_schedule') }}</span>
                    <span class="sm:hidden">{{ __('file.add') }}</span>
                </a>
            </div>
        </div>

        <div id="filter-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="filter-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.doctor') }}
                        </label>
                        <select id="filter-doctor"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_doctors') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.room') }}
                        </label>
                        <select id="filter-room"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_rooms') }}</option>
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

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('doctor-schedules.bulkDelete') }}"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                @csrf
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.schedule_selected') }}
                </span>
                <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

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
                                {{ __('file.doctor') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.room') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.days') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.time') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
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
                const filterDoctor = document.getElementById('filter-doctor');
                const filterRoom = document.getElementById('filter-room');

                function openDrawer() {
                    filterBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        filterBackdrop.classList.add('opacity-100');
                        filterBackdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) {
                            filterDrawer.classList.remove('md:translate-x-full');
                        } else {
                            filterDrawer.classList.remove('translate-y-full');
                        }
                    }, 10);
                }

                function closeDrawerFunc() {
                    filterBackdrop.classList.remove('opacity-100');
                    filterBackdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) {
                        filterDrawer.classList.add('md:translate-x-full');
                    } else {
                        filterDrawer.classList.add('translate-y-full');
                    }
                    setTimeout(() => {
                        filterBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                filterToggle.addEventListener('click', e => { e.stopPropagation(); openDrawer(); });
                closeDrawer.addEventListener('click', closeDrawerFunc);
                filterBackdrop.addEventListener('click', closeDrawerFunc);
                filterDrawer.addEventListener('click', e => e.stopPropagation());
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && !filterBackdrop.classList.contains('hidden')) closeDrawerFunc();
                });

                function updateFilterCount() {
                    const count = [filterDoctor.value, filterRoom.value].filter(Boolean).length;
                    filterCount.textContent = count;
                    filterCount.classList.toggle('hidden', count === 0);
                }

                $.get('{{ route("doctor-schedules.filters") }}', { column: 'doctor' }, data => {
                    $.each(data, (id, name) => $('#filter-doctor').append(`<option value="${id}">${name}</option>`));
                });
                $.get('{{ route("doctor-schedules.filters") }}', { column: 'room' }, data => {
                    $.each(data, (id, name) => $('#filter-room').append(`<option value="${id}">${name}</option>`));
                });

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("doctor-schedules.datatable") }}',
                        data: d => {
                            d.doctor = filterDoctor.value;
                            d.room = filterRoom.value;
                        }
                    },
                    order: [[1, 'asc']], // Doctor column (index 1) as default sort
                    columnDefs: [
                        { targets: 0, orderable: false, className: 'dtr-control', responsivePriority: 1 },
                        { targets: 1, responsivePriority: 2 },           // Doctor
                        { targets: -1, orderable: false, searchable: false, responsivePriority: 1 } // Actions
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center',
                            orderable: false
                        },
                        {
                            data: 'doctor',
                            render: data => `<div class="font-medium text-gray-900 dark:text-white">${data}</div>`
                        },
                        { data: 'room', render: data => data },
                        { data: 'days', render: data => data || '-' },
                        { data: 'time', render: data => data },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                    <div class="flex items-center justify-end gap-1">
                                                        <a href="${row.edit_url}" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ __('file.edit') }}">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                        <form method="POST" action="${row.delete_url}" class="inline">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('{{ __("file.confirm_delete_schedule") }}')" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                `
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection', text: "{{ __('file.Export') }}", className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium', buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'excel', text: 'Excel', filename: 'Rooms_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'csv', text: 'CSV', filename: 'Rooms_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'Rooms_{{ date("Y-m-d") }}', title: 'Doctor Schedules List', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'print', text: "{{ __('file.print') }}", exportOptions: { columns: [0, 1, 2, 3, 4] } }
                                    ]
                                }
                            ]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_schedules') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries_schedules') }}",
                        emptyTable: "{{ __('file.no_schedules_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                document.getElementById('apply-filters').addEventListener('click', () => {
                    table.draw();
                    updateFilterCount();
                    closeDrawerFunc();
                });

                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterDoctor.value = '';
                    filterRoom.value = '';
                    table.draw();
                    updateFilterCount();
                });

                [filterDoctor, filterRoom].forEach(el => el.addEventListener('change', updateFilterCount));

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const count = $('.row-checkbox:checked').length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    $('#bulk-ids').val($('.row-checkbox:checked').map(function () { return this.value; }).get().join(','));
                }

                $('#bulk-delete-form form').on('submit', function (e) {
                    e.preventDefault();
                    if (confirm('{{ __("file.confirm_delete_selected_schedules") }}')) {
                        $.ajax({
                            url: this.action,
                            method: 'POST',
                            data: $(this).serialize(),
                            success: (data) => {
                                table.draw(false);
                                $('.row-checkbox').prop('checked', false);
                                $('#select-all').prop('checked', false);
                                if (typeof updateBulkDelete === 'function') updateBulkDelete();
                                if (typeof showNotification === 'function') showNotification('Success', data.message || '{{ __("file.bulk_deleted") }}', 'success');
                            },
                            error: (xhr) => {
                                let msg = '{{ __("file.error_deleting") }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                if (typeof showNotification === 'function') showNotification(msg, 'error');
                                else alert(msg);
                            }
                        });
                    }
                });

                updateFilterCount();
            });
        </script>
    @endpush
@endsection