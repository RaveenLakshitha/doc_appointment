@extends('layouts.app')

@section('title', __('file.patients'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.patients') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_patient_records') }}
            </p>
        </div>

        <a href="{{ route('patients.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 text-white rounded transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('file.add_patient') }}
        </a>
    </div>

    <form method="POST" action="{{ route('patients.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('file.patient_selected') }}
            </span>
            <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_selected') }}')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ __('file.delete') }}
            </button>
        </div>
    </form>

    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <table id="patients-table" class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left w-12">
                        <input type="checkbox" id="select-all"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-gray-900">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">MRN</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('file.name') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Age</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Last Visit</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('file.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('file.actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#patients-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('patients.datatable') }}',
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ],
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-gray-900">`
            },
            { data: 'medical_record_number', name: 'medical_record_number' },
            { data: 'full_name', name: 'first_name', render: data => data || '' },
            {
                data: 'age',
                orderable: true,
                className: 'text-center font-medium',
                render: data => data > 0 ? data : '-'
            },
            {
                data: 'gender',
                name: 'gender_sort',
                orderable: true,
                searchable: false
            },
            {
                data: 'last_visit',
                orderable: true,
                render: data => data ? data : '<span class="text-gray-400 text-xs">Never</span>'
            },
            { data: 'status_html', name: 'is_active' },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-right whitespace-nowrap',
                render: (data, type, row) => `
                    <div class="flex items-center justify-end gap-1">
                        <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="${row.delete_url}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('{{ __("file.confirm_delete_patient") }}')"
                                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>`
            }
        ],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            emptyTable: "{{ __('file.no_patients_found') }}",
            zeroRecords: "{{ __('file.no_patients_found') }}"
        }
    });

    $('#select-all').on('change', function () {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkDelete();
    });

    $(document).on('change', '.row-checkbox', updateBulkDelete);

    function updateBulkDelete() {
        const checked = $('.row-checkbox:checked').length;
        $('#bulk-delete-form').toggleClass('hidden', checked === 0);
        $('#bulk-ids').val($('.row-checkbox:checked').map(function() { return this.value; }).get().join(','));
        $('#selected-count').text(checked);
    }

    $('#bulk-delete-form').on('submit', function (e) {
        e.preventDefault();
        if (confirm('{{ __("file.confirm_delete_selected") }}')) {
            $.post(this.action, $(this).serialize(), () => {
                table.ajax.reload(null, false);
                updateBulkDelete();
            });
        }
    });
});
</script>
@endpush
@endsection