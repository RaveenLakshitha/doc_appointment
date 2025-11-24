{{-- resources/views/appointments/create.blade.php --}}
@extends('layouts.app')

@section('title', __('file.schedule_appointment'))

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('appointments.index') }}"
               class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.appointments') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.schedule_appointment') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.new_appointment') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_new_appointment_record') }}</p>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-8" x-data="appointmentForm()">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 lg:p-8">
                <div class="space-y-8">

                    <!-- Select Patient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.select_patient') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1 relative">
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchPatients"
                                       placeholder="{{ __('file.search_by_name_or_email') }}"
                                       class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                                       autocomplete="off">

                                <div x-show="showDropdown && patients.length"
                                     class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                                    <template x-for="patient in patients" :key="patient.id">
                                        <div @click="selectPatient(patient)"
                                             class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-700 last:border-0">
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="patient.full_name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <span x-text="patient.email"></span>
                                                <span x-show="patient.phone"> • </span>
                                                <span x-text="patient.phone"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <a href="{{ route('patients.create') }}"
                               class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-medium whitespace-nowrap">
                                {{ __('file.register_new_patient') }}
                            </a>
                        </div>

                        <div x-show="selectedPatient" class="mt-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                            <span class="text-sm font-medium text-emerald-800 dark:text-emerald-300">
                                {{ __('file.selected') }}: <strong x-text="selectedPatient.full_name"></strong>
                            </span>
                        </div>

                        <input type="hidden" name="patient_id" x-model="selectedPatientId" required>
                        @error('patient_id')
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor, Date, Time -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.doctor') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="doctor_id" x-model="doctorId" @change="loadAvailableSlots" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_doctor') }}</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">
                                        {{ $doctor->full_name }} ({{ $doctor->primary_specialty ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" x-model="selectedDate" @change="loadAvailableSlots" required
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                            @error('date')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.time') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="time" x-model="selectedTime" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_time') }}</option>
                                <template x-for="slot in availableSlots" :key="slot">
                                    <option :value="slot" x-text="slot"></option>
                                </template>
                            </select>
                            <p x-show="loadingSlots" class="mt-1 text-xs text-indigo-600 animate-pulse">
                                {{ __('file.loading_slots') }}...
                            </p>
                        </div>
                    </div>

                    <!-- Appointment Type & Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.appointment_type') }}
                            </label>
                            <select name="appointment_type"
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="consultation">{{ __('file.consultation') }}</option>
                                <option value="follow_up">{{ __('file.follow_up') }}</option>
                                <option value="procedure">{{ __('file.procedure') }}</option>
                                <option value="checkup">{{ __('file.checkup') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.duration_minutes') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="duration_minutes" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                                <option value="30">30 {{ __('file.minutes') }}</option>
                                <option value="45">45 {{ __('file.minutes') }}</option>
                                <option value="60" selected>60 {{ __('file.minutes') }}</option>
                                <option value="90">90 {{ __('file.minutes') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reason for Visit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.reason_for_visit') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason_for_visit" rows="4" required
                                  placeholder="{{ __('file.enter_reason') }}"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow resize-none"></textarea>
                        @error('reason_for_visit')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('file.appointment_status') }}
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="status" value="scheduled" checked class="mr-3 text-gray-900 focus:ring-gray-900">
                                <span>{{ __('file.scheduled') }}</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="status" value="tentative" class="mr-3 text-gray-900 focus:ring-gray-900">
                                <span>{{ __('file.tentative_pending') }}</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="status" value="waitlist" class="mr-3 text-gray-900 focus:ring-gray-900">
                                <span>{{ __('file.add_to_waitlist') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.notes_for_staff') }}
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="{{ __('file.additional_notes') }}"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('file.schedule_appointment') }}
            </button>
            <a href="{{ route('appointments.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
function appointmentForm() {
    return {
        searchQuery: '',
        patients: [],
        selectedPatient: null,
        selectedPatientId: '',
        showDropdown: false,
        selectedDate: '{{ now()->format('Y-m-d') }}',
        selectedTime: '',
        doctorId: '',
        availableSlots: [],
        loadingSlots: false,

        searchPatients() {
            if (this.searchQuery.length < 2) {
                this.patients = []; this.showDropdown = false; return;
            }

            fetch(`/api/patients/search?q=${encodeURIComponent(this.searchQuery)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(r => r.json())
            .then(data => {
                this.patients = data;
                this.showDropdown = true;
            })
            .catch(() => {
                this.patients = []; this.showDropdown = false;
            });
        },

        selectPatient(patient) {
            this.selectedPatient = patient;
            this.selectedPatientId = patient.id;
            this.searchQuery = patient.full_name;
            this.showDropdown = false;
        },

        loadAvailableSlots() {
            if (!this.doctorId || !this.selectedDate) {
                this.availableSlots = []; return;
            }

            this.loadingSlots = true;
            this.availableSlots = [];
            this.selectedTime = '';

            fetch(`/api/appointments/slots?doctor_id=${this.doctorId}&date=${this.selectedDate}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(r => r.json())
            .then(slots => {
                this.availableSlots = slots;
                this.loadingSlots = false;
            })
            .catch(() => this.loadingSlots = false);
        }
    }
}
</script>

{{-- Required for CSRF in fetch() --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection