@extends('layouts.app')

@section('title', __('file.appointment_change_requests') ?? 'Appointment Change Requests')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.appointment_change_requests') ?? 'Appointment Change Requests' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.change_requests_desc') ?? 'Patient-submitted reschedule and cancellation requests.' }}
                </p>
            </div>

            {{-- Stats Cards & Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $pendingCount = \App\Models\AppointmentChangeRequest::where('status', 'pending')->count();
                    $todayCount = \App\Models\AppointmentChangeRequest::whereDate('created_at', today())->count();
                @endphp
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">{{ $pendingCount }}
                        {{ __('file.pending') ?? 'Pending' }}</span>
                </div>
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-semibold text-blue-800 dark:text-blue-200">{{ $todayCount }}
                        {{ __('file.today') ?? 'Today' }}</span>
                </div>
            </div>
        </div>

        {{-- Inline Filters --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <span
                    class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.filter_by_status') ?? 'Filter by Status' }}:</span>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" data-filter-status=""
                        class="filter-status-btn active px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 transition">
                        {{ __('file.all') ?? 'All' }}
                    </button>
                    <button type="button" data-filter-status="pending"
                        class="filter-status-btn px-3 py-1.5 text-xs font-medium rounded-full border border-yellow-300 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition">
                        {{ __('file.pending') ?? 'Pending' }}
                    </button>
                    <button type="button" data-filter-status="approved"
                        class="filter-status-btn px-3 py-1.5 text-xs font-medium rounded-full border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 transition">
                        {{ __('file.approved') ?? 'Approved' }}
                    </button>
                    <button type="button" data-filter-status="rejected"
                        class="filter-status-btn px-3 py-1.5 text-xs font-medium rounded-full border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 transition">
                        {{ __('file.rejected') ?? 'Rejected' }}
                    </button>
                </div>

                <span
                    class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-4">{{ __('file.type') ?? 'Type' }}:</span>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" data-filter-type=""
                        class="filter-type-btn active-type px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 transition">
                        {{ __('file.all') ?? 'All' }}
                    </button>
                    <button type="button" data-filter-type="reschedule"
                        class="filter-type-btn px-3 py-1.5 text-xs font-medium rounded-full border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                        {{ __('file.reschedule') ?? 'Reschedule' }}
                    </button>
                    <button type="button" data-filter-type="cancel"
                        class="filter-type-btn px-3 py-1.5 text-xs font-medium rounded-full border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 transition">
                        {{ __('file.cancel') ?? 'Cancel' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Active Patient Filter --}}
        <div id="patient-filter-indicator" class="hidden mb-4 p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                <span class="text-sm text-indigo-800 dark:text-indigo-200 font-medium">{{ __('file.filtering_by_patient') ?? 'Showing history for patient:' }} <strong id="patient-filter-name"></strong></span>
            </div>
            <button type="button" id="clear-patient-filter" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline font-medium">{{ __('file.clear_filter') ?? 'Clear Filter' }}</button>
        </div>

        {{-- Table --}}
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <style>
                    /* Compact table styles */
                    #docapp-table.dataTable { font-size: 11px; table-layout: fixed; width: 100% !important; }
                    #docapp-table.dataTable td,
                    #docapp-table.dataTable th  { padding: .35rem .5rem; white-space: nowrap; text-overflow: ellipsis; }
                    #docapp-table.dataTable td:not(:last-child),
                    #docapp-table.dataTable th:not(:last-child) { overflow: hidden; }
                    
                    /* Column widths */
                    #docapp-table.dataTable colgroup col:nth-child(1)  { width: 140px !important; } /* Patient */
                    #docapp-table.dataTable colgroup col:nth-child(2)  { width: 120px !important; } /* Appointment */
                    #docapp-table.dataTable colgroup col:nth-child(3)  { width: 110px !important; } /* Appt. Date */
                    #docapp-table.dataTable colgroup col:nth-child(4)  { width: 80px  !important; } /* Type */
                    #docapp-table.dataTable colgroup col:nth-child(5)  { width: 130px !important; } /* Requested Date */
                    #docapp-table.dataTable colgroup col:nth-child(6)  { width: 80px  !important; } /* Status */
                    #docapp-table.dataTable colgroup col:nth-child(7)  { width: 90px  !important; } /* Submitted */
                    #docapp-table.dataTable colgroup col:nth-child(8)  { width: 180px !important; } /* Actions */
                </style>
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700"
                    style="table-layout:fixed;">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.patient') ?? 'Patient' }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.appointment') ?? 'Appointment' }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.appt_date') ?? 'Appt. Date' }}
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.type') ?? 'Type' }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.requested_date') ?? 'Requested Date' }}
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.status') ?? 'Status' }}
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.submitted_on') ?? 'Submitted' }}
                            </th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider no-export">
                                {{ __('file.actions') ?? 'Actions' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div id="approve-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                id="approve-backdrop"></div>
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 pt-6 pb-4 space-y-4 max-h-[85vh] overflow-y-auto">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('file.approve_request') ?? 'Approve Request' }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('file.approve_request_desc') ?? 'This will apply the requested change to the appointment.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Request summary --}}
                    <div id="approve-request-info"
                        class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    </div>

                    {{-- Scheduling fields (reschedule only) --}}
                    <div id="approve-schedule-fields" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 hidden">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                            {{ __('file.assign_new_schedule') ?? 'Assign New Schedule' }}
                        </p>

                        {{-- Therapist Label --}}
                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.therapist') ?? 'Therapist' }}
                            </label>
                            <div class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-200 cursor-not-allowed">
                                <span id="approve-doctor-label">{{ __('file.loading') ?? 'Loading...' }}</span>
                                <input type="hidden" id="approve-doctor" name="doctor_id">
                            </div>
                        </div>

                        {{-- Requested Date --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.requested_date') ?? 'Requested Date' }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="approve-date" min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <p class="text-xs text-red-500 mt-1 hidden" id="approve-err-date"></p>
                        </div>

                        {{-- Time Slot --}}
                        <div id="approve-slot-group" class="hidden mt-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.time_slot') ?? 'Time Slot' }}
                            </label>
                            <div class="grid grid-cols-1 gap-3">
                                <select id="approve-slot-select"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="">{{ __('file.select_slot') ?? 'Select Slot' }}</option>
                                </select>
                                <div>
                                    <input type="time" id="approve-time"
                                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                    <p id="approve-time-hint" class="mt-1 text-[11px] text-red-600 dark:text-red-300 font-medium hidden"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Unavailable Times --}}
                        <div id="approve-unavailable-container"
                            class="hidden mt-3 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('file.unavailable_times') ?? 'Unavailable Times' }}
                            </p>
                            <div id="approve-unavailable-list" class="space-y-1.5"></div>
                        </div>

                        {{-- Preferred Time --}}
                        <div class="mt-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.preferred_time') ?? 'Preferred Time' }}
                            </label>
                            <select id="approve-preferred"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">{{ __('file.any_time') ?? 'Any time' }}</option>
                                <option value="morning">{{ __('file.morning') ?? 'Morning' }}</option>
                                <option value="evening">{{ __('file.evening') ?? 'Evening' }}</option>
                                <option value="anytime">{{ __('file.anytime') ?? 'Any Time' }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Admin Notes --}}
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.admin_notes') ?? 'Admin Notes' }}
                            ({{ __('file.optional') ?? 'Optional' }})</label>
                        <textarea id="approve-notes" rows="3"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="{{ __('file.admin_notes_placeholder') ?? 'Optional notes to the patient...' }}"></textarea>
                    </div>
                </div>
                <div
                    class="flex justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="approve-cancel-btn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition">
                        {{ __('file.cancel') ?? 'Cancel' }}
                    </button>
                    <button type="button" id="approve-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-sm">
                        {{ __('file.approve') ?? 'Approve' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="reject-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                id="reject-backdrop"></div>
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('file.reject_request') ?? 'Reject Request' }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('file.reject_request_desc') ?? 'The appointment will remain unchanged.' }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.reason_for_rejection') ?? 'Reason for Rejection' }}
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reject-notes" rows="3"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="{{ __('file.rejection_reason_placeholder') ?? 'Explain why the request is being rejected...' }}"></textarea>
                        <p class="text-xs text-red-500 mt-1 hidden" id="reject-notes-error">
                            {{ __('file.field_required') ?? 'This field is required.' }}</p>
                    </div>
                </div>
                <div
                    class="flex justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="reject-cancel-btn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition">
                        {{ __('file.cancel') ?? 'Cancel' }}
                    </button>
                    <button type="button" id="reject-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                        {{ __('file.reject') ?? 'Reject' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentApproveUrl = null;
            let currentRejectUrl = null;
            let currentRow = null;
            let activeStatus = '';
            let activeType = '';
            let activePatientId = null;

            window.filterByPatient = function(id, name) {
                activePatientId = id;
                document.getElementById('patient-filter-name').textContent = name;
                document.getElementById('patient-filter-indicator').classList.remove('hidden');
                table.draw();
            };

            document.getElementById('clear-patient-filter').addEventListener('click', function() {
                activePatientId = null;
                document.getElementById('patient-filter-indicator').classList.add('hidden');
                table.draw();
            });

            // ── DataTable ─────────────────────────────────────────────────────────────
            const table = $('#docapp-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                autoWidth: false,
                ajax: {
                    url: '{{ route("appointment-change-requests.admin.datatable") }}',
                    data: function (d) {
                        d.status = activeStatus;
                        d.request_type = activeType;
                        d.patient_id = activePatientId;
                    }
                },
                order: [[7, 'desc']],
                columnDefs: [
                    { targets: [0, 1, 3, 6], responsivePriority: 1 },
                    { targets: -1, orderable: false, searchable: false },
                ],
                columns: [
                    { 
                        data: 'patient_name', 
                        render: (d, t, r) => `<div class="font-medium text-gray-900 dark:text-white flex items-center justify-between group">
                            <span class="truncate pr-2" title="${d}">${d}</span>
                            <button type="button" onclick="filterByPatient(${r.patient_id}, '${d.replace(/'/g, "\\'")}')" class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 text-[10px] uppercase tracking-wider font-semibold bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-1.5 py-0.5 rounded" title="{{ __('file.history') ?? 'History' }}">{{ __('file.history') ?? 'History' }}</button>
                        </div>` 
                    },
                    {
                        data: 'appointment',
                        render: function(d) {
                            if (d && d.startsWith('#')) {
                                return `<div class="flex items-center text-gray-400 dark:text-gray-500"><span class="text-xs font-normal">${d}</span></div>`;
                            }
                            return `<div class="font-medium text-indigo-600 dark:text-indigo-400">${d}</div>`;
                        }
                    },
                    { data: 'appt_date', render: d => `<span class="text-sm text-gray-600 dark:text-gray-300">${d}</span>` },
                    { data: 'type_badge', className: 'text-center' },
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
                    { data: 'status_badge', className: 'text-center' },
                    { data: 'created_at', render: d => `<span class="text-sm text-gray-500 dark:text-gray-400">${d}</span>` },
                    {
                        data: null,
                        className: 'text-right whitespace-nowrap',
                        render: function (d, t, r) {
                            if (r.status !== 'pending') {
                                const note = r.admin_notes
                                    ? `<span class="text-xs text-gray-400 italic">${r.admin_notes.substring(0, 40)}${r.admin_notes.length > 40 ? '…' : ''}</span>`
                                    : `<span class="text-xs text-gray-400">—</span>`;
                                return `<div class="text-right">${note}</div>`;
                            }
                            return `
                            <div class="flex items-center justify-end gap-1">
                                <button type="button"
                                    onclick="openApproveModal('${r.approve_url}', ${JSON.stringify(r).replace(/"/g, '&quot;')})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition"
                                    title="{{ __('file.approve') ?? 'Approve' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('file.approve') ?? 'Approve' }}
                                </button>
                                <button type="button"
                                    onclick="openRejectModal('${r.reject_url}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition"
                                    title="{{ __('file.reject') ?? 'Reject' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('file.reject') ?? 'Reject' }}
                                </button>
                            </div>`;
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

            // ── Filter Buttons ────────────────────────────────────────────────────────
            document.querySelectorAll('.filter-status-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-status-btn').forEach(b => b.classList.remove('active', 'ring-2', 'ring-offset-1'));
                    this.classList.add('active', 'ring-2', 'ring-offset-1');
                    activeStatus = this.dataset.filterStatus;
                    table.draw();
                });
            });

            document.querySelectorAll('.filter-type-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-type-btn').forEach(b => b.classList.remove('active-type', 'ring-2', 'ring-offset-1'));
                    this.classList.add('active-type', 'ring-2', 'ring-offset-1');
                    activeType = this.dataset.filterType;
                    table.draw();
                });
            });

            // ── Approve Modal ─────────────────────────────────────────────────────────
            const approveSlotSelect = document.getElementById('approve-slot-select');
            const approveTimeInput  = document.getElementById('approve-time');
            const approveTimeHint   = document.getElementById('approve-time-hint');

            window.openApproveModal = function (url, row) {
                currentApproveUrl = url;
                currentRow = row;

                const info           = document.getElementById('approve-request-info');
                const scheduleFields = document.getElementById('approve-schedule-fields');
                const slotGroup      = document.getElementById('approve-slot-group');

                if (row.request_type === 'reschedule') {
                    info.innerHTML = `<strong>{{ __('file.type') ?? 'Type' }}:</strong> {{ __('file.reschedule') ?? 'Reschedule' }}<br>
                    <strong>{{ __('file.patient') ?? 'Patient' }}:</strong> ${row.patient_name}<br>
                    <strong>{{ __('file.current_appointment') ?? 'Current Appt' }}:</strong> ${row.appt_date}<br>
                    <strong>{{ __('file.reason') ?? 'Reason' }}:</strong> ${row.reason}`;

                    scheduleFields.classList.remove('hidden');

                    // Pre-fill from stored values
                    const dateEl = document.getElementById('approve-date');
                    dateEl.value = row.requested_date_raw || '';
                    document.getElementById('approve-preferred').value = row.preferred_time_raw || '';
                    document.getElementById('approve-err-date').classList.add('hidden');
                    
                    // Load doctors and display the currently assigned one
                    const doctorLabel = document.getElementById('approve-doctor-label');
                    const doctorInput = document.getElementById('approve-doctor');
                    doctorLabel.textContent = '{{ __("file.loading") ?? "Loading..." }}';
                    doctorInput.value = row.doctor_id || '';
                    fetch('{{ route("appointments.doctors.all") }}')
                        .then(r => r.json())
                        .then(doctors => {
                            let found = false;
                            doctors.forEach(doc => {
                                if (row.doctor_id == doc.value) {
                                    doctorLabel.textContent = doc.text;
                                    found = true;
                                }
                            });
                            if (!found) doctorLabel.textContent = '{{ __("file.unknown") ?? "Unknown" }}';
                            
                            // Load slots for the selected doctor if date is present
                            if (doctorInput.value && dateEl.value) {
                                slotGroup.classList.remove('hidden');
                                loadApproveSlots(doctorInput.value, dateEl.value);
                            } else {
                                slotGroup.classList.add('hidden');
                            }
                        })
                        .catch(() => {
                            doctorLabel.textContent = '{{ __("file.error") ?? "Error loading doctor" }}';
                        });
                } else {
                    info.innerHTML = `<strong>{{ __('file.type') ?? 'Type' }}:</strong> {{ __('file.cancel') ?? 'Cancel' }}<br>
                    <strong>{{ __('file.patient') ?? 'Patient' }}:</strong> ${row.patient_name}<br>
                    <strong>{{ __('file.appointment') ?? 'Appointment' }}:</strong> ${row.appointment} (${row.appt_date})<br>
                    <strong>{{ __('file.reason') ?? 'Reason' }}:</strong> ${row.reason}`;

                    scheduleFields.classList.add('hidden');
                }

                document.getElementById('approve-notes').value = '';
                document.getElementById('approve-modal').classList.remove('hidden');
            };

            // Date change → reload slots
            document.getElementById('approve-date').addEventListener('change', function () {
                const doctorId = document.getElementById('approve-doctor').value;
                if (doctorId && this.value) {
                    document.getElementById('approve-slot-group').classList.remove('hidden');
                    loadApproveSlots(doctorId, this.value);
                } else {
                    document.getElementById('approve-slot-group').classList.add('hidden');
                }
            });

            // Slot → constrain time
            approveSlotSelect.addEventListener('change', function () {
                if (this.value) {
                    const [s, e] = this.value.split('|');
                    approveTimeInput.min = s;
                    approveTimeInput.max = e;
                    if (!approveTimeInput.value || approveTimeInput.value < s || approveTimeInput.value > e) {
                        approveTimeHint.textContent = `{{ __('file.please_select_a_time_between') ?? 'Please select a time between' }} ${s} {{ __('file.and') ?? 'and' }} ${e}`;
                        approveTimeHint.classList.remove('hidden');
                    } else {
                        approveTimeHint.classList.add('hidden');
                    }
                } else {
                    approveTimeInput.removeAttribute('min');
                    approveTimeInput.removeAttribute('max');
                    approveTimeHint.classList.add('hidden');
                }
            });

            approveTimeInput.addEventListener('input', function () {
                let hasError = false;

                if (approveSlotSelect.value) {
                    const [s, e] = approveSlotSelect.value.split('|');
                    if (this.value && (this.value < s || this.value > e)) {
                        const msg = `{{ __('file.time_must_be_between') ?? 'Time must be between' }} ${s} {{ __('file.and') ?? 'and' }} ${e}`;
                        this.setCustomValidity(msg);
                        this.reportValidity();
                        approveTimeHint.textContent = msg;
                        approveTimeHint.classList.remove('hidden');
                        hasError = true;
                    } else {
                        approveTimeHint.classList.add('hidden');
                    }
                }

                if (!hasError && this.value && window.approveBookedSlots) {
                    const time = this.value;
                    const dur = parseInt(currentRow?.duration_minutes || 30);
                    const [h, m] = time.split(':');
                    const startMins = parseInt(h) * 60 + parseInt(m);
                    const endMins = startMins + dur;

                    const conflict = window.approveBookedSlots.find(a => {
                        if (!a.start || !a.end) return false;
                        const [sh, sm] = a.start.split(':');
                        const sMins = parseInt(sh) * 60 + parseInt(sm);
                        const [eh, em] = a.end.split(':');
                        const eMins = parseInt(eh) * 60 + parseInt(em);
                        return (startMins < eMins && endMins > sMins);
                    });

                    if (conflict) {
                        const msg = conflict.type === 'appointment'
                            ? `{{ __('file.doctor_busy_at_this_time') ?? 'Therapist is already busy during this time' }} (${conflict.start} – ${conflict.end})`
                            : `{{ __('file.doctor_on_break_at_this_time') ?? 'Therapist has a scheduled break during this time' }} (${conflict.start} – ${conflict.end})`;
                        this.setCustomValidity(msg);
                        this.reportValidity();
                        hasError = true;
                    }
                }

                if (!hasError) {
                    this.setCustomValidity('');
                }
            });

            function loadApproveSlots(doctorId, date) {
                approveSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") ?? "Select Slot" }}</option>';
                document.getElementById('approve-unavailable-container').classList.add('hidden');

                fetch(`{{ url('doctors') }}/${doctorId}/available-slots?date=${encodeURIComponent(date)}`)
                    .then(r => r.json())
                    .then(data => {
                        const slots = data.slots || [];
                        if (slots.length === 0 && data.message) {
                            const opt = new Option(data.message, ''); opt.disabled = true;
                            approveSlotSelect.add(opt);
                        }
                        slots.forEach(slot => {
                            approveSlotSelect.add(new Option(slot.label || `${slot.start} – ${slot.end}`, `${slot.start}|${slot.end}`));
                        });

                        const combined = [
                            ...(data.appointments || []).map(a => ({ ...a, type: 'appointment' })),
                            ...(slots.filter(s => s.off_start && s.off_end).map(s => ({ start: s.off_start, end: s.off_end, status: '{{ __("file.off_time") ?? "Off" }}' }))),
                            ...(data.room_appointments || []).map(ra => ({ ...ra, status: ra.doctor_name })),
                        ].sort((a, b) => (a.start || '').localeCompare(b.start || ''));

                        const container = document.getElementById('approve-unavailable-container');
                        const list      = document.getElementById('approve-unavailable-list');
                        window.approveBookedSlots = combined;
                        if (!combined.length) { container.classList.add('hidden'); return; }
                        container.classList.remove('hidden');
                        list.innerHTML = combined.map(a => {
                            const fmt = t => { if (!t) return '--:--'; const [h, m] = t.split(':'); const hr = parseInt(h); return `${hr%12||12}:${m} ${hr>=12?'PM':'AM'}`; };
                            return `<div class="flex items-center justify-between px-2.5 py-1.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-xs">
                                <span class="font-semibold text-red-700 dark:text-red-300">${fmt(a.start)} – ${fmt(a.end)}</span>
                                <span class="text-[10px] uppercase tracking-wider text-red-500 font-bold">${a.status}</span>
                            </div>`;
                        }).join('');
                    })
                    .catch(() => { approveSlotSelect.innerHTML = '<option value="">{{ __("file.error_loading_slots") ?? "Error" }}</option>'; });
            }

            document.getElementById('approve-cancel-btn').addEventListener('click', () => {
                document.getElementById('approve-modal').classList.add('hidden');
            });
            document.getElementById('approve-backdrop').addEventListener('click', () => {
                document.getElementById('approve-modal').classList.add('hidden');
            });

            document.getElementById('approve-confirm-btn').addEventListener('click', function () {
                const notes = document.getElementById('approve-notes').value;
                const btn = this;

                // Validate date for reschedule
                if (currentRow?.request_type === 'reschedule') {
                    const dateVal = document.getElementById('approve-date').value;
                    if (!dateVal) {
                        document.getElementById('approve-err-date').textContent = '{{ __("file.field_required") ?? "This field is required." }}';
                        document.getElementById('approve-err-date').classList.remove('hidden');
                        return;
                    }
                    document.getElementById('approve-err-date').classList.add('hidden');
                    
                    // Exact time collision check
                    const exactTime = document.getElementById('approve-time').value;
                    if (exactTime && window.approveBookedSlots) {
                        for (const b of window.approveBookedSlots) {
                            if (exactTime >= b.start && exactTime < b.end) {
                                approveTimeHint.textContent = `{{ __('file.time_conflicts_with_booking') ?? 'Time conflicts with a booking' }} (${b.start} - ${b.end})`;
                                approveTimeHint.classList.remove('hidden');
                                return;
                            }
                        }
                    }
                }

                btn.disabled = true;
                btn.textContent = '{{ __("file.processing") ?? "Processing" }}...';

                const payload = {
                    _token:         '{{ csrf_token() }}',
                    admin_notes:    notes,
                    requested_date: document.getElementById('approve-date').value || null,
                    requested_time: approveTimeInput.value || null,
                    slot:           approveSlotSelect.value || null,
                    preferred_time: document.getElementById('approve-preferred').value || null,
                    doctor_id:      document.getElementById('approve-doctor')?.value || null,
                };

                $.ajax({
                    url: currentApproveUrl,
                    method: 'PATCH',
                    data: payload,
                    success: function (res) {
                        document.getElementById('approve-modal').classList.add('hidden');
                        table.draw(false);
                        if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                        if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                    },
                    complete: function () {
                        btn.disabled = false;
                        btn.textContent = '{{ __("file.approve") ?? "Approve" }}';
                    }
                });
            });

            // ── Reject Modal ──────────────────────────────────────────────────────────
            window.openRejectModal = function (url) {
                currentRejectUrl = url;
                document.getElementById('reject-notes').value = '';
                document.getElementById('reject-notes-error').classList.add('hidden');
                document.getElementById('reject-modal').classList.remove('hidden');
            };

            document.getElementById('reject-cancel-btn').addEventListener('click', () => {
                document.getElementById('reject-modal').classList.add('hidden');
            });
            document.getElementById('reject-backdrop').addEventListener('click', () => {
                document.getElementById('reject-modal').classList.add('hidden');
            });

            document.getElementById('reject-confirm-btn').addEventListener('click', function () {
                const notes = document.getElementById('reject-notes').value.trim();
                if (!notes) {
                    document.getElementById('reject-notes-error').classList.remove('hidden');
                    return;
                }
                document.getElementById('reject-notes-error').classList.add('hidden');

                const btn = this;
                btn.disabled = true;
                btn.textContent = '{{ __("file.processing") ?? "Processing" }}...';

                $.ajax({
                    url: currentRejectUrl,
                    method: 'PATCH',
                    data: { _token: '{{ csrf_token() }}', admin_notes: notes },
                    success: function (res) {
                        document.getElementById('reject-modal').classList.add('hidden');
                        table.draw(false);
                        if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                        if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                    },
                    complete: function () {
                        btn.disabled = false;
                        btn.textContent = '{{ __("file.reject") ?? "Reject" }}';
                    }
                });
            });
        });
    </script>
@endpush