@extends('layouts.app')

@section('title', __('file.create_appointment_request'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('appointment_requests.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.appointment_requests') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.create') }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.create_new_appointment_request') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.create_appointment_request_description') }}
            </p>
        </div>

        <form method="POST" action="{{ route('appointment_requests.store') }}" class="space-y-8">
            @csrf

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="space-y-8">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.patient') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="patient_id" required class="tom-select-patient w-full">
                                <option value="">{{ __('file.select_patient') }}</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->getFullNameAttribute() }} (ID: {{ $patient->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                                {{ __('file.request_details') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.doctor_selection') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="doctor_selection_mode" required id="doctor-selection-mode"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent transition-all">
                                        <option value="">{{ __('file.select_option') }}</option>
                                        <option value="specific" {{ old('doctor_selection_mode') == 'specific' ? 'selected' : '' }}>
                                            {{ __('file.specific_doctor') }}
                                        </option>
                                        <option value="primary_provider" {{ old('doctor_selection_mode') == 'primary_provider' ? 'selected' : '' }}>
                                            {{ __('file.primary_care_provider') }}
                                        </option>
                                    </select>
                                    @error('doctor_selection_mode')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.specialization') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="specialization_id" required id="specialization-select"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent transition-all">
                                        <option value="">{{ __('file.select_specialization') }}</option>
                                        @foreach($specializations as $specialization)
                                            <option value="{{ $specialization->id }}" {{ old('specialization_id') == $specialization->id ? 'selected' : '' }}>
                                                {{ $specialization->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialization_id')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div id="specific-doctor-field"
                                class="mt-6 {{ old('doctor_selection_mode') == 'specific' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.select_doctor') }} <span class="text-red-500 doctor-required">*</span>
                                </label>

                                <select name="doctor_id" id="doctor-select" class="tom-select-doctor w-full">
                                    <option value="">{{ __('file.select_specialization_first') }}</option>
                                </select>

                                @error('doctor_id')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.requested_date') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="requested_date" value="{{ old('requested_date') }}" required
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.preferred_start_time') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="requested_start_time" value="{{ old('requested_start_time') }}"
                                        required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.preferred_time_range_start') }}
                                    </label>
                                    <input type="time" name="preferred_time_range_start"
                                        value="{{ old('preferred_time_range_start') }}"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.preferred_time_range_end') }}
                                    </label>
                                    <input type="time" name="preferred_time_range_end"
                                        value="{{ old('preferred_time_range_end') }}"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.duration_minutes') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="duration_minutes" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent transition-all">
                                        <option value="15" {{ old('duration_minutes', 30) == 15 ? 'selected' : '' }}>15
                                            {{ __('file.minutes') }}
                                        </option>
                                        <option value="30" {{ old('duration_minutes', 30) == 30 ? 'selected' : '' }}>30
                                            {{ __('file.minutes') }}
                                        </option>
                                        <option value="45" {{ old('duration_minutes', 30) == 45 ? 'selected' : '' }}>45
                                            {{ __('file.minutes') }}
                                        </option>
                                        <option value="60" {{ old('duration_minutes', 30) == 60 ? 'selected' : '' }}>60
                                            {{ __('file.minutes') }}
                                        </option>
                                        <option value="90" {{ old('duration_minutes', 30) == 90 ? 'selected' : '' }}>90
                                            {{ __('file.minutes') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.reason_for_visit') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reason_for_visit" rows="4" required
                                    placeholder="{{ __('file.reason_for_visit_placeholder') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('reason_for_visit') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.additional_notes') }}
                                </label>
                                <textarea name="notes" rows="4" placeholder="{{ __('file.additional_notes_placeholder') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.create_request') }}
                </button>
                <a href="{{ route('appointment_requests.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .dark .ts-wrapper .ts-control {
            background-color: transparent !important;
            border-color: rgb(75 85 99) !important;
            color: white !important;
        }

        .dark .ts-wrapper .ts-control input {
            color: white !important;
        }

        .dark .ts-wrapper .ts-dropdown {
            background-color: rgb(31 41 55) !important;
            border-color: rgb(75 85 99) !important;
        }

        .dark .ts-wrapper .ts-dropdown .option {
            color: rgb(229 231 235) !important;
        }

        .dark .ts-wrapper .ts-dropdown .option:hover,
        .dark .ts-wrapper .ts-dropdown .option.active {
            background-color: rgb(55 65 81) !important;
            color: white !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('.tom-select-patient', {
                maxItems: 1,
                placeholder: '{{ __('file.search_patient') }}',
                render: {
                    option: function (data, escape) { return `<div class="py-2 px-3">${escape(data.text)}</div>`; },
                    item: function (data, escape) { return `<div>${escape(data.text)}</div>`; }
                }
            });

            const doctorModeSelect = document.getElementById('doctor-selection-mode');
            const specificDoctorField = document.getElementById('specific-doctor-field');
            const specializationSelect = document.getElementById('specialization-select');
            const doctorSelectEl = document.getElementById('doctor-select');

            let doctorTomSelect = null;

            function initializeDoctorTomSelect() {
                if (doctorTomSelect) return;

                doctorTomSelect = new TomSelect('#doctor-select', {
                    maxItems: 1,
                    placeholder: '{{ __('file.select_specialization_first') }}',
                    valueField: 'value',
                    labelField: 'text',
                    searchField: ['text'],
                    render: {
                        option: function (data, escape) {
                            return `<div class="py-2 px-3">${escape(data.text)}</div>`;
                        },
                        item: function (data, escape) {
                            return `<div>${escape(data.text)}</div>`;
                        }
                    }
                });
            }

            function setDoctorPlaceholder(text) {
                if (doctorTomSelect) {
                    const control = doctorTomSelect.control;
                    const input = control.querySelector('input[placeholder]') ||
                        control.querySelector('.ts-control > div');
                    if (input) {
                        input.setAttribute('placeholder', text);
                    }
                }
            }

            function loadDoctors(specializationId) {
                if (!doctorTomSelect) initializeDoctorTomSelect();

                doctorTomSelect.clear();
                doctorTomSelect.clearOptions();

                if (!specializationId) {
                    setDoctorPlaceholder('{{ __('file.select_specialization_first') }}');
                    return;
                }

                setDoctorPlaceholder('{{ __('file.loading_doctors') }}...');

                // Use the named route with Laravel's route() helper
                fetch(`{{ route('doctors.by_specialization', ':specialization_id') }}`.replace(':specialization_id', specializationId))
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        doctorTomSelect.addOptions(data);
                        setDoctorPlaceholder('{{ __('file.select_doctor') }}');

                        @if(old('doctor_id'))
                            const oldDoctorId = '{{ old('doctor_id') }}';
                            if (data.some(opt => opt.value == oldDoctorId)) {
                                doctorTomSelect.addItem(oldDoctorId);
                            }
                        @endif
                        })
                    .catch(() => {
                        setDoctorPlaceholder('{{ __('file.no_doctors_found') }}');
                    });
            }

            function toggleDoctorField() {
                if (doctorModeSelect.value === 'specific') {
                    specificDoctorField.classList.remove('hidden');
                    doctorSelectEl.setAttribute('required', 'required');
                    initializeDoctorTomSelect();

                    const specId = specializationSelect.value;
                    if (specId) {
                        loadDoctors(specId);
                    } else {
                        setDoctorPlaceholder('{{ __('file.select_specialization_first') }}');
                    }
                } else {
                    specificDoctorField.classList.add('hidden');
                    doctorSelectEl.removeAttribute('required');
                    if (doctorTomSelect) {
                        doctorTomSelect.clear();
                        doctorTomSelect.clearOptions();
                        setDoctorPlaceholder('{{ __('file.select_specialization_first') }}');
                    }
                }
            }

            specializationSelect.addEventListener('change', function () {
                if (doctorModeSelect.value === 'specific') {
                    loadDoctors(this.value);
                }
            });

            doctorModeSelect.addEventListener('change', toggleDoctorField);

            toggleDoctorField();

            @if(old('specialization_id') && old('doctor_selection_mode') == 'specific')
                loadDoctors('{{ old('specialization_id') }}');
            @endif
            });
    </script>
@endsection