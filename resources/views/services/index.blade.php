{{-- resources/views/services/index.blade.php --}}

@extends('layouts.app')

@section('title', __('Services'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 py-4 sm:py-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('Services') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage hospital services and procedures') }}
            </p>
        </div>

        <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <a href="{{ route('services.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add Service') }}
            </a>
        </div>
    </div>

    <div id="bulk-delete-form" class="hidden mb-6">
        <form method="POST" action="{{ route('services.bulkDelete') }}" class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulk-ids">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('service(s) selected') }}
            </span>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                {{ __('Delete Selected') }}
            </button>
        </form>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div id="table-loading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-10 flex items-center justify-center hidden">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Loading...') }}</span>
            </div>
        </div>
        
        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Service Name') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Department') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Type') }}
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Duration') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Price') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Rows will be populated by DataTables -->
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function showLoading() { document.getElementById('table-loading').classList.remove('hidden'); }
    function hideLoading() { document.getElementById('table-loading').classList.add('hidden'); }

    const table = $('#docapp-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route('services.datatable') }}',
            beforeSend: showLoading,
            complete: hideLoading,
            error: hideLoading
        },
        order: [[1, 'asc']], // Default order by Service Name
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Disable ordering on checkbox and actions
            { searchable: false, targets: [0, 3, 4, 5, 6] }
        ],
        columns: [
            { 
                data: 'id', 
                render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, 
                orderable: false, 
                searchable: false, 
                className: 'text-center' 
            },
            { data: 'name', render: data => data || '-' },
            { data: 'department_name', render: data => data || '-' },
            { data: 'type' }, // Already contains badge HTML from controller
            { data: 'duration_minutes', className: 'text-center', render: data => data ? `${data} min` : '-' },
            { data: 'price', className: 'text-right font-medium', render: data => data ? `$${parseFloat(data).toFixed(2)}` : '-' },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                className: 'text-right whitespace-nowrap', 
                render: (data, type, row) => `
                    <div class="flex items-center justify-end gap-1">
                        <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="${row.delete_url}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to deactivate this service?')"
                                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="Deactivate">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>`
            }
        ],
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'pageLength',
                        className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm'
                    }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        pageLength: 15,
        lengthMenu: [10, 25, 50, 100],
        language: {
            search: "",
            searchPlaceholder: "{{ __('Search services...') }}",
            lengthMenu: "{{ __('Show _MENU_ entries') }}",
            info: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
            emptyTable: "{{ __('No services found') }}",
            processing: "{{ __('Processing...') }}"
        }
    });

    // Bulk delete functionality
    $('#select-all').on('change', function () {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkDelete();
    });

    $(document).on('change', '.row-checkbox', updateBulkDelete);

    function updateBulkDelete() {
        const count = $('.row-checkbox:checked').length;
        $('#bulk-delete-form').toggleClass('hidden', count === 0);
        $('#selected-count').text(count);
        $('#bulk-ids').val($('.row-checkbox:checked').map(function() { return this.value; }).get().join(','));
    }

    $('#bulk-delete-form form').on('submit', function (e) {
        e.preventDefault();
        if (confirm('{{ __("Are you sure you want to deactivate the selected services?") }}')) {
            this.submit();
        }
    });
});
</script>
@endpush
@endsection