@extends('layouts.app')

@section('title', __('file.appointments'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.appointments') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_appointment_records') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <a href="{{ route('appointments.create', ['return_to' => 'all_appointments']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('file.schedule_appointment') }}</span>
                    <span class="sm:hidden">{{ __('file.add') }}</span>
                </a>
            </div>
        </div>
        
        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('appointments.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.appointment_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition shadow-sm">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>


        <!-- Inline Status Filters -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.filter_by_status') }}:</span>
                <div class="flex flex-wrap items-center gap-4">
                    @php
                        $statuses = [
                            \App\Models\Appointment::STATUS_PENDING => 'status_pending',
                            \App\Models\Appointment::STATUS_APPROVED => 'status_approved',
                            \App\Models\Appointment::STATUS_RUNNING => 'status_running',
                            \App\Models\Appointment::STATUS_COMPLETED => 'status_completed',
                            \App\Models\Appointment::STATUS_PAID => 'status_paid',
                            \App\Models\Appointment::STATUS_REJECTED => 'status_rejected',
                            \App\Models\Appointment::STATUS_CANCELLED => 'status_cancelled',
                        ];
                    @endphp

                    @foreach($statuses as $value => $label)
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="status[]" value="{{ $value }}"
                                @if($value === \App\Models\Appointment::STATUS_PENDING && auth()->user()->hasAnyRole(['admin', 'primary_care_provider'])) checked @endif
                                class="status-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 transition-colors">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
                                {{ __('file.' . $label) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left w-12 all">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.No') }}
                            </th>

                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.patient') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.doctor') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.date') }}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("appointments.datatable") }}',
                        data: function (d) {
                            @if(isset($isDoctorPanel) && $isDoctorPanel)
                                d.doctor_id = '{{ auth()->user()->doctor->id ?? "" }}';
                            @endif
                            
                            // Get all checked statuses
                            const selectedStatuses = [];
                            $('.status-checkbox:checked').each(function() {
                                selectedStatuses.push($(this).val());
                            });
                            d.status = selectedStatuses;
                        }
                    },
                    order: [[4, 'desc']], // Shifted due to new column
                    columnDefs: [
                        { targets: [0, 1, 2, 3], responsivePriority: 1 },
                        { targets: -1, orderable: false, searchable: false, responsivePriority: 2 },
                        { targets: 5, searchable: false, className: 'text-center' },
                        { targets: 0, orderable: false, searchable: false }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'appointment_number',
                            defaultContent: '—',
                            render: function (data) {
                                return `<div class="font-medium text-indigo-600 dark:text-indigo-400">${data || '—'}</div>`;
                            }
                        },
                        {
                            data: 'patient_name',
                            render: data => `<div class="font-medium text-gray-900 dark:text-white">${data || '-'}</div>`
                        },
                        {
                            data: 'doctor_name',
                            render: data => `<div class="font-medium text-gray-900 dark:text-white">${data || '(Any Doctor)'}</div>`
                        },
                        {
                            data: 'scheduled_datetime',
                            className: 'whitespace-nowrap',
                            render: function (data, type, row) {
                                if (data === 'Not set') {
                                    return `<span class="text-gray-500 dark:text-gray-400 italic">${data}</span>`;
                                }
                                return data;
                            }
                        },
                        { data: 'status_badge', className: 'text-center' },
                        {
                            data: null,
                            className: 'text-right whitespace-nowrap',
                            render: function (data, type, row) {
                                return `
                                                        <div class="flex items-center justify-end gap-1">
                                                            <a href="${row.show_url}" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view') }}">
                                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                            </a>
                                                            ${row.edit_url ? `
                                                            <a href="${row.edit_url}" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ __('file.edit') }}">
                                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                            </a>
                                                            ` : ''}
                                                            ${row.delete_url ? `
                                                                <button type="button" onclick="confirmDelete('${row.delete_url}')"
                                                                        class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                </button>
                                                            ` : ''}
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
                                    text: '<span class="hidden sm:inline">Export</span><span class="sm:hidden">⬇</span>',
                                    className: 'btn btn-sm btn-dark',
                                    text: "{{ __('file.Export') }}",
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
                        searchPlaceholder: "{{ __('file.search_appointments') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries_appointments') }}",
                        infoEmpty: "{{ __('file.no_appointments_found') }}",
                        emptyTable: "{{ __('file.no_appointments_found') }}",
                        processing: "{{ __('file.processing') }}..."
                    },
                    autoWidth: false
                });

                // Re-draw table when a checkbox is toggled
                $('.status-checkbox').on('change', function() {
                    table.draw();
                });

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    
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

                $('#bulk-delete-form-el').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_delete_selected") }}')) return;
                    
                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: (response) => {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            if (typeof showNotification === 'function') showNotification('Success', response.message || 'Appointments deleted successfully.', 'success');
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_appointment") }}')) return;
                    
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            table.draw(false);
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                };
            });

        </script>
    @endpush
@endsection