@extends('layouts.app')

@section('title', __('file.edit_appointment') . ' #' . $appointment->id)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-6 pt-20">

    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('appointments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
            {{ __('file.appointments') }}
        </a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-medium text-gray-900 dark:text-white">
            #{{ $appointment->id }} • Edit
        </span>
    </div>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('file.edit_appointment_number') }}{{ $appointment->id }}
            </h1>
            <div class="flex gap-2 mt-2">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    @switch($appointment->status)
                        @case('pending')    bg-amber-100 text-amber-800 @break
                        @case('approved')   bg-green-100 text-green-800 @break
                        @case('confirmed')  bg-blue-100 text-blue-800 @break
                        @case('completed')  bg-emerald-100 text-emerald-800 @break
                        @case('cancelled')  bg-red-100 text-red-800 @break
                        @case('rejected')   bg-gray-100 text-gray-800 @break
                        @default            bg-gray-100 text-gray-700
                    @endswitch">
                    {{ ucfirst(__("file.{$appointment->status}")) }}
                </span>
                @if($appointment->appointment_type)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-violet-100 text-violet-800">
                        {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('appointments.show', $appointment) }}"
               class="px-4 py-2 bg-white border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50">
                {{ __('file.cancel') }}
            </a>
            <button form="appointmentEditForm" type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                {{ __('file.save_changes') }}
            </button>
        </div>
    </div>

    <form id="appointmentEditForm" method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Patient - read only -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.patient') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.full_name') }}</label>
                        <p class="text-base font-medium">
                            <a href="{{ route('patients.show', $appointment->patient) }}" class="text-indigo-600 hover:underline">
                                {{ $appointment->patient?->full_name ?? '—' }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">MRN</label>
                        <p>{{ $appointment->patient?->medical_record_number ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Doctor & Specialization -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.doctor') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label for="specialization_id" class="block text-sm text-gray-700 dark:text-gray-300 mb-1 required">
                            {{ __('file.specialization') }}
                        </label>
                        <select name="specialization_id" id="specialization_id" required
                                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="">{{ __('file.select_specialization') }}</option>
                            @foreach($specializations as $spec)
                                <option value="{{ $spec->id }}"
                                        {{ old('specialization_id', $appointment->specialization_id ?? $appointment->doctor?->primarySpecialization?->id) == $spec->id ? 'selected' : '' }}>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialization_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm text-gray-700 dark:text-gray-300 mb-1 required">
                            {{ __('file.doctor') }}
                        </label>
                        <select name="doctor_id" id="doctor_id" required
                                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="">{{ __('file.select_specialization_first') }}</option>
                        </select>
                        @error('doctor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.schedule') }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="date" class="block text-sm text-gray-700 dark:text-gray-300 mb-1 required">
                            {{ __('file.date') }}
                        </label>
                        <input type="date" name="date" id="date_input" required min="{{ now()->format('Y-m-d') }}"
                               value="{{ old('date', $appointment->scheduled_start?->format('Y-m-d') ?? '') }}"
                               class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slot" class="block text-sm text-gray-700 dark:text-gray-300 mb-1 required">
                            {{ __('file.time_slot') }}
                        </label>
                        <select name="slot" id="slot_select" required
                                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="">{{ __('file.select_date_first') }}</option>
                        </select>
                        @error('slot') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="appointment_type" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.type') }}
                        </label>
                        <select name="appointment_type" id="appointment_type"
                                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="{{ \App\Models\Appointment::TYPE_SPECIFIC }}" {{ old('appointment_type', $appointment->appointment_type) === \App\Models\Appointment::TYPE_SPECIFIC ? 'selected' : '' }}>
                                Specific Doctor
                            </option>
                            <option value="{{ \App\Models\Appointment::TYPE_ANY }}" {{ old('appointment_type', $appointment->appointment_type) === \App\Models\Appointment::TYPE_ANY ? 'selected' : '' }}>
                                Any Doctor
                            </option>
                            <option value="{{ \App\Models\Appointment::TYPE_PRIMARY_PROVIDER }}" {{ old('appointment_type', $appointment->appointment_type) === \App\Models\Appointment::TYPE_PRIMARY_PROVIDER ? 'selected' : '' }}>
                                Primary Provider
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.duration_minutes') }}
                        </label>
                        <input type="number" name="duration_minutes" min="5" max="240" step="5"
                               value="{{ old('duration_minutes', $appointment->duration_minutes ?? 15) }}"
                               class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        @error('duration_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Change -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.status') }}</h3>
            <select name="status" class="w-full md:w-64 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                @if($appointment->status === 'pending')
                    <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('status', $appointment->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $appointment->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                @elseif($appointment->status === 'approved')
                    <option value="approved" {{ old('status', $appointment->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                @elseif($appointment->status === 'completed')
                    <option value="completed" selected>Completed</option>
                @else
                    <option value="{{ $appointment->status }}" selected>{{ ucfirst($appointment->status) }}</option>
                @endif
            </select>
            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Details / Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.details') }}</h3>
                <div class="space-y-4">
                    <div>
                        <label for="reason_for_visit" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.reason_for_visit') }}
                        </label>
                        <textarea name="reason_for_visit" rows="3"
                                  class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">{{ old('reason_for_visit', $appointment->reason_for_visit ?? '') }}</textarea>
                        @error('reason_for_visit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="patient_notes" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.patient_notes') }}
                        </label>
                        <textarea name="patient_notes" rows="3"
                                  class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">{{ old('patient_notes', $appointment->patient_notes ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="admin_notes" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.admin_notes') }}
                        </label>
                        <textarea name="admin_notes" rows="3"
                                  class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">{{ old('admin_notes', $appointment->admin_notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Treatments - current + available with checkboxes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('file.treatments') }}
                </h3>

                <!-- Current attached treatments -->
                @if($appointment->treatments->isNotEmpty())
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.treatment') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.unit_price') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.qty') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.line_total') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($appointment->treatments as $treatment)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                                            {{ $treatment->name }}
                                            @if($treatment->code)
                                                <span class="ml-1.5 text-xs text-gray-500 dark:text-gray-400">({{ $treatment->code }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                            {{ $currency_code ?? 'LKR' }} {{ number_format($treatment->pivot->price_at_time ?? 0, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                            {{ $treatment->pivot->quantity ?? 1 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                            {{ $currency_code ?? 'LKR' }} {{ number_format(($treatment->pivot->quantity ?? 1) * ($treatment->pivot->price_at_time ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800/50 font-medium">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                        {{ __('file.total_treatments_cost') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400">
                                        {{ $currency_code ?? 'LKR' }} {{ number_format($appointment->total_treatment_price ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-6">
                        {{ __('file.no_treatments_added_yet') }}
                    </p>
                @endif

                <!-- Available treatments with checkboxes -->
                <div id="available-treatments" class="mt-4">
                    <h4 class="text-xs font-medium text-gray-600 dark:text-gray-300 mb-3">
                        Available treatments for this doctor
                    </h4>
                    <div id="treatments-checkbox-list" class="max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-900/50">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                            Select a doctor to load available treatments...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 flex justify-end gap-4">
            <a href="{{ route('appointments.show', $appointment) }}"
               class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                {{ __('file.cancel') }}
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                {{ __('file.save_appointment') }}
            </button>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const specSelect   = document.getElementById('specialization_id');
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput    = document.getElementById('date_input');
    const slotSelect   = document.getElementById('slot_select');
    const treatmentsList = document.getElementById('treatments-checkbox-list');

    const preDoctorId = '{{ $appointment->doctor_id ?? "" }}';
    const currentTreatmentIds = {{ json_encode($appointment->treatments->pluck('id')->toArray()) }};

    function resetSlots() {
        slotSelect.innerHTML = '<option value="">{{ __("file.select_date_first") }}</option>';
        slotSelect.disabled = true;
    }

    function loadSlots() {
        const doctorId = doctorSelect?.value || preDoctorId;
        const date = dateInput.value;

        resetSlots();

        if (!doctorId || !date) return;

        slotSelect.disabled = false;
        slotSelect.innerHTML = '<option value="">Loading...</option>';

        const url = '{{ route("doctors.available-slots", ":doctor") }}'
            .replace(':doctor', doctorId) + '?date=' + encodeURIComponent(date);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                slotSelect.innerHTML = '<option value="">{{ __("file.select_time_slot") }}</option>';
                if (data.slots?.length) {
                    data.slots.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.start + '|' + s.end;
                        opt.textContent = s.label;
                        if ('{{ $appointment->scheduled_start?->format('H:i') ?? '' }}' === s.start &&
                            '{{ $appointment->scheduled_end?->format('H:i')   ?? '' }}' === s.end) {
                            opt.selected = true;
                        }
                        slotSelect.appendChild(opt);
                    });
                } else {
                    slotSelect.innerHTML = '<option value="">No available slots</option>';
                }
            })
            .catch(() => {
                slotSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    function loadDoctors(callback = null) {
        const specId = specSelect.value;
        if (!specId) {
            doctorSelect.innerHTML = '<option value="">{{ __("file.select_specialization_first") }}</option>';
            resetSlots();
            return;
        }

        doctorSelect.innerHTML = '<option value="">Loading...</option>';

        const url = '{{ route("appointments.doctors.by_specialization", ":specialization_id") }}'
            .replace(':specialization_id', specId);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.value;
                    opt.textContent = d.text;
                    doctorSelect.appendChild(opt);
                });

                if (preDoctorId) {
                    const match = Array.from(doctorSelect.options).find(o => o.value === preDoctorId);
                    if (match) match.selected = true;
                }

                if (callback) callback();
            })
            .catch(() => {
                doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            });
    }

    function loadDoctorTreatments() {
        const doctorId = doctorSelect?.value || preDoctorId;

        if (!doctorId) {
            treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Select a doctor to see available treatments...</p>';
            return;
        }

        treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Loading treatments...</p>';

        const url = '{{ route("appointments.treatments", ":doctor") }}'.replace(':doctor', doctorId);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.treatments || data.treatments.length === 0) {
                    treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No treatments available for this doctor</p>';
                    return;
                }

                let html = '';
                data.treatments.forEach(t => {
                    const isChecked = currentTreatmentIds.includes(t.id);
                    html += `
                        <label class="flex items-center py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded cursor-pointer">
                            <input type="checkbox"
                                   name="treatment_ids[]"
                                   value="${t.id}"
                                   ${isChecked ? 'checked' : ''}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm text-gray-900 dark:text-gray-200 flex-1">
                                ${t.name}
                                ${t.code ? `<span class="ml-1 text-gray-500">(${t.code})</span>` : ''}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                {{ $currency_code ?? 'LKR' }} ${Number(t.price || 0).toFixed(2)}
                            </span>
                        </label>
                    `;
                });

                treatmentsList.innerHTML = html;
            })
            .catch(() => {
                treatmentsList.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400 text-center py-8">Error loading treatments</p>';
            });
    }

    // Event listeners
    if (specSelect && doctorSelect) {
        specSelect.addEventListener('change', () => {
            loadDoctors(() => {
                resetSlots();
                if (dateInput.value) loadSlots();
                loadDoctorTreatments();
            });
        });

        doctorSelect.addEventListener('change', () => {
            resetSlots();
            if (dateInput.value) loadSlots();
            loadDoctorTreatments();
        });
    }

    dateInput.addEventListener('change', () => {
        if ((doctorSelect?.value || preDoctorId) && dateInput.value) {
            loadSlots();
        }
    });

    // Initial loads
    if (preDoctorId) {
        const initialSpec = '{{ $appointment->doctor?->primarySpecialization?->id ?? $appointment->specialization_id ?? "" }}';
        if (initialSpec && specSelect) {
            const opt = Array.from(specSelect.options).find(o => o.value === initialSpec);
            if (opt) opt.selected = true;
            loadDoctors(() => {
                if (dateInput.value) loadSlots();
                loadDoctorTreatments();
            });
        }
    } else if (specSelect?.value) {
        loadDoctors();
    }

    // Load treatments on page load if doctor pre-selected
    if (preDoctorId) {
        loadDoctorTreatments();
    }
});
</script>
@endsection