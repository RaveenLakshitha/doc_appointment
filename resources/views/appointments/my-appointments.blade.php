@extends('layouts.app')

@section('title', __('file.my_appointments') ?? 'My Appointments')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.my_appointments') ?? 'My Appointments' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.my_appointments_desc') ?? 'Your assigned patient appointments.' }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                @can('appointments.create')
                    @php
                        $doctor = auth()->user()->doctor ?? \App\Models\Doctor::where('email', auth()->user()->email)->active()->first();
                    @endphp
                    @if ($doctor)
                        <a href="{{ route('appointments.create', ['appointment_type' => \App\Models\Appointment::TYPE_SPECIFIC, 'doctor_id' => $doctor->id, 'lock_doctor' => 1, 'return_to' => 'my_appointments']) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>{{ __('file.create_appointment') ?? 'Create Appointment' }}</span>
                        </a>
                    @endif
                @endcan
                <div class="relative">
                    <button type="button" id="filter-toggle"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>{{ __('file.filters') }}</span>
                        <span id="filter-count"
                            class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

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
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select id="filter-status"
                            class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">{{ __('file.all_statuses') ?? 'All Statuses' }}</option>
                            <option value="{{ \App\Models\Appointment::STATUS_PENDING }}">{{ __('file.pending') }}</option>
                            <option value="{{ \App\Models\Appointment::STATUS_APPROVED }}">{{ __('file.approved') }}
                            </option>
                            <option value="{{ \App\Models\Appointment::STATUS_COMPLETED }}">{{ __('file.completed') }}
                            </option>
                            <option value="{{ \App\Models\Appointment::STATUS_CANCELLED }}">{{ __('file.cancelled') }}
                            </option>
                            <option value="{{ \App\Models\Appointment::STATUS_REJECTED }}">{{ __('file.rejected') }}
                            </option>
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

        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.No') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.patient') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.date_time') }}
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterToggle = document.getElementById('filter-toggle');
            const filterDrawer = document.getElementById('filter-drawer');
            const filterBackdrop = document.getElementById('filter-backdrop');
            const closeDrawer = document.getElementById('close-drawer');
            const filterCount = document.getElementById('filter-count');
            const filterStatus = document.getElementById('filter-status');

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

            filterToggle.addEventListener('click', (e) => { e.stopPropagation(); openDrawer(); });
            closeDrawer.addEventListener('click', closeDrawerFunc);
            filterBackdrop.addEventListener('click', closeDrawerFunc);
            filterDrawer.addEventListener('click', (e) => e.stopPropagation());
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !filterBackdrop.classList.contains('hidden')) closeDrawerFunc();
            });

            function updateFilterCount() {
                const count = [filterStatus.value].filter(Boolean).length;
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
                    url: '{{ route("appointments.datatable") }}',
                    data: function (d) {
                        d.my_appointments = 1;
                        d.status = filterStatus.value;
                    }
                },
                order: [[2, 'desc']],
                columnDefs: [
                    { targets: [0, 1], responsivePriority: 1 },
                    { targets: -1, orderable: false, searchable: false, responsivePriority: 2 },
                    { targets: 3, searchable: false, className: 'text-center' }
                ],
                columns: [
                    {
                        data: 'appointment_number',
                        defaultContent: '-',
                        render: function (data) {
                            return `<div class="font-medium text-indigo-600 dark:text-indigo-400">${data || '-'}</div>`;
                        }
                    },
                    {
                        data: 'patient_name',
                        render: function (data, type, row) {
                            const queue = row.queue_info && row.queue_info !== '-'
                                ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">${row.queue_info}</div>` : '';
                            return `<div class="font-medium text-gray-900 dark:text-white">${data || '-'}</div>${queue}`;
                        }
                    },
                    {
                        data: 'scheduled_datetime',
                        className: 'whitespace-nowrap',
                        render: function (data) {
                            if (!data || data === 'Not set') {
                                return `<span class="text-gray-400 dark:text-gray-500 italic text-sm">{{ __('file.not_set') ?? 'Not set' }}</span>`;
                            }
                            return `<span class="text-sm text-gray-700 dark:text-gray-300">${data}</span>`;
                        }
                    },
                    { data: 'status_badge', className: 'text-center' },
                    {
                        data: null,
                        className: 'text-right whitespace-nowrap',
                        render: function (data, type, row) {
                            return `
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="${row.show_url}"
                                           class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                           title="{{ __('file.view') }}">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                `;
                        }
                    }
                ],
                layout: {
                    topStart: {
                        buttons: [
                            { extend: 'pageLength', className: 'btn btn-sm btn-light' },
                            {
                                extend: 'collection',
                                text: "{{ __('file.Export') ?? 'Export' }}",
                                className: 'btn btn-sm btn-dark',
                                buttons: [
                                    { extend: 'copy', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'excel', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'csv', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'pdf', exportOptions: { columns: ':not(.no-export)' } },
                                    { extend: 'print', exportOptions: { columns: ':not(.no-export)' } }
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
                    searchPlaceholder: "{{ __('file.search_appointments') ?? 'Search appointments...' }}",
                    lengthMenu: "{{ __('file.show_entries') }}",
                    info: "{{ __('file.showing_entries_appointments') }}",
                    infoEmpty: "{{ __('file.no_appointments_found') }}",
                    emptyTable: "{{ __('file.no_appointments_found') }}",
                    processing: "{{ __('file.processing') }}..."
                },
                autoWidth: false
            });

            document.getElementById('apply-filters').addEventListener('click', () => {
                table.draw();
                updateFilterCount();
                closeDrawerFunc();
            });

            document.getElementById('clear-filters').addEventListener('click', () => {
                filterStatus.value = '';
                table.draw();
                updateFilterCount();
            });

            filterStatus.addEventListener('change', updateFilterCount);
            updateFilterCount();
        });
    </script>
@endpush