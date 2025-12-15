@extends('layouts.app')

@section('title', __('file.specializations'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 py-4 sm:py-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.specializations') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_specialization_records') }}
            </p>
        </div>
        <a href="{{ route('specializations.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('file.add_specialization') }}
        </a>
    </div>

    <!-- Bulk Delete Bar -->
    <div id="bulk-delete-form" class="hidden mb-6">
        <form method="POST" action="{{ route('specializations.bulkDelete') }}" class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulk-ids">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('file.specialization_selected') }}
            </span>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                {{ __('file.delete_selected') }}
            </button>
        </form>
    </div>

    <!-- Table Card with Loading Overlay -->
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading Overlay -->
        <div id="table-loading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-10 flex items-center justify-center hidden">
            <div class="flex flex-col items-center gap-3">
                <div class="flex space-x-2">
                </div>
            </div>
        </div>

        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.description') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.doctors') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.department') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
        </table>
    </div>
</div>

<!-- Profile Drawer (Kept exactly as you had it) -->
<div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeProfileDrawer()"></div>

    <div id="drawer-panel"
         class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col transform transition-all duration-300 h-[90vh] md:h-full rounded-t-3xl md:rounded-none translate-y-full md:translate-x-full">
        
        <div class="md:hidden flex justify-center pt-4 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-name"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.specialization_details') }}</p>
            </div>
            <button onclick="closeProfileDrawer()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-5 text-sm space-y-6">
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('file.department') }}</h4>
                <div class="text-gray-900 dark:text-white font-medium" id="drawer-department"></div>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('file.doctors') }}</h4>
                <div class="text-gray-900 dark:text-white font-medium" id="drawer-doctors"></div>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('file.description') }}</h4>
                <div class="text-gray-900 dark:text-white whitespace-pre-wrap" id="drawer-description"></div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeProfileDrawer()" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('file.close') }}
            </button>
        </div>
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
            url: '{{ route('specializations.datatable') }}',
            beforeSend: showLoading,
            complete: hideLoading,
            error: hideLoading
        },
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 3, 5] }
        ],
        columns: [
            { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
            { data: 'name', render: data => data || '-' },
            { data: 'description', render: data => data ? `<div class="max-w-md truncate" title="${data}">${data}</div>` : '<span class="text-gray-400">—</span>' },
            { data: 'doctors_count', className: 'text-center font-medium' },
            { data: 'department_name', render: data => data || '-' },
            { data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                <div class="flex items-center justify-end gap-1">
                    <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="${row.delete_url}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_specialization') }}')"
                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
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
                    },
                    {
                        extend: 'collection',
                        text: "{{ __('file.Export') }}",
                        className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                        buttons: [
                            { extend: 'copy', text: "{{ __('file.copy') }}" },
                            { extend: 'excel', text: 'Excel', filename: 'Specializations_{{ date("Y-m-d") }}' },
                            { extend: 'csv', text: 'CSV', filename: 'Specializations_{{ date("Y-m-d") }}' },
                            { extend: 'pdf', text: 'PDF', filename: 'Specializations_{{ date("Y-m-d") }}', title: 'Specialization List' },
                            { extend: 'print', text: "{{ __('file.print') }}" }
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
            searchPlaceholder: "{{ __('file.search_specializations') }}",
            lengthMenu: "{{ __('file.show_entries') }}",
            info: "{{ __('file.showing_entries') }}",
            emptyTable: "{{ __('file.no_specializations_found') }}",
            processing: "{{ __('file.processing') }}"
        }
    });

    // Bulk Delete Logic
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
        if (confirm('{{ __("file.confirm_delete_selected") }}')) {
            $.ajax({
                url: this.action,
                method: 'POST',
                data: $(this).serialize(),
                success: () => {
                    table.draw(false);
                    updateBulkDelete();
                }
            });
        }
    });
});

// Drawer Functions (unchanged)
const drawer = document.getElementById('profile-drawer');
const panel = document.getElementById('drawer-panel');
const overlay = document.getElementById('drawer-overlay');
let bodyScrollPos = 0;

function openProfileDrawer(spec) {
    document.getElementById('drawer-name').textContent = spec.name;
    document.getElementById('drawer-department').textContent = spec.department_name || '—';
    document.getElementById('drawer-doctors').textContent = spec.doctors_count;
    document.getElementById('drawer-description').textContent = spec.description || '—';

    bodyScrollPos = window.pageYOffset;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${bodyScrollPos}px`;
    document.body.style.width = '100%';

    drawer.classList.remove('hidden');
    setTimeout(() => {
        overlay.classList.add('opacity-100');
        panel.classList.remove('translate-y-full', 'md:translate-x-full');
    }, 10);
}

function closeProfileDrawer() {
    overlay.classList.remove('opacity-100');
    panel.classList.add('translate-y-full', 'md:translate-x-full');
    setTimeout(() => {
        drawer.classList.add('hidden');
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, bodyScrollPos);
    }, 300);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !drawer.classList.contains('hidden')) closeProfileDrawer();
});
</script>
@endpush
@endsection