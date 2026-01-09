@extends('layouts.app')

@section('title', __('file.schedule_appointment'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('appointments.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.appointments') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.schedule_appointment') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.schedule_new_appointment') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.fill_details_below') }}</p>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-8">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
            <div class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.patient') }} <span class="text-red-500">*</span></label>
                        <select name="patient_id" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_patient') }}</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->getFullNameAttribute() }} (MRN: {{ $patient->medical_record_number ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.appointment_type') }} <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-gray-900 bg-gray-100 dark:border-gray-400 dark:bg-gray-700/70' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_SPECIFIC }}"
                                       class="text-gray-900 focus:ring-gray-900" {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('file.specific_doctor') }}</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'border-gray-900 bg-gray-100 dark:border-gray-400 dark:bg-gray-700/70' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_ANY }}"
                                       class="text-gray-900 focus:ring-gray-900" {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('file.any_doctor') }}</span>
                            </label>
                        </div>
                        @error('appointment_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div id="specialization-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.specialization') }} <span id="spec-required" class="text-red-500 hidden">*</span>
                    </label>
                    <select name="specialization_id" id="specialization_id" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        <option value="">{{ __('file.select_specialization') }}</option>
                        @foreach($specializations as $spec)
                            <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                        @endforeach
                    </select>
                    @error('specialization_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div id="doctor-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.doctor') }} <span id="doctor-required" class="text-red-500 hidden">*</span>
                    </label>
                    <select name="doctor_id" id="doctor_id" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        <option value="">{{ __('file.select_doctor') }}</option>
                    </select>
                    @error('doctor_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date') }} <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="preferred_date" required min="{{ today()->format('Y-m-d') }}"
                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                </div>

                <!-- Time slots - only shown for Specific Doctor -->
                <div id="time-slots-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.available_time_slots') }} <span class="text-red-500">*</span></label>
                    <div id="time-slots" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3"></div>
                    <input type="hidden" name="scheduled_start" id="scheduled_start" required>
                    <p id="no-slots-message" class="mt-3 text-sm text-orange-600 dark:text-orange-400 hidden">{{ __('file.no_available_slots') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reason_for_visit') }} <span class="text-red-500">*</span></label>
                    <textarea name="reason_for_visit" rows="5" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                              placeholder="{{ __('file.describe_reason') }}">{{ old('reason_for_visit') }}</textarea>
                    @error('reason_for_visit') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.patient_notes') }}</label>
                    <textarea name="patient_notes" rows="4" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                              placeholder="{{ __('file.optional_notes') }}">{{ old('patient_notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-300 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.schedule_appointment') }}
            </button>
            <a href="{{ route('appointments.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeRadios          = document.querySelectorAll('input[name="appointment_type"]');
    const specializationGroup = document.getElementById('specialization-group');
    const doctorGroup         = document.getElementById('doctor-group');
    const timeSlotsGroup      = document.getElementById('time-slots-group');
    const specRequiredMark    = document.getElementById('spec-required');
    const doctorRequiredMark  = document.getElementById('doctor-required');
    const specializationSelect = document.getElementById('specialization_id');
    const doctorSelect        = document.getElementById('doctor_id');
    const dateInput           = document.getElementById('date');

    function getCurrentType() {
        return document.querySelector('input[name="appointment_type"]:checked')?.value || '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
    }

    function updateFormVisibility() {
        const type = getCurrentType();
        const isSpecific = type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
        const isAny      = type === '{{ \App\Models\Appointment::TYPE_ANY }}';

        specializationGroup.classList.toggle('hidden', !isAny);
        doctorGroup.classList.toggle('hidden', !isSpecific);
        timeSlotsGroup.classList.toggle('hidden', !isSpecific);

        specRequiredMark.classList.toggle('hidden', !isAny);
        doctorRequiredMark.classList.toggle('hidden', !isSpecific);

        specializationSelect.required = isAny;
        doctorSelect.required = isSpecific;
        dateInput.required = true;

        // Reset
        doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';

        if (isSpecific) {
            loadAllDoctors();
            specializationSelect.value = '';
        }
    }

    function loadAllDoctors() {
        fetch('{{ route("appointments.filters", ["column" => "doctor"]) }}')
            .then(r => r.json())
            .then(data => {
                doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                Object.entries(data).forEach(([id, name]) => {
                    const opt = new Option(name, id);
                    if ('{{ old('doctor_id') }}' == id) opt.selected = true;
                    doctorSelect.add(opt);
                });
            });
    }

    typeRadios.forEach(r => r.addEventListener('change', updateFormVisibility));
    doctorSelect.addEventListener('change', loadAvailableSlots);
    dateInput.addEventListener('change', () => {
        if (getCurrentType() === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') {
            loadAvailableSlots();
        } else if (getCurrentType() === '{{ \App\Models\Appointment::TYPE_ANY }}') {
            loadAvailableSlotsForSpecialization();
        }
    });

    updateFormVisibility();
});
</script>
@endsection