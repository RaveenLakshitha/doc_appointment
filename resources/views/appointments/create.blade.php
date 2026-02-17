@extends('layouts.app')

@section('title', __('file.schedule_appointment'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('appointments.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.appointments') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.schedule_appointment') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.schedule_new_appointment') }}</h1>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-8">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
            <div class="space-y-6">

                <!-- Patient Selection with Add Button -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.patient') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="max-w-full">
                            <select name="patient_id" id="patient-select" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                <!-- Populated dynamically by Select2 -->
                            </select>
                        </div>
                        @error('patient_id')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <button type="button" id="add-patient-btn"
                                class="mt-3 inline-flex items-center justify-center px-4 py-2 bg-gray-900 dark:bg-white border border-gray-900 dark:border-gray-300 text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('file.add_patient') }}
                        </button>
                    </div>

                    <!-- Appointment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.appointment_type') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                                {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-gray-900 bg-gray-100 dark:border-gray-400 dark:bg-gray-700/70' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_SPECIFIC }}"
                                       class="text-gray-900 focus:ring-gray-900" 
                                       {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('file.specific_doctor') }}</span>
                            </label>

                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                                {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'border-gray-900 bg-gray-100 dark:border-gray-400 dark:bg-gray-700/70' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_ANY }}"
                                       class="text-gray-900 focus:ring-gray-900" 
                                       {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('file.any_doctor') }}</span>
                            </label>
                        </div>
                        @error('appointment_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Specialization -->
                <div id="specialization-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.specialization') }} <span id="spec-required" class="text-red-500 hidden">*</span>
                    </label>
                    <select name="specialization_id" id="specialization_id"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                        <option value="">{{ __('file.select_specialization') }}</option>
                        @foreach($specializations as $spec)
                            <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                {{ $spec->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('specialization_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Specific Doctor -->
                <div id="doctor-group" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.doctor') }} <span id="doctor-required" class="text-red-500 hidden">*</span>
                    </label>
                    <select name="doctor_id" id="doctor_id"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                        <option value="">{{ __('file.select_doctor') }}</option>
                    </select>
                    @error('doctor_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Reason for Visit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.reason_for_visit') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason_for_visit" rows="5" required
                              class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white resize-none"
                              placeholder="{{ __('file.describe_reason') }}">{{ old('reason_for_visit') }}</textarea>
                    @error('reason_for_visit') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Patient Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.patient_notes') }}
                    </label>
                    <textarea name="patient_notes" rows="4"
                              class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white resize-none"
                              placeholder="{{ __('file.optional_notes') }}">{{ old('patient_notes') }}</textarea>
                </div>

            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-300 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.create_appointment_request') }}
            </button>

            <a href="{{ route('appointments.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
</div>

<!-- Add Patient Drawer -->
<div id="patient-drawer" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50 dark:bg-black/70 transition-opacity" id="drawer-overlay"></div>
    
    <!-- Drawer -->
    <div class="absolute right-0 top-0 h-full w-full sm:w-[480px] bg-white dark:bg-gray-900 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out" id="drawer-content">
        <div class="flex flex-col h-full">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_patient') }}</h2>
                <button type="button" id="close-drawer" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <div class="flex-1 overflow-y-auto px-6 py-6">
                <form id="add-patient-form" class="space-y-5">
                    @csrf

                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.first_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                               placeholder="{{ __('file.enter_first_name') }}">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.last_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                               placeholder="{{ __('file.enter_last_name') }}">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.date_of_birth') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.gender') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                            <option value="">{{ __('file.select_gender') }}</option>
                            <option value="male">{{ __('file.male') }}</option>
                            <option value="female">{{ __('file.female') }}</option>
                            <option value="other">{{ __('file.other') }}</option>
                        </select>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.phone') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                               placeholder="{{ __('file.enter_phone') }}">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.email') }}
                        </label>
                        <input type="email" name="email"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                               placeholder="{{ __('file.enter_email') }}">
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.address') }}
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-800 dark:text-white resize-none"
                                  placeholder="{{ __('file.enter_address') }}"></textarea>
                    </div>

                    <!-- Error message container -->
                    <div id="drawer-error" class="hidden p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400"></p>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex gap-3">
                    <button type="submit" form="add-patient-form" id="save-patient-btn"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 dark:bg-white border border-gray-900 dark:border-gray-300 text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('file.save_patient') }}
                    </button>
                    <button type="button" id="cancel-drawer"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        {{ __('file.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
document.addEventListener('DOMContentLoaded', function () {

    // Appointment type / doctor visibility logic
    const typeRadios          = document.querySelectorAll('input[name="appointment_type"]');
    const specializationGroup = document.getElementById('specialization-group');
    const doctorGroup         = document.getElementById('doctor-group');
    const specRequiredMark    = document.getElementById('spec-required');
    const doctorRequiredMark  = document.getElementById('doctor-required');
    const specializationSelect = document.getElementById('specialization_id');
    const doctorSelect        = document.getElementById('doctor_id');

    function getCurrentType() {
        return document.querySelector('input[name="appointment_type"]:checked')?.value || '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
    }

    function updateFormVisibility() {
        const type = getCurrentType();
        const isSpecific = type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
        const isAny      = type === '{{ \App\Models\Appointment::TYPE_ANY }}';

        specializationGroup.classList.toggle('hidden', !isAny);
        doctorGroup.classList.toggle('hidden', !isSpecific);

        specRequiredMark.classList.toggle('hidden', !isAny);
        doctorRequiredMark.classList.toggle('hidden', !isSpecific);

        specializationSelect.required = isAny;
        doctorSelect.required = isSpecific;

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
    updateFormVisibility();

    // Patient Select2 initialization
    $('#patient-select').select2({
    placeholder: "{{ __('file.select_patient') }}",
    allowClear: true,
    minimumInputLength: 1,
    ajax: {
        url: "{{ route('patients.search') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term || '',
                page: params.page || 1
            };
        },
        processResults: function (data) {
            return {
                results: data.results,
                pagination: {
                    more: data.pagination.more
                }
            };
        },
        cache: true
    }
});

    // Handle old('patient_id') after validation fail
    @if(old('patient_id'))
        const oldPatientId = '{{ old('patient_id') }}';
        if (oldPatientId) {
            fetch("{{ url('/patients') }}/" + oldPatientId + "/select2")
                .then(response => response.json())
                .then(data => {
                    const option = new Option(data.text, data.id, true, true);
                    $('#patient-select').append(option).trigger('change');
                })
                .catch(error => console.error('Error preloading old patient:', error));
        }
    @endif

    // Drawer functionality
    const drawer = document.getElementById('patient-drawer');
    const drawerContent = document.getElementById('drawer-content');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const addPatientBtn = document.getElementById('add-patient-btn');
    const closeDrawerBtn = document.getElementById('close-drawer');
    const cancelDrawerBtn = document.getElementById('cancel-drawer');
    const addPatientForm = document.getElementById('add-patient-form');
    const drawerError = document.getElementById('drawer-error');

    function openDrawer() {
        drawer.classList.remove('hidden');
        setTimeout(() => {
            drawerContent.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawerContent.classList.add('translate-x-full');
        setTimeout(() => {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
            addPatientForm.reset();
            drawerError.classList.add('hidden');
        }, 300);
    }

    addPatientBtn.addEventListener('click', openDrawer);
    closeDrawerBtn.addEventListener('click', closeDrawer);
    cancelDrawerBtn.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    // Handle form submission
    addPatientForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const saveBtn = document.getElementById('save-patient-btn');
        const originalText = saveBtn.innerHTML;
        
        // Disable button and show loading
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("file.saving") }}';
        
        try {
            const response = await fetch('{{ route("patients.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // Add new patient to select2
                const newOption = new Option(
                    data.patient.full_name + ' (MRN: ' + (data.patient.medical_record_number || 'N/A') + ')',
                    data.patient.id,
                    true,
                    true
                );
                $('#patient-select').append(newOption).trigger('change');
                
                closeDrawer();
                
                // Show success message (you can customize this)
                alert('{{ __("file.patient_added_successfully") }}');
            } else {
                // Show error
                drawerError.classList.remove('hidden');
                drawerError.querySelector('p').textContent = data.message || '{{ __("file.error_adding_patient") }}';
            }
        } catch (error) {
            drawerError.classList.remove('hidden');
            drawerError.querySelector('p').textContent = '{{ __("file.error_adding_patient") }}';
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });

    // Close drawer on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !drawer.classList.contains('hidden')) {
            closeDrawer();
        }
    });
});
</script>

<style>
/* Select2 Dark Mode Styling */
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    background-color: transparent;
    border-color: #d4d4d4;
    height: 42px;
    display: flex;
    align-items: center;
}

.dark .select2-container--default .select2-selection--single {
    background-color: #171717;
    border-color: #525252;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px;
    padding-left: 12px;
}

.dark .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: white;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #737373;
}

.dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #a3a3a3;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}

.dark .select2-dropdown {
    background-color: #171717;
    border-color: #525252;
}

.select2-dropdown {
    background-color: white;
    border-color: #d4d4d4;
}

.dark .select2-container--default .select2-results__option {
    color: white;
    background-color: #171717;
}

.select2-container--default .select2-results__option {
    color: #0f0f0f;
    background-color: white;
}

.dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #262626;
    color: white;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #f5f5f5;
    color: #0f0f0f;
}

.dark .select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #404040;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #e5e5e5;
}

.dark .select2-container--default .select2-search--dropdown .select2-search__field {
    background-color: #0f0f0f;
    border-color: #525252;
    color: white;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    background-color: white;
    border-color: #d4d4d4;
    color: #0f0f0f;
}

.dark .select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
    color: #a3a3a3;
}

.select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
    color: #737373;
}

.dark .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #737373;
    outline: none;
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #0f0f0f;
    outline: none;
}

.dark .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #737373;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #0f0f0f;
}
</style>
@endsection