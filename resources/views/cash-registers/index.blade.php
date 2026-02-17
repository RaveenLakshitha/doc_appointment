@extends('layouts.app')

@section('title', 'Cash Registers')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                Cash Registers
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage cash register sessions, openings, closings & reconciliations
            </p>
        </div>

        @if (!auth()->user()->cashRegisters()->whereNull('closed_at')->exists())
            <button onclick="openCreateDrawer()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Open New Register
            </button>
        @else
            <span class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-lg text-sm font-medium">
                You have an open register — close it first
            </span>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">Opened At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">Opening Balance</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">Expected Closing</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">Actual Closing</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- View / Close Drawer -->
<div id="register-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeDrawer()"></div>

    <div id="drawer-panel"
         class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-lg bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none overflow-hidden">
        <div class="md:hidden flex justify-center pt-4 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-title">Register Details</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Session #<span id="drawer-id"></span></p>
            </div>
            <button onclick="closeDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6 text-sm space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">User</p>
                    <p class="font-medium" id="drawer-user"></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">Opened At</p>
                    <p class="font-medium" id="drawer-opened"></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">Opening Balance</p>
                    <p class="font-medium text-green-600 dark:text-green-400" id="drawer-opening-balance"></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">Status</p>
                    <div id="drawer-status-badge"></div>
                </div>
            </div>

            <div id="reconciliation-section" class="hidden">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Close & Reconcile Register</h4>
                <form id="close-form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="id" id="close-id">

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Counted Cash in Drawer</label>
                        <input type="number" name="actual_closing_balance" id="actual-closing-balance" step="0.01" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes / Discrepancy Reason</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="pt-2">
                        <p class="text-xs text-gray-500">Expected: <span id="expected-value" class="font-medium"></span></p>
                        <p class="text-xs mt-1" id="difference-preview"></p>
                    </div>
                </form>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Cash Movements</h4>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-2 text-left">Time</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-body" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3">
            <button onclick="closeDrawer()" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Close
            </button>
            <button id="btn-close-register" form="close-form" type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">
                Close & Reconcile
            </button>
        </div>
    </div>
</div>

<!-- Open New Register Drawer -->
<div id="open-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeOpenDrawer()"></div>

    <div class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[70vh] md:h-full rounded-t-3xl md:rounded-none overflow-hidden">
        <div class="md:hidden flex justify-center pt-4 pb-2">
            <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Open New Cash Register</h3>
            <button onclick="closeOpenDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-8">
            <form id="open-form" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opening Cash Balance
                    </label>
                    <input type="number" name="opening_balance" step="0.01" min="0" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 text-lg">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Count and enter the physical cash currently in the drawer.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes (optional)
                    </label>
                    <textarea name="notes" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700"></textarea>
                </div>
            </form>
        </div>

        <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-4">
            <button onclick="closeOpenDrawer()" class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">
                Cancel
            </button>
            <button id="btn-open-register" form="open-form"  type="submit"
                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                Open Register
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#docapp-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('cash-registers.datatable') }}',
        order: [[2, 'desc']],
        columns: [
            { data: 'id' },
            { data: 'user_name' },
            { data: 'opened_at_formatted' },
            { data: 'opening_balance_formatted', className: 'text-right font-medium text-green-600 dark:text-green-400' },
            { data: 'expected_closing_formatted', className: 'text-right' },
            { data: 'actual_closing_formatted', className: 'text-right' },
            { data: 'status_html', className: 'text-center' },
            {
                data: null,
                orderable: false,
                className: 'text-right',
                render: (data, type, row) => `
                    <button onclick='openRegisterDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                `
            }
        ],
        language: {
            searchPlaceholder: "Search registers...",
            emptyTable: "No cash registers found",
            processing: "Loading..."
        }
    });

    // ─── View Drawer ────────────────────────────────────────
    window.openRegisterDrawer = function(register) {
        document.getElementById('drawer-id').textContent = register.id;
        document.getElementById('drawer-user').textContent = register.user_name;
        document.getElementById('drawer-opened').textContent = register.opened_at_formatted;
        document.getElementById('drawer-opening-balance').textContent = register.opening_balance_formatted;
        document.getElementById('drawer-status-badge').innerHTML = register.status_html;
        document.getElementById('expected-value').textContent = register.expected_closing_formatted || '—';

        const isOpen = register.status === 'open';
        document.getElementById('reconciliation-section').classList.toggle('hidden', !isOpen);
        document.getElementById('btn-close-register').classList.toggle('hidden', !isOpen);

        if (isOpen) {
            document.getElementById('close-id').value = register.id;
            document.getElementById('actual-closing-balance').value = '';
            document.getElementById('difference-preview').textContent = '';
        }

        const tbody = document.getElementById('transactions-body');
        tbody.innerHTML = '';

        if (register.transactions && register.transactions.length > 0) {
            register.transactions.forEach(t => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2">${t.happened_at}</td>
                    <td class="px-4 py-2">${t.type_formatted}</td>
                    <td class="px-4 py-2 text-right ${t.amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'} font-medium">
                        ${t.amount_formatted}
                    </td>`;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-6 text-gray-500 dark:text-gray-400 italic">No transactions recorded yet</td></tr>';
        }

        document.body.style.overflow = 'hidden';
        document.getElementById('register-drawer').classList.remove('hidden');
    };

    window.closeDrawer = function() {
        document.getElementById('register-drawer').classList.add('hidden');
        document.body.style.overflow = '';
    };

    // Live difference preview
    document.getElementById('actual-closing-balance')?.addEventListener('input', function(e) {
        const expectedEl = document.getElementById('expected-value');
        const previewEl  = document.getElementById('difference-preview');

        const expected = parseFloat(expectedEl.textContent.replace(/[^0-9.-]+/g,"")) || 0;
        const actual   = parseFloat(e.target.value) || 0;
        const diff     = actual - expected;

        previewEl.textContent = `Difference: ${diff.toFixed(2)}`;
        previewEl.className = diff === 0
            ? 'text-green-600 dark:text-green-400 font-medium'
            : (diff > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400');
    });

    // Close form submit (AJAX)
    document.getElementById('close-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-close-register');
        btn.disabled = true;
        btn.innerHTML = 'Closing...';

        try {
            const formData = new FormData(this);
            const id = formData.get('id');
            const url = '{{ route("cash-registers.close", ":id") }}'.replace(':id', id);

            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Failed to close register');

            table.draw(false);
            closeDrawer();
            alert(data.message || 'Register closed successfully!');
        } catch (err) {
            alert('Error: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Close & Reconcile';
        }
    });

    // ─── Open Drawer ────────────────────────────────────────
    window.openCreateDrawer = function() {
        document.getElementById('open-drawer').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.querySelector('#open-form input[name="opening_balance"]')?.focus();
    };

    window.closeOpenDrawer = function() {
        document.getElementById('open-drawer').classList.add('hidden');
        document.body.style.overflow = '';
    };

    document.getElementById('open-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('btn-open-register');
    if (!btn) return;

    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Opening...';

    try {
        const formData = new FormData(this);
        const response = await fetch('{{ route("cash-registers.open") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message || 'Failed');

        table.draw(false);
        closeOpenDrawer();
        alert(data.message || 'Success!');
    } catch (err) {
        alert('Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
});

    // ESC key to close drawers
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (!document.getElementById('register-drawer').classList.contains('hidden')) {
                closeDrawer();
            }
            if (!document.getElementById('open-drawer').classList.contains('hidden')) {
                closeOpenDrawer();
            }
        }
    });
});
</script>
@endpush
@endsection