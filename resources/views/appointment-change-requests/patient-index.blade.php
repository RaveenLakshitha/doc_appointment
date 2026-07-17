@extends('layouts.app')

@section('title', __('file.my_change_requests') ?? 'My Change Requests')

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.my_change_requests') ?? 'My Change Requests' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.my_change_requests_desc') ?? 'Submit and track your reschedule or cancellation requests.' }}
            </p>
        </div>
        <button type="button" id="open-new-request-btn"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('file.new_request') ?? 'New Request' }}
        </button>
    </div>

    {{-- Info Banner --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 flex gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800 dark:text-blue-200">
            <strong>{{ __('file.note') ?? 'Note' }}:</strong>
            {{ __('file.change_request_info') ?? 'You can only submit requests for approved or assigned appointments. Requests are reviewed by our staff and you will be notified of the decision.' }}
        </div>
    </div>

    {{-- Table --}}
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="patient-requests-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap" style="width:100%">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.appointment') ?? 'Appointment' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.appt_date') ?? 'Appt. Date' }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.type') ?? 'Type' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.requested_date') ?? 'Requested Date' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.reason') ?? 'Reason' }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.status') ?? 'Status' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.submitted_on') ?? 'Submitted' }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider no-export">{{ __('file.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- New Request Modal --}}
<div id="new-request-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-opacity-75" id="request-backdrop"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl w-full">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.submit_change_request') ?? 'Submit Change Request' }}</h3>
                <button type="button" id="close-request-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="change-request-form" class="px-6 py-5 space-y-5">
                @csrf
                {{-- Appointment Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ __('file.select_appointment') ?? 'Select Appointment' }} <span class="text-red-500">*</span>
                    </label>
                    <select id="req-appointment-id" name="appointment_id"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('file.loading') ?? 'Loading...' }}</option>
                    </select>
                    <p class="text-red-500 text-xs mt-1 hidden" id="err-appointment"></p>
                </div>

                {{-- Request Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.request_type') ?? 'Request Type' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="request-type-card cursor-pointer flex items-center gap-3 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                            <input type="radio" name="request_type" value="reschedule" class="text-indigo-600">
                            <div>
                                <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ __('file.reschedule') ?? 'Reschedule' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.change_date_time') ?? 'Change date/time' }}</div>
                            </div>
                        </label>
                        <label class="request-type-card cursor-pointer flex items-center gap-3 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-red-400 dark:hover:border-red-500 transition has-[:checked]:border-red-500 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-900/20">
                            <input type="radio" name="request_type" value="cancel" class="text-red-600">
                            <div>
                                <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ __('file.cancel_appointment') ?? 'Cancel' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.cancel_appointment_desc') ?? 'Cancel this appointment' }}</div>
                            </div>
                        </label>
                    </div>
                    <p class="text-red-500 text-xs mt-1 hidden" id="err-type"></p>
                </div>

                {{-- Reschedule Fields --}}
                <div id="reschedule-fields" class="hidden space-y-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wider">{{ __('file.new_date_time') ?? 'New Date & Time' }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('file.requested_date') ?? 'Requested Date' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="req-date" name="requested_date" min="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                            <p class="text-red-500 text-xs mt-1 hidden" id="err-date"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.preferred_time') ?? 'Preferred Time' }}</label>
                            <select id="req-time" name="requested_time"
                                class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <option value="">{{ __('file.any_time') ?? 'Any time' }}</option>
                                <option value="morning">{{ __('file.morning') ?? 'Morning' }}</option>
                                <option value="evening">{{ __('file.evening') ?? 'Evening' }}</option>
                                <option value="anytime">{{ __('file.anytime') ?? 'Any Time' }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ __('file.reason') ?? 'Reason' }} <span class="text-red-500">*</span>
                    </label>
                    <textarea id="req-reason" name="reason" rows="3"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="{{ __('file.reason_placeholder') ?? 'Please explain why you are requesting this change...' }}" maxlength="1000"></textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-red-500 text-xs hidden" id="err-reason"></p>
                        <span class="text-xs text-gray-400 ml-auto"><span id="reason-count">0</span>/1000</span>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancel-request-btn"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition">
                        {{ __('file.cancel') ?? 'Cancel' }}
                    </button>
                    <button type="submit" id="submit-request-btn"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                        {{ __('file.submit_request') ?? 'Submit Request' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── DataTable ─────────────────────────────────────────────────────────────
    const table = $('#patient-requests-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        ajax: { url: '{{ route("appointment-change-requests.patient.datatable") }}' },
        order: [[6, 'desc']],
        columnDefs: [
            { targets: [0, 2, 5], responsivePriority: 1 },
            { targets: -1, orderable: false, searchable: false },
        ],
        columns: [
            { data: 'appointment', render: d => `<div class="font-medium text-indigo-600 dark:text-indigo-400">${d}</div>` },
            { data: 'appt_date',   render: d => `<span class="text-sm text-gray-600 dark:text-gray-300">${d}</span>` },
            { data: 'type_badge',  className: 'text-center' },
            {
                data: null,
                render: function (d, t, r) {
                    if (r.request_type === 'reschedule') {
                        const timeStr = r.requested_time !== '—' ? r.requested_time : '';
                        const slotStr = r.slot !== '—' ? ` (${r.slot.replace('|', '–')})` : '';
                        const prefStr = r.preferred_time !== '—' ? ` [${r.preferred_time}]` : '';
                        return `<div class="text-sm">
                            <div class="font-medium text-gray-800 dark:text-gray-200">${r.requested_date}</div>
                            <div class="text-xs text-gray-500">${timeStr}${slotStr}${prefStr}</div>
                        </div>`;
                    }
                    return '<span class="text-gray-400 text-xs italic">{{ __("file.not_available") ?? "N/A" }}</span>';
                }
            },
            { data: 'reason', render: d => `<div class="max-w-xs truncate text-sm text-gray-600 dark:text-gray-300" title="${d}">${d}</div>` },
            { data: 'status_badge', className: 'text-center' },
            { data: 'created_at',   render: d => `<span class="text-sm text-gray-500 dark:text-gray-400">${d}</span>` },
            {
                data: null,
                className: 'text-right whitespace-nowrap',
                render: function (d, t, r) {
                    if (r.status === 'pending' && r.withdraw_url) {
                        return `<button type="button" onclick="withdrawRequest('${r.withdraw_url}')"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:text-red-700 border border-red-300 hover:border-red-400 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('file.withdraw') ?? 'Withdraw' }}
                        </button>`;
                    }
                    if (r.admin_notes) {
                        return `<span class="text-xs text-gray-400 italic">${r.admin_notes.substring(0,40)}${r.admin_notes.length>40?'…':''}</span>`;
                    }
                    return '<span class="text-gray-400 text-xs">—</span>';
                }
            }
        ],
        layout: {
            topStart: { buttons: [{ extend: 'pageLength', className: 'btn btn-sm btn-light' }] },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        pageLength: 10,
        language: {
                        info: "{{ __('file.showing_entries') }}",
                        buttons: {
                            pageLength: {
                                _: "{{ __('file.show_d_rows') }}",
                                '-1': "{{ __('file.show_all_rows') }}"
                            }
                        },
            search: "",
            searchPlaceholder: "{{ __('file.search') ?? 'Search...' }}",
            emptyTable: "{{ __('file.no_requests_found') ?? 'No requests found.' }}",
            processing: "{{ __('file.processing') ?? 'Processing' }}..."
        },
        autoWidth: false
    });

    // ── Modal Open/Close ──────────────────────────────────────────────────────
    function openModal() {
        document.getElementById('new-request-modal').classList.remove('hidden');
        loadEligibleAppointments();
    }
    function closeModal() {
        document.getElementById('new-request-modal').classList.add('hidden');
        document.getElementById('change-request-form').reset();
        document.getElementById('reschedule-fields').classList.add('hidden');
        clearErrors();
    }

    document.getElementById('open-new-request-btn').addEventListener('click', openModal);
    document.getElementById('close-request-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-request-btn').addEventListener('click', closeModal);
    document.getElementById('request-backdrop').addEventListener('click', closeModal);

    // ── Load Eligible Appointments ────────────────────────────────────────────
    function loadEligibleAppointments() {
        const sel = document.getElementById('req-appointment-id');
        sel.innerHTML = '<option value="">{{ __("file.loading") ?? "Loading..." }}</option>';
        $.get('{{ route("appointment-change-requests.eligible") }}', function (data) {
            if (data.length === 0) {
                sel.innerHTML = '<option value="">{{ __("file.no_eligible_appointments") ?? "No eligible appointments found." }}</option>';
                return;
            }
            sel.innerHTML = '<option value="">{{ __("file.select_appointment") ?? "— Select Appointment —" }}</option>';
            data.forEach(a => {
                sel.innerHTML += `<option value="${a.id}">${a.number} — ${a.doctor} (${a.date} ${a.time})</option>`;
            });
        }).fail(function () {
            sel.innerHTML = '<option value="">{{ __("file.error_loading") ?? "Error loading appointments." }}</option>';
        });
    }

    // ── Request Type Toggle ───────────────────────────────────────────────────
    document.querySelectorAll('input[name="request_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const rf = document.getElementById('reschedule-fields');
            if (this.value === 'reschedule') {
                rf.classList.remove('hidden');
            } else {
                rf.classList.add('hidden');
                document.getElementById('req-date').value = '';
                document.getElementById('req-time').value = '';
            }
        });
    });

    // ── Character Count ───────────────────────────────────────────────────────
    document.getElementById('req-reason').addEventListener('input', function () {
        document.getElementById('reason-count').textContent = this.value.length;
    });

    // ── Form Submit ───────────────────────────────────────────────────────────
    document.getElementById('change-request-form').addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const appointmentId = document.getElementById('req-appointment-id').value;
        const requestType   = document.querySelector('input[name="request_type"]:checked')?.value;
        const reason        = document.getElementById('req-reason').value.trim();
        const requestedDate = document.getElementById('req-date').value;
        const preferredTime = document.getElementById('req-time').value;

        let valid = true;
        if (!appointmentId) { showError('err-appointment', '{{ __("file.field_required") ?? "Required." }}'); valid = false; }
        if (!requestType)   { showError('err-type', '{{ __("file.field_required") ?? "Required." }}'); valid = false; }
        if (!reason)        { showError('err-reason', '{{ __("file.field_required") ?? "Required." }}'); valid = false; }
        if (requestType === 'reschedule' && !requestedDate) {
            showError('err-date', '{{ __("file.reschedule_date_required") ?? "A date is required for rescheduling." }}');
            valid = false;
        }
        if (!valid) return;

        const btn = document.getElementById('submit-request-btn');
        btn.disabled = true;
        btn.textContent = '{{ __("file.submitting") ?? "Submitting..." }}';

        $.ajax({
            url: '{{ route("appointment-change-requests.store") }}',
            method: 'POST',
            data: {
                _token:         '{{ csrf_token() }}',
                appointment_id: appointmentId,
                request_type:   requestType,
                reason:         reason,
                requested_date: requestedDate || null,
                preferred_time: preferredTime || null,
            },
            success: function (res) {
                closeModal();
                table.draw(false);
                if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(k => {
                        const map = { appointment_id:'err-appointment', request_type:'err-type', reason:'err-reason', requested_date:'err-date' };
                        if (map[k]) showError(map[k], errors[k][0]);
                    });
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                    if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                }
            },
            complete: function () {
                btn.disabled = false;
                btn.textContent = '{{ __("file.submit_request") ?? "Submit Request" }}';
            }
        });
    });

    // ── Withdraw ──────────────────────────────────────────────────────────────
    window.withdrawRequest = function (url) {
        if (!confirm('{{ __("file.confirm_withdraw_request") ?? "Are you sure you want to withdraw this request?" }}')) return;
        $.ajax({
            url: url,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                table.draw(false);
                if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
            }
        });
    };

    // ── Error Helpers ─────────────────────────────────────────────────────────
    function showError(id, msg) {
        const el = document.getElementById(id);
        if (el) { el.textContent = msg; el.classList.remove('hidden'); }
    }
    function clearErrors() {
        ['err-appointment','err-type','err-reason','err-date'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.textContent = ''; el.classList.add('hidden'); }
        });
    }
});
</script>
@endpush
