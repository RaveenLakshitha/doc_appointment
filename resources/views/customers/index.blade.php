@extends('layouts.app')

@section('title', __('file.customers'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.customers') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_customer_records') }}
                </p>
            </div>
            @can('customers.create')
            <button onclick="openCreateDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_customer') }}
            </button>
            @endcan
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('customers.bulkDelete') }}"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.customers_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right pr-6 no-export" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.email') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.phone') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider no-export">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Drawer -->
    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="create-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeCreateDrawer()"></div>
        <div id="create-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.create_new_customer') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.add_customer_details') }}</p>
                </div>
                <button onclick="closeCreateDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm">
                <form id="create-form" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.first_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow" placeholder="First Name">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.last_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow" placeholder="Last Name">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.email') }}</label>
                        <input type="email" name="email" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow" placeholder="email@example.com">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.phone') }}</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow" placeholder="+123456789">
                    </div>

                    <div class="grid grid-cols-1">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.customer_status') }}</label>
                            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="active">{{ __('file.customer_status_active') }}</option>
                                <option value="inactive">{{ __('file.customer_status_inactive') }}</option>
                                <option value="lead">{{ __('file.customer_status_lead') }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.notes') }}</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()" class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">{{ __('file.cancel') }}</button>
                    <button type="submit" form="create-form" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ __('file.create') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Drawer -->
    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="edit-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeEditDrawer()"></div>
        <div id="edit-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_customer') }}</p>
                </div>
                <button onclick="closeEditDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm">
                <form id="edit-form" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.first_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="edit-first-name" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.last_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" id="edit-last-name" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.email') }}</label>
                        <input type="email" name="email" id="edit-email" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.phone') }}</label>
                        <input type="text" name="phone" id="edit-phone" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                    </div>

                    <div class="grid grid-cols-1">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.customer_status') }}</label>
                            <select name="status" id="edit-status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="active">{{ __('file.customer_status_active') }}</option>
                                <option value="inactive">{{ __('file.customer_status_inactive') }}</option>
                                <option value="lead">{{ __('file.customer_status_lead') }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1 block">{{ __('file.notes') }}</label>
                        <textarea name="notes" id="edit-notes" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()" class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">{{ __('file.cancel') }}</button>
                    <button type="submit" form="edit-form" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ __('file.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openCreateDrawer() {
                document.getElementById('create-drawer').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeCreateDrawer() {
                document.getElementById('create-drawer').classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('customers.datatable') }}',
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center'
                        },
                        { data: 'full_name' },
                        { data: 'email', render: data => data || '-' },
                        { data: 'phone', render: data => data || '-' },
                        {
                            data: 'status',
                            render: data => {
                                const map = {
                                    active: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                    inactive: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                    lead: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                };
                                const textMap = {
                                    active: '{{ __("file.customer_status_active") }}',
                                    inactive: '{{ __("file.customer_status_inactive") }}',
                                    lead: '{{ __("file.customer_status_lead") }}'
                                };
                                const label = textMap[data] ? textMap[data].toUpperCase() : data.toUpperCase();
                                return `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium ${map[data] || ''}">${label}</span>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-right',
                            render: (data, type, row) => `
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" onclick="deleteCustomer('${row.delete_url}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>`
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium' },
                                { extend: 'collection', text: "{{ __('file.Export') }}", className: 'bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] }
                            ]
                        },
                        topEnd: 'search'
                    },
                    language: {
                        info: "{{ __('file.showing_entries') }}",
                        buttons: {
                            pageLength: {
                                _: "{{ __('file.show_d_rows') }}",
                                '-1': "{{ __('file.show_all_rows') }}"
                            }
                        },
                        search: "",
                        searchPlaceholder: "{{ __('file.search_customers') }}",
                        emptyTable: "{{ __('file.no_customers_found') }}"
                    }
                });

                // Checkbox logic
                $('#select-all').on('click change', function () {
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

                // Create form
                document.getElementById('create-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetch('{{ route("customers.store") }}', {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { table.draw(false); closeCreateDrawer(); this.reset(); showNotification('Success', data.message, 'success'); }
                        else { showNotification('Error', data.message, 'error'); }
                    });
                });

                // Edit logic
                window.openEditDrawer = function (customer) {
                    document.getElementById('edit-id').value = customer.id;
                    document.getElementById('edit-drawer-name').textContent = customer.full_name;
                    document.getElementById('edit-first-name').value = customer.first_name;
                    document.getElementById('edit-last-name').value = customer.last_name;
                    document.getElementById('edit-email').value = customer.email || '';
                    document.getElementById('edit-phone').value = customer.phone || '';
                    document.getElementById('edit-status').value = customer.status;
                    document.getElementById('edit-notes').value = customer.notes || '';
                    document.getElementById('edit-drawer').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                };
                window.closeEditDrawer = function () { document.getElementById('edit-drawer').classList.add('hidden'); document.body.style.overflow = ''; };

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetch(`{{ url('customers') }}/${document.getElementById('edit-id').value}`, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { table.draw(false); closeEditDrawer(); showNotification('Success', data.message, 'success'); }
                        else { showNotification('Error', data.message, 'error'); }
                    });
                });

                window.deleteCustomer = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_customer") }}')) return;
                    fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => { if (data.success) { table.draw(false); showNotification('Success', data.message, 'success'); } });
                };
            });
        </script>
    @endpush
@endsection
