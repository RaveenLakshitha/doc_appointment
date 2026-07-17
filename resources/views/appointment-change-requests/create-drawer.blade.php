{{-- Appointment Change Request Drawer (Staff/Admin Facing) --}}
<div id="cr-create-drawer"
    class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl z-[100] transform translate-x-full transition-transform duration-300 ease-in-out border-l dark:border-gray-700 hidden">
    <div class="h-full flex flex-col">
        {{-- Header --}}
        <div
            class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ __('file.create_change_request') ?? 'Create Change Request' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('file.submit_request_on_behalf') ?? 'Submit a request on behalf of a patient.' }}</p>
            </div>
            <button type="button" onclick="closeCRDrawer()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="cr-create-form" class="flex-1 overflow-y-auto p-6 space-y-6">
            @csrf

            {{-- Appointment Info Card (pre-selected) --}}
            <div id="cr-appointment-info-card"
                class="p-4 bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-100 dark:border-indigo-800 rounded-xl hidden">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/60 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <input type="hidden" name="appointment_id" id="cr-input-appointment-id">
                        <input type="hidden" name="doctor_id" id="cr-input-doctor-id">
                        <h4 id="cr-display-patient" class="text-sm font-bold text-gray-900 dark:text-white">Patient Name
                        </h4>
                        <p id="cr-display-appt-number" class="text-xs text-indigo-700 dark:text-indigo-300 font-bold">
                            #APP-0001</p>
                        <p id="cr-display-appt-date" class="text-xs text-gray-600 dark:text-gray-300 mt-1 font-medium">May 20, 2026
                            @ 10:00 AM</p>
                    </div>
                </div>
            </div>

            {{-- Appointment Search (if no appointment pre-selected) --}}
            <div id="cr-appointment-search-group">
                <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.search_appointment') ?? 'Search Appointment' }}
                    <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="cr-search-input"
                        placeholder="{{ __('file.search_by_patient_or_number') ?? 'Search by patient name or appt #...' }}"
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div id="cr-search-results"
                    class="mt-2 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl hidden bg-white dark:bg-gray-800 shadow-lg divide-y divide-gray-100 dark:divide-gray-700">
                </div>
                <p class="text-xs text-red-500 mt-1 hidden" id="cr-err-appointment"></p>
            </div>

            {{-- Request Type --}}
            <div>
                <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('file.request_type') ?? 'Request Type' }}
                    <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer group">
                        <input type="radio" name="request_type" value="reschedule" class="peer hidden" checked>
                        <div
                            class="p-3 text-center rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 transition group-hover:border-indigo-300 dark:group-hover:border-indigo-500">
                            <div class="font-bold text-sm text-gray-700 dark:text-gray-300 peer-checked:text-white">
                                {{ __('file.reschedule') ?? 'Reschedule' }}</div>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="request_type" value="cancel" class="peer hidden">
                        <div
                            class="p-3 text-center rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 peer-checked:border-red-600 peer-checked:bg-red-600 transition group-hover:border-red-300 dark:group-hover:border-red-500">
                            <div class="font-bold text-sm text-gray-700 dark:text-gray-300 peer-checked:text-white">
                                {{ __('file.cancel') ?? 'Cancel' }}</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.reason') ?? 'Reason' }}
                    <span class="text-red-500">*</span></label>
                <textarea name="reason" id="cr-reason-input" rows="4"
                    placeholder="{{ __('file.explain_reason_for_change') ?? 'Explain the reason for this change request...' }}"
                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                <p class="text-xs text-red-500 mt-1 hidden" id="cr-err-reason"></p>
            </div>
        </form>

        {{-- Footer --}}
        <div class="p-6 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex gap-3">
            <button type="button" onclick="closeCRDrawer()"
                class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 transition shadow-sm">
                {{ __('file.cancel') ?? 'Cancel' }}
            </button>
            <button type="button" id="cr-submit-btn"
                class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-md">
                {{ __('file.submit_request') ?? 'Submit Request' }}
            </button>
        </div>
    </div>
</div>

{{-- Backdrop --}}
<div id="cr-drawer-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[99] hidden" onclick="closeCRDrawer()">
</div>

{{-- ═══════════════════════════════════════════════════════════════
     Approve Modal (same as admin-index, triggered after submission)
════════════════════════════════════════════════════════════════ --}}
<div id="cr-approve-modal" class="hidden fixed inset-0 z-[200] overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
            id="cr-approve-backdrop"></div>
        <div
            class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="px-6 pt-6 pb-4 space-y-4 max-h-[80vh] overflow-y-auto">
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

                {{-- Request summary info card --}}
                <div id="cr-approve-request-info"
                    class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                </div>

                {{-- ── Reschedule scheduling fields (shown only for reschedule type) ── --}}
                <div id="cr-approve-schedule-fields" class="hidden space-y-4">
                    <div class="border-t dark:border-gray-700 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                            {{ __('file.assign_new_schedule') ?? 'Assign New Schedule' }}
                        </p>

                        {{-- Therapist Label --}}
                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.therapist') ?? 'Therapist' }}
                            </label>
                            <div class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-200 cursor-not-allowed">
                                <span id="cr-am-doctor-label">{{ __('file.loading') ?? 'Loading...' }}</span>
                                <input type="hidden" id="cr-am-doctor" name="doctor_id">
                            </div>
                        </div>

                        {{-- Requested Date --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.requested_date') ?? 'Requested Date' }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="cr-am-date" name="requested_date" min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <p class="text-xs text-red-500 mt-1 hidden" id="cr-am-err-date"></p>
                        </div>

                        {{-- Time Slot --}}
                        <div id="cr-am-slot-group" class="hidden mt-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.time_slot') ?? 'Time Slot' }}
                            </label>
                            <div class="grid grid-cols-1 gap-3">
                                <select id="cr-am-slot-select"
                                    class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="">{{ __('file.select_slot') ?? 'Select Slot' }}</option>
                                </select>
                                <div class="relative">
                                    <input type="time" id="cr-am-time"
                                        class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                    <p id="cr-am-time-hint"
                                        class="mt-1 text-[11px] text-red-600 dark:text-red-300 font-medium hidden">
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Unavailable Times --}}
                        <div id="cr-am-unavailable-container"
                            class="hidden mt-3 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('file.unavailable_times') ?? 'Unavailable Times' }}
                            </p>
                            <div id="cr-am-unavailable-list" class="space-y-1.5"></div>
                        </div>

                        {{-- Preferred Time --}}
                        <div class="mt-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.preferred_time') ?? 'Preferred Time' }}
                            </label>
                            <select id="cr-am-preferred"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">{{ __('file.any_time') ?? 'Any time' }}</option>
                                <option value="morning">{{ __('file.morning') ?? 'Morning' }}</option>
                                <option value="evening">{{ __('file.evening') ?? 'Evening' }}</option>
                                <option value="anytime">{{ __('file.anytime') ?? 'Any Time' }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.admin_notes') ?? 'Admin Notes' }}
                        ({{ __('file.optional') ?? 'Optional' }})</label>
                    <textarea id="cr-approve-notes" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="{{ __('file.admin_notes_placeholder') ?? 'Optional notes to the patient...' }}"></textarea>
                </div>
            </div>
            <div
                class="flex justify-between gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="cr-approve-cancel-btn"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition">
                    {{ __('file.cancel') ?? 'Cancel' }}
                </button>
                <div class="flex gap-2">
                    <button type="button" id="cr-approve-open-reject-btn"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('file.reject') ?? 'Reject' }}
                    </button>
                    <button type="button" id="cr-approve-confirm-btn"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('file.approve') ?? 'Approve' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     Reject Modal (same as admin-index, triggered after submission)
════════════════════════════════════════════════════════════════ --}}
<div id="cr-reject-modal" class="hidden fixed inset-0 z-[200] overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
            id="cr-reject-backdrop"></div>
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
                    <textarea id="cr-reject-notes" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="{{ __('file.rejection_reason_placeholder') ?? 'Explain why the request is being rejected...' }}"></textarea>
                    <p class="text-xs text-red-500 mt-1 hidden" id="cr-reject-notes-error">
                        {{ __('file.field_required') ?? 'This field is required.' }}</p>
                </div>
            </div>
            <div
                class="flex justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="cr-reject-cancel-btn"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition">
                    {{ __('file.cancel') ?? 'Cancel' }}
                </button>
                <button type="button" id="cr-reject-confirm-btn"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                    {{ __('file.reject') ?? 'Reject' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const drawer   = document.getElementById('cr-create-drawer');
            const backdrop = document.getElementById('cr-drawer-backdrop');
            const form     = document.getElementById('cr-create-form');
            const searchResults = document.getElementById('cr-search-results');
            const searchInput   = document.getElementById('cr-search-input');

            let searchTimeout = null;
            let crApproveUrl  = null;
            let crRejectUrl   = null;
            let crCurrentRow  = null;

            // ── Open / Close Drawer ────────────────────────────────────────────────
            window.openCRDrawer = function (appointmentId = null, appointmentData = null) {
                form.reset();
                clearErrors();

                drawer.classList.remove('hidden');
                backdrop.classList.remove('hidden');
                setTimeout(() => drawer.classList.remove('translate-x-full'), 10);

                if (appointmentId) {
                    setPreselectedAppointment(appointmentId, appointmentData);
                } else {
                    document.getElementById('cr-appointment-info-card').classList.add('hidden');
                    document.getElementById('cr-appointment-search-group').classList.remove('hidden');
                }
            };

            window.closeCRDrawer = function () {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
                setTimeout(() => drawer.classList.add('hidden'), 300);
            };

            function setPreselectedAppointment(id, data) {
                document.getElementById('cr-input-appointment-id').value = id;
                document.getElementById('cr-input-doctor-id').value      = data.doctor_id || '';
                document.getElementById('cr-display-patient').textContent     = data.patient_name || 'Patient';
                document.getElementById('cr-display-appt-number').textContent = data.appointment_number || `#${id}`;
                document.getElementById('cr-display-appt-date').innerHTML     = data.scheduled_datetime || 'Date not set';

                document.getElementById('cr-appointment-info-card').classList.remove('hidden');
                document.getElementById('cr-appointment-search-group').classList.add('hidden');
            }

            // ── Appointment Search ─────────────────────────────────────────────────
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const val = this.value.trim();
                if (val.length < 2) { searchResults.classList.add('hidden'); return; }

                searchTimeout = setTimeout(() => {
                    $.get('{{ route("appointments.datatable") }}', { search: { value: val }, length: 5 }, function (res) {
                        if (res.data && res.data.length > 0) {
                            searchResults.innerHTML = '';
                            res.data.forEach(a => {
                                const div = document.createElement('div');
                                div.className = 'p-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 cursor-pointer transition';
                                div.innerHTML = `<div class="font-bold text-xs text-gray-900 dark:text-white">${a.patient_name}</div>
                                             <div class="text-[10px] text-indigo-700 dark:text-indigo-300 font-bold">${a.appointment_number} — ${a.scheduled_datetime}</div>`;
                                div.onclick = () => {
                                    setPreselectedAppointment(a.id, a);
                                    searchResults.classList.add('hidden');
                                    searchInput.value = '';
                                };
                                searchResults.appendChild(div);
                            });
                            searchResults.classList.remove('hidden');
                        } else {
                            searchResults.innerHTML = '<div class="p-3 text-xs text-gray-500 italic">No appointments found.</div>';
                            searchResults.classList.remove('hidden');
                        }
                    });
                }, 300);
            });

            // ── Form Submission ────────────────────────────────────────────────────
            document.getElementById('cr-submit-btn').addEventListener('click', function () {
                clearErrors();
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("file.submitting") ?? "Submitting" }}...';

                $.ajax({
                    url: '{{ route("appointment-change-requests.store") }}',
                    method: 'POST',
                    data: $(form).serialize(),
                    success: function (res) {
                        if (res.success) {
                            if (typeof showNotification === 'function') {
                                showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                            } else {
                                alert(res.message);
                            }
                            form.reset();
                            closeCRDrawer();
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                if (errors.appointment_id) showError('cr-err-appointment', errors.appointment_id[0]);
                                if (errors.reason)          showError('cr-err-reason',      errors.reason[0]);
                            } else {
                                const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                                if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                            }
                        } else {
                            const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                            if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                        }
                    },
                    complete: function () {
                        btn.disabled  = false;
                        btn.innerHTML = '{{ __("file.submit_request") ?? "Submit Request" }}';
                    }
                });
            });

            // ── Approve Modal ──────────────────────────────────────────────────────
            window.openCRApproveModal = function (approveUrl, rejectUrl, row) {
                crApproveUrl = approveUrl;
                crRejectUrl  = rejectUrl;
                crCurrentRow = row;

                const info = document.getElementById('cr-approve-request-info');
                const scheduleFields = document.getElementById('cr-approve-schedule-fields');
                const slotGroup      = document.getElementById('cr-am-slot-group');

                if (row.request_type === 'reschedule') {
                    info.innerHTML = `<strong>{{ __('file.type') ?? 'Type' }}:</strong> {{ __('file.reschedule') ?? 'Reschedule' }}<br>
                    <strong>{{ __('file.patient') ?? 'Patient' }}:</strong> ${row.patient_name}<br>
                    <strong>{{ __('file.current_appointment') ?? 'Current Appt' }}:</strong> ${row.appt_date}<br>
                    <strong>{{ __('file.reason') ?? 'Reason' }}:</strong> ${row.reason}`;

                    // Show scheduling fields
                    scheduleFields.classList.remove('hidden');

                    // Pre-fill date if the patient already set one
                    const dateEl = document.getElementById('cr-am-date');
                    dateEl.value = row.requested_date_raw || '';

                    // Load doctors and display the currently assigned one
                    const doctorLabel = document.getElementById('cr-am-doctor-label');
                    const doctorInput = document.getElementById('cr-am-doctor');
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
                            
                            // Show slot group only when a doctor is assigned
                            if (doctorInput.value && dateEl.value) {
                                slotGroup.classList.remove('hidden');
                                loadCRAmSlots(doctorInput.value, dateEl.value);
                            } else {
                                slotGroup.classList.add('hidden');
                            }
                        })
                        .catch(() => {
                            doctorLabel.textContent = '{{ __("file.error") ?? "Error loading doctor" }}';
                        });

                    // Pre-fill preferred time
                    document.getElementById('cr-am-preferred').value = row.preferred_time_raw || '';

                } else {
                    info.innerHTML = `<strong>{{ __('file.type') ?? 'Type' }}:</strong> {{ __('file.cancel') ?? 'Cancel' }}<br>
                    <strong>{{ __('file.patient') ?? 'Patient' }}:</strong> ${row.patient_name}<br>
                    <strong>{{ __('file.appointment') ?? 'Appointment' }}:</strong> ${row.appointment} (${row.appt_date})<br>
                    <strong>{{ __('file.reason') ?? 'Reason' }}:</strong> ${row.reason}`;

                    scheduleFields.classList.add('hidden');
                }

                document.getElementById('cr-approve-notes').value = '';
                document.getElementById('cr-approve-modal').classList.remove('hidden');
            };

            // Date change → load slots in approve modal
            document.getElementById('cr-am-date').addEventListener('change', function () {
                const doctorId = document.getElementById('cr-am-doctor').value;
                if (doctorId && this.value) {
                    document.getElementById('cr-am-slot-group').classList.remove('hidden');
                    loadCRAmSlots(doctorId, this.value);
                } else {
                    document.getElementById('cr-am-slot-group').classList.add('hidden');
                }
            });

            // Slot select → constrain time input
            const crAmSlotSelect = document.getElementById('cr-am-slot-select');
            const crAmTimeInput  = document.getElementById('cr-am-time');
            const crAmTimeHint   = document.getElementById('cr-am-time-hint');

            crAmSlotSelect.addEventListener('change', function () {
                if (this.value) {
                    const [s, e] = this.value.split('|');
                    crAmTimeInput.min = s;
                    crAmTimeInput.max = e;
                    if (!crAmTimeInput.value || crAmTimeInput.value < s || crAmTimeInput.value > e) {
                        crAmTimeHint.textContent = `{{ __('file.please_select_a_time_between') ?? 'Please select a time between' }} ${s} {{ __('file.and') ?? 'and' }} ${e}`;
                        crAmTimeHint.classList.remove('hidden');
                    } else {
                        crAmTimeHint.classList.add('hidden');
                    }
                } else {
                    crAmTimeInput.removeAttribute('min');
                    crAmTimeInput.removeAttribute('max');
                    crAmTimeHint.classList.add('hidden');
                }
            });

            crAmTimeInput.addEventListener('input', function () {
                let hasError = false;

                if (crAmSlotSelect.value) {
                    const [s, e] = crAmSlotSelect.value.split('|');
                    if (this.value && (this.value < s || this.value > e)) {
                        const msg = `{{ __('file.time_must_be_between') ?? 'Time must be between' }} ${s} {{ __('file.and') ?? 'and' }} ${e}`;
                        this.setCustomValidity(msg);
                        this.reportValidity();
                        crAmTimeHint.textContent = msg;
                        crAmTimeHint.classList.remove('hidden');
                        hasError = true;
                    } else {
                        crAmTimeHint.classList.add('hidden');
                    }
                }

                if (!hasError && this.value && window.crAmBookedSlots) {
                    const time = this.value;
                    const dur = parseInt(currentRow?.duration_minutes || 30);
                    const [h, m] = time.split(':');
                    const startMins = parseInt(h) * 60 + parseInt(m);
                    const endMins = startMins + dur;

                    const conflict = window.crAmBookedSlots.find(a => {
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

            function loadCRAmSlots(doctorId, date) {
                crAmSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") ?? "Select Slot" }}</option>';
                document.getElementById('cr-am-unavailable-container').classList.add('hidden');

                fetch(`{{ url('doctors') }}/${doctorId}/available-slots?date=${encodeURIComponent(date)}`)
                    .then(r => r.json())
                    .then(data => {
                        const slots = data.slots || [];
                        if (slots.length === 0 && data.message) {
                            const opt = new Option(data.message, '');
                            opt.disabled = true;
                            crAmSlotSelect.add(opt);
                        }
                        slots.forEach(slot => {
                            const label = slot.label || `${slot.start} – ${slot.end}`;
                            crAmSlotSelect.add(new Option(label, `${slot.start}|${slot.end}`));
                        });

                        // Unavailable times
                        const combined = [
                            ...(data.appointments || []).map(a => ({ ...a, type: 'appointment' })),
                            ...(slots.filter(s => s.off_start && s.off_end).map(s => ({ start: s.off_start, end: s.off_end, status: '{{ __("file.off_time") ?? "Off" }}', type: 'off' }))),
                            ...(data.room_appointments || []).map(ra => ({ ...ra, type: 'room', status: ra.doctor_name })),
                        ].sort((a, b) => (a.start || '').localeCompare(b.start || ''));

                        const container = document.getElementById('cr-am-unavailable-container');
                        const list      = document.getElementById('cr-am-unavailable-list');
                        window.crAmBookedSlots = combined;
                        if (combined.length === 0) { container.classList.add('hidden'); return; }

                        container.classList.remove('hidden');
                        list.innerHTML = combined.map(a => {
                            const fmt = t => {
                                if (!t) return '--:--';
                                const [h, m] = t.split(':');
                                const hr = parseInt(h);
                                return `${hr % 12 || 12}:${m} ${hr >= 12 ? 'PM' : 'AM'}`;
                            };
                            return `<div class="flex items-center justify-between px-2.5 py-1.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-xs">
                                <span class="font-semibold text-red-700 dark:text-red-300">${fmt(a.start)} – ${fmt(a.end)}</span>
                                <span class="text-[10px] uppercase tracking-wider text-red-500 font-bold">${a.status}</span>
                            </div>`;
                        }).join('');
                    })
                    .catch(() => {
                        crAmSlotSelect.innerHTML = '<option value="">{{ __("file.error_loading_slots") ?? "Error loading slots" }}</option>';
                    });
            }

            document.getElementById('cr-approve-cancel-btn').addEventListener('click', () => {
                document.getElementById('cr-approve-modal').classList.add('hidden');
            });
            document.getElementById('cr-approve-backdrop').addEventListener('click', () => {
                document.getElementById('cr-approve-modal').classList.add('hidden');
            });

            // "Reject" button inside approve modal → switch to reject modal
            document.getElementById('cr-approve-open-reject-btn').addEventListener('click', () => {
                document.getElementById('cr-approve-modal').classList.add('hidden');
                document.getElementById('cr-reject-notes').value = '';
                document.getElementById('cr-reject-notes-error').classList.add('hidden');
                document.getElementById('cr-reject-modal').classList.remove('hidden');
            });

            document.getElementById('cr-approve-confirm-btn').addEventListener('click', function () {
                const notes = document.getElementById('cr-approve-notes').value;
                const btn   = this;

                // Validate date for reschedule
                if (crCurrentRow?.request_type === 'reschedule') {
                    const dateVal = document.getElementById('cr-am-date').value;
                    if (!dateVal) {
                        document.getElementById('cr-am-err-date').textContent = '{{ __("file.field_required") ?? "This field is required." }}';
                        document.getElementById('cr-am-err-date').classList.remove('hidden');
                        return;
                    }
                    document.getElementById('cr-am-err-date').classList.add('hidden');
                    
                    // Exact time collision check
                    const exactTime = document.getElementById('cr-am-time').value;
                    if (exactTime && window.crAmBookedSlots) {
                        for (const b of window.crAmBookedSlots) {
                            if (exactTime >= b.start && exactTime < b.end) {
                                crAmTimeHint.textContent = `{{ __('file.time_conflicts_with_booking') ?? 'Time conflicts with a booking' }} (${b.start} - ${b.end})`;
                                crAmTimeHint.classList.remove('hidden');
                                return;
                            }
                        }
                    }
                }

                btn.disabled    = true;
                btn.textContent = '{{ __("file.processing") ?? "Processing" }}...';

                const payload = {
                    _token:           '{{ csrf_token() }}',
                    admin_notes:      notes,
                    requested_date:   document.getElementById('cr-am-date').value || null,
                    requested_time:   document.getElementById('cr-am-time').value || null,
                    slot:             crAmSlotSelect.value || null,
                    preferred_time:   document.getElementById('cr-am-preferred').value || null,
                    doctor_id:        document.getElementById('cr-am-doctor')?.value || null,
                };

                $.ajax({
                    url: crApproveUrl,
                    method: 'PATCH',
                    data: payload,
                    success: function (res) {
                        document.getElementById('cr-approve-modal').classList.add('hidden');
                        if (window.LaravelDataTables && window.LaravelDataTables["docapp-table"]) window.LaravelDataTables["docapp-table"].draw(false);
                        if ($.fn.DataTable.isDataTable('#change-requests-table')) $('#change-requests-table').DataTable().draw(false);
                        if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                        if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                    },
                    complete: function () {
                        btn.disabled    = false;
                        btn.textContent = '{{ __("file.approve") ?? "Approve" }}';
                    }
                });
            });

            // ── Reject Modal ───────────────────────────────────────────────────────

            document.getElementById('cr-reject-cancel-btn').addEventListener('click', () => {
                document.getElementById('cr-reject-modal').classList.add('hidden');
            });
            document.getElementById('cr-reject-backdrop').addEventListener('click', () => {
                document.getElementById('cr-reject-modal').classList.add('hidden');
            });

            document.getElementById('cr-reject-confirm-btn').addEventListener('click', function () {
                const notes = document.getElementById('cr-reject-notes').value.trim();
                if (!notes) {
                    document.getElementById('cr-reject-notes-error').classList.remove('hidden');
                    return;
                }
                document.getElementById('cr-reject-notes-error').classList.add('hidden');

                const btn = this;
                btn.disabled    = true;
                btn.textContent = '{{ __("file.processing") ?? "Processing" }}...';

                $.ajax({
                    url: crRejectUrl,
                    method: 'PATCH',
                    data: { _token: '{{ csrf_token() }}', admin_notes: notes },
                    success: function (res) {
                        document.getElementById('cr-reject-modal').classList.add('hidden');
                        if (window.LaravelDataTables && window.LaravelDataTables["docapp-table"]) window.LaravelDataTables["docapp-table"].draw(false);
                        if ($.fn.DataTable.isDataTable('#change-requests-table')) $('#change-requests-table').DataTable().draw(false);
                        if (typeof showNotification === 'function') showNotification('{{ __("file.success") ?? "Success" }}', res.message, 'success');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("file.error_occurred") ?? "An error occurred." }}';
                        if (typeof showNotification === 'function') showNotification('{{ __("file.error") ?? "Error" }}', msg, 'error');
                    },
                    complete: function () {
                        btn.disabled    = false;
                        btn.textContent = '{{ __("file.reject") ?? "Reject" }}';
                    }
                });
            });

            // ── Helpers ────────────────────────────────────────────────────────────
            function showError(id, msg) {
                const el = document.getElementById(id);
                if (el) { el.textContent = msg; el.classList.remove('hidden'); }
            }
            function clearErrors() {
                document.querySelectorAll('[id^="cr-err-"]').forEach(el => el.classList.add('hidden'));
            }
        })();
    </script>
@endpush