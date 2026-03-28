@extends('layouts.app')

@section('title', __('file.schedule_appointment'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('appointments.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.appointments') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.schedule_appointment') }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.schedule_new_appointment') }}</h1>
        </div>

        <form method="POST" action="{{ route('appointments.store') }}" class="space-y-8">
            @csrf
            <input type="hidden" name="return_to" value="{{ request('return_to') }}">

            @php
                $lockDoctor = request('lock_doctor') == 1;
                $requestedDoctorId = request('doctor_id');
                $requestedType = request('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC);
            @endphp

            @if($lockDoctor && $requestedDoctorId)
                <div
                    class="bg-indigo-50 dark:bg-transparent dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-6 flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                            {{ __('file.appointment_with') ?? 'Appointment with' }}</h3>
                        <p class="text-lg font-bold text-indigo-700 dark:text-indigo-300">
                            @php
                                $lockedDoctor = \App\Models\Doctor::find($requestedDoctorId);
                            @endphp
                            {{ $lockedDoctor ? 'Dr. ' . $lockedDoctor->full_name : 'Selected Doctor' }}
                        </p>
                    </div>
                    <input type="hidden" name="appointment_type" value="{{ $requestedType }}">
                    <input type="hidden" name="doctor_id" value="{{ $requestedDoctorId }}">
                </div>
            @endif

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.patient') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="max-w-full">
                                <select name="patient_id" id="patient-select" required
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                </select>
                            </div>
                            @error('patient_id')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <button type="button" id="add-patient-btn"
                                class="mt-3 inline-flex items-center justify-center px-4 py-2 bg-gray-900 dark:bg-white border border-gray-900 dark:border-gray-300 text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('file.add_patient') }}
                            </button>
                        </div>

                        <div class="{{ $lockDoctor ? 'bg-gray-50 dark:bg-gray-800/50 rounded-xl p-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.appointment_type') }} <span class="text-red-500">*</span>
                                @if($lockDoctor)
                                    <span
                                        class="ml-2 text-xs font-normal text-indigo-600 dark:text-indigo-400 italic">({{ __('file.locked') ?? 'Locked' }})</span>
                                @endif
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="appointment-type-cards">
                                <label
                                    class="relative flex flex-col p-4 border rounded-xl {{ $lockDoctor ? 'cursor-not-allowed opacity-70' : 'cursor-pointer hover:border-gray-300 dark:hover:border-gray-600' }} transition-all duration-200 group
                                                {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-gray-900 bg-gray-50 dark:bg-gray-800 dark:border-gray-300' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" name="appointment_type" {{ $lockDoctor ? 'disabled' : '' }}
                                        value="{{ \App\Models\Appointment::TYPE_SPECIFIC }}" class="absolute opacity-0" {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'checked' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('file.specific_doctor') }}</span>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-gray-900 bg-gray-900 dark:border-white dark:bg-white' : 'border-gray-300 dark:border-gray-600' }}">
                                            <div
                                                class="w-2 h-2 rounded-full {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? (old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'bg-white dark:bg-gray-900' : 'bg-white') : 'bg-white' }} {{ old('appointment_type', \App\Models\Appointment::TYPE_SPECIFIC) == \App\Models\Appointment::TYPE_SPECIFIC ? 'block' : 'hidden' }}">
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label
                                    class="relative flex flex-col p-4 border rounded-xl {{ $lockDoctor ? 'cursor-not-allowed opacity-70' : 'cursor-pointer hover:border-gray-300 dark:hover:border-gray-600' }} transition-all duration-200 group
                                                {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'border-gray-900 bg-gray-50 dark:bg-gray-800 dark:border-gray-300' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" name="appointment_type" {{ $lockDoctor ? 'disabled' : '' }}
                                        value="{{ \App\Models\Appointment::TYPE_ANY }}" class="absolute opacity-0" {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'checked' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('file.any_doctor') }}</span>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'border-gray-900 bg-gray-900 dark:border-white dark:bg-white' : 'border-gray-300 dark:border-gray-600' }}">
                                            <div
                                                class="w-2 h-2 rounded-full {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'bg-white dark:bg-gray-900' : 'bg-white' }} {{ old('appointment_type') == \App\Models\Appointment::TYPE_ANY ? 'block' : 'hidden' }}">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('appointment_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>
                    </div>

                    <div id="specialization-group" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.specialization') }} <span id="spec-required" class="text-red-500 hidden">*</span>
                        </label>
                        <select name="specialization_id" id="specialization_id"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_specialization') }}</option>
                            @foreach($specializations as $spec)
                                <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialization_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                        </p> @enderror

                        {{-- Available Therapists & Slots Panel --}}
                        <div id="availability-panel" class="hidden mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('file.available_therapists_and_slots') ?? 'Available Therapists & Slots' }}
                            </h3>
                            <div id="availability-content" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="col-span-full py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                    {{ __('file.loading_availability') ?? 'Loading availability...' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="doctor-group"
                        class="{{ $lockDoctor ? 'bg-gray-50 dark:bg-gray-800/50 rounded-xl p-2' : 'hidden' }} space-y-4">
                        @if($lockDoctor)
                            <div class="px-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.doctor') }} <span class="text-red-500">*</span>
                                    <span
                                        class="ml-2 text-xs font-normal text-indigo-600 dark:text-indigo-400 italic">({{ __('file.locked') ?? 'Locked' }})</span>
                                </label>
                            </div>
                        @endif
                        <div class="{{ $lockDoctor ? 'px-2 pb-2' : '' }}">
                            @if(!$lockDoctor)
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.doctor') }} <span id="doctor-required" class="text-red-500 hidden">*</span>
                                </label>
                            @endif
                            <select id="doctor_id" {{ $lockDoctor ? 'disabled' : 'name=doctor_id' }}
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow {{ $lockDoctor ? 'opacity-70 pointer-events-none' : '' }}">
                                <option value="">{{ __('file.select_doctor') }}</option>
                            </select>
                            @error('doctor_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>



                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date') }} <span class="text-red-500">*</span></label>
                            
                            <div id="create_available_days_container" class="space-y-3 hidden">
                                <div id="create_available_days" class="flex flex-wrap gap-2">
                                    <!-- Quick select days will go here -->
                                </div>
                                <button type="button" id="toggle_create_date" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('file.pick_another_date') ?? 'Pick another date' }}
                                </button>
                            </div>

                            <div id="create_date_input_group">
                                <input type="date" name="date" id="create_date" value="{{ old('date', $requestedDate ?? date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow [color-scheme:light] dark:[color-scheme:dark]">
                            </div>

                            <div id="create_date_warning" class="mt-2 hidden">
                                <div class="flex items-center p-2 text-xs text-amber-800 border border-amber-200 rounded-lg bg-amber-50 dark:bg-gray-800 dark:text-amber-400 dark:border-amber-900">
                                    <svg class="flex-shrink-0 inline w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                    <span class="sr-only">Warning</span>
                                    <div id="create_date_warning_text">
                                        <!-- Warning message will go here -->
                                    </div>
                                </div>
                            </div>
                            @error('date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div id="slot-group" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.time_slot') }}</label>
                            <select name="slot" id="create_slot" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_slot') }}</option>
                            </select>
                            @error('slot') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Unavailable Times --}}
                        <div id="create_unavailable_container" class="hidden col-span-full mt-2 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-red-600 dark:text-red-400 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('file.unavailable_times') ?? 'Unavailable Times' }}
                            </label>
                            <div id="create_unavailable_list" class="space-y-2">
                                {{-- Booked slots will be injected here --}}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.appointment_time') ?? 'Appointment Time' }} <span class="text-red-500">*</span></label>
                            <input type="time" name="appointment_time" id="appointment_time" value="{{ old('appointment_time') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            @error('appointment_time') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            <p id="time_slot_hint" class="mt-1.5 text-xs text-indigo-600 dark:text-indigo-400 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.duration_minutes') ?? 'Duration' }} <span class="text-red-500">*</span></label>
                            <select name="duration_minutes" id="duration_minutes" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="15" {{ old('duration_minutes') == 15 ? 'selected' : '' }}>15 {{ __('file.min') ?? 'min' }}</option>
                                <option value="30" {{ old('duration_minutes', 30) == 30 ? 'selected' : '' }}>30 {{ __('file.min') ?? 'min' }}</option>
                                <option value="45" {{ old('duration_minutes') == 45 ? 'selected' : '' }}>45 {{ __('file.min') ?? 'min' }}</option>
                                <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }}>1 {{ __('file.hour') ?? 'hr' }}</option>
                            </select>
                            @error('duration_minutes') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div id="room-group" class="hidden">
                            <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.room') }} <span class="text-red-500">*</span></label>
                            <select name="room_id" id="room_id" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_room') ?? 'Select Room' }}</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }} ({{ $room->room_number }})</option>
                                @endforeach
                            </select>
                            @error('room_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.reason_for_visit') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason_for_visit" rows="5" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                            placeholder="{{ __('file.describe_reason') }}">{{ old('reason_for_visit') }}</textarea>
                        @error('reason_for_visit') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                        </p> @enderror
                    </div>


                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.create_appointment_request') }}
                </button>

                <a href="{{ route('appointments.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <div id="patient-drawer" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 dark:bg-black/70 transition-opacity" id="drawer-overlay"></div>

        <div class="absolute right-0 top-0 h-full w-full sm:w-[480px] bg-white dark:bg-gray-900 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out"
            id="drawer-content">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_patient') }}</h2>
                    <button type="button" id="close-drawer"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <form id="add-patient-form" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.first_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="{{ __('file.enter_first_name') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.last_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="{{ __('file.enter_last_name') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.age') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="age" min="0" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="{{ __('file.enter_age') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.gender') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_gender') }}</option>
                                <option value="male">{{ __('file.male') }}</option>
                                <option value="female">{{ __('file.female') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.phone') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" required minlength="7" maxlength="15"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="{{ __('file.enter_phone') }}">
                        </div>



                        <div id="drawer-error"
                            class="hidden p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-600 dark:text-red-400"></p>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex gap-3">
                        <button type="submit" form="add-patient-form" id="save-patient-btn"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 dark:bg-white border border-gray-900 dark:border-gray-300 text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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

            const typeRadios = document.querySelectorAll('input[name="appointment_type"]');
            const specializationGroup = document.getElementById('specialization-group');
            const doctorGroup = document.getElementById('doctor-group');
            const specRequiredMark = document.getElementById('spec-required');
            const doctorRequiredMark = document.getElementById('doctor-required');
            const specializationSelect = document.getElementById('specialization_id');
            const doctorSelect = document.getElementById('doctor_id');


            function getCurrentType() {
                const lockDoctor = {{ $lockDoctor ? 'true' : 'false' }};
                if (lockDoctor) return '{{ $requestedType }}';
                return document.querySelector('input[name="appointment_type"]:checked')?.value || '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
            }



            function updateFormVisibility() {
                const type = getCurrentType();
                const isSpecific = type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
                const isAny = type === '{{ \App\Models\Appointment::TYPE_ANY }}';

                 const slotGroup = document.getElementById('slot-group');
                const roomGroup = document.getElementById('room-group');
                const roomSelect = document.getElementById('room_id');
                const createSlot = document.getElementById('create_slot');

                if (slotGroup) slotGroup.classList.toggle('hidden', !isSpecific);
                if (roomGroup) roomGroup.classList.toggle('hidden', !isSpecific);
                if (roomSelect) roomSelect.required = isSpecific;

                if (isSpecific && createSlot) {
                    createSlot.style.pointerEvents = 'none';
                    createSlot.classList.add('bg-gray-100', 'dark:bg-gray-800', 'opacity-60', 'cursor-not-allowed');
                    createSlot.setAttribute('tabindex', '-1');
                } else if (createSlot) {
                    createSlot.style.pointerEvents = '';
                    createSlot.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'opacity-60', 'cursor-not-allowed');
                    createSlot.removeAttribute('tabindex');
                }

                const timeSlotHint = document.getElementById('time_slot_hint');
                const appointmentTimeInput = document.getElementById('appointment_time');
                if (isAny) {
                    if (timeSlotHint) timeSlotHint.classList.add('hidden');
                    if (appointmentTimeInput) {
                        appointmentTimeInput.removeAttribute('min');
                        appointmentTimeInput.removeAttribute('max');
                        appointmentTimeInput.setCustomValidity('');
                    }
                } else if (isSpecific) {
                    const createSlot = document.getElementById('create_slot');
                    if (createSlot && createSlot.value) {
                        createSlot.dispatchEvent(new Event('change'));
                    }
                }

                const createAvailableDays = document.getElementById('create_available_days');
                if (isAny && createAvailableDays) {
                    createAvailableDays.classList.add('hidden');
                } else if (isSpecific && document.getElementById('doctor_id')?.value) {
                    if (typeof loadCreateDays === 'function') loadCreateDays();
                }

                // Specific doctor: hide specialization, show all doctors
                // Any doctor: show specialization, hide doctors (as per user request)
                if (specializationGroup) specializationGroup.classList.toggle('hidden', !isAny);
                if (doctorGroup) doctorGroup.classList.toggle('hidden', !isSpecific);

                if (specRequiredMark) specRequiredMark.classList.toggle('hidden', !isAny);
                if (doctorRequiredMark) doctorRequiredMark.classList.toggle('hidden', !isSpecific);

                if (specializationSelect) specializationSelect.required = isAny;
                if (doctorSelect) {
                    doctorSelect.required = isSpecific;
                    if (isSpecific) {
                        loadAllDoctors();
                    } else if (isAny) {
                        loadAvailabilityPanel();
                    } else {
                        // In Any Doctor mode, we don't necessarily need to load doctors into the hidden dropdown,
                        // but we can clear it or load filtered ones if needed for background logic.
                        // For now, let's just clear it.
                        doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                        if (document.getElementById('availability-panel')) {
                            document.getElementById('availability-panel').classList.add('hidden');
                        }
                    }
                }
            }

            function loadAvailabilityPanel() {
                const type = getCurrentType();
                if (type !== '{{ \App\Models\Appointment::TYPE_ANY }}') {
                    if (document.getElementById('availability-panel')) {
                        document.getElementById('availability-panel').classList.add('hidden');
                    }
                    return;
                }

                const specId = specializationSelect.value;
                const dateInput = document.getElementById('create_date');
                const date = dateInput ? dateInput.value : '';

                if (!specId || !date) {
                    if (document.getElementById('availability-panel')) {
                        document.getElementById('availability-panel').classList.add('hidden');
                    }
                    return;
                }

                const panel = document.getElementById('availability-panel');
                const content = document.getElementById('availability-content');
                if (panel) panel.classList.remove('hidden');
                if (content) content.innerHTML = '<div class="col-span-full py-8 text-center text-gray-500 italic">{{ __("file.loading_availability") ?? "Loading availability..." }}</div>';

                fetch(`{{ route("appointments.specialization-availability") }}?specialization_id=${specId}&date=${date}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!content) return;
                        if (!data.doctors || data.doctors.length === 0) {
                            content.innerHTML = '<div class="col-span-full py-8 text-center text-gray-400 italic">{{ __("file.no_doctors_found") ?? "No therapists found for this specialization" }}</div>';
                            return;
                        }

                        content.innerHTML = data.doctors.map(doc => `
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-white">${doc.name}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">${data.day_name}</span>
                                </div>
                                <div class="space-y-1.5">
                                    ${doc.on_leave 
                                        ? `<span class="text-xs text-red-500 italic">{{ __("file.doctor_on_leave") ?? "On Leave" }}</span>` 
                                        : doc.slots.length > 0
                                            ? `<div class="flex flex-wrap gap-1.5">
                                                ${doc.slots.map(slot => `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800" title="${slot.label}">${slot.label}</span>`).join('')}
                                               </div>`
                                            : `<span class="text-xs text-gray-400 italic">{{ __("file.no_schedule_available") ?? "No slots available" }}</span>`
                                    }
                                </div>
                            </div>
                        `).join('');
                    })
                    .catch(err => {
                        if (content) content.innerHTML = '<div class="col-span-full py-8 text-center text-red-500 italic">Error loading availability data.</div>';
                    });
            }

            function loadAllDoctors() {
                fetch('{{ route("appointments.doctors.all") }}')
                    .then(response => response.json())
                    .then(doctors => {
                        const currentVal = doctorSelect.value;
                        doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                        doctors.forEach(doc => {
                            const opt = new Option(doc.text, doc.value);
                            if (currentVal == doc.value || '{{ old("doctor_id", $requestedDoctorId) }}' == doc.value) opt.selected = true;
                            doctorSelect.add(opt);
                        });

                        // After loading all doctors, if one is selected, load its attributes
                        if (doctorSelect.value) {
                            loadDoctorAttributes(doctorSelect.value);
                        }
                    });
            }

            function loadFilteredDoctors() {
                const type = getCurrentType();
                if (type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') return;

                const specId = specializationSelect.value;

                let url = '{{ route("appointments.doctors.filtered") }}?';
                if (specId) url += `specialization_id=${specId}&`;

                fetch(url)
                    .then(response => response.json())
                    .then(doctors => {
                        const currentVal = doctorSelect.value;
                        doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                        doctors.forEach(doc => {
                            const opt = new Option(doc.text, doc.value);
                            if (currentVal == doc.value || '{{ old('doctor_id') }}' == doc.value) opt.selected = true;
                            doctorSelect.add(opt);
                        });
                    })
                    .catch(error => console.error('Error loading filtered doctors:', error));
            }

            function loadDoctorAttributes(doctorId) {
                if (!doctorId) {
                    resetAttributes();
                    return;
                }

                fetch(`{{ url('doctors') }}/${doctorId}/attributes`)
                    .then(response => response.json())
                    .then(data => {
                        // Set hidden specialization value for form submission
                        specializationSelect.value = data.specialization_id || '';

                    })
                    .catch(error => {
                        console.error('Error loading doctor attributes:', error);
                        resetAttributes();
                    });
            }

            function resetAttributes() {
                // Future attributes can be reset here
            }

            specializationSelect.addEventListener('change', function() {
                loadFilteredDoctors();
                loadAvailabilityPanel();
            });

            doctorSelect.addEventListener('change', function () {
                loadDoctorAttributes(this.value);
            });


            typeRadios.forEach(r => {
                r.addEventListener('change', function () {
                    // Update UI for all cards
                    document.querySelectorAll('#appointment-type-cards label').forEach(label => {
                        const radio = label.querySelector('input');
                        const isSelected = radio.checked;
                        const radioDisplay = label.querySelector('.w-5.h-5');
                        const radioDot = radioDisplay.querySelector('div');

                        if (isSelected) {
                            label.classList.remove('border-gray-200', 'dark:border-gray-700', 'hover:border-gray-300', 'dark:hover:border-gray-600');
                            label.classList.add('border-gray-900', 'bg-gray-50', 'dark:bg-gray-800', 'dark:border-gray-300');

                            radioDisplay.classList.remove('border-gray-300', 'dark:border-gray-600');
                            radioDisplay.classList.add('border-gray-900', 'bg-gray-900', 'dark:border-white', 'dark:bg-white');
                            radioDot.classList.remove('hidden');
                            radioDot.classList.remove('bg-white');
                            radioDot.classList.add('bg-white', 'dark:bg-gray-900');
                        } else {
                            label.classList.add('border-gray-200', 'dark:border-gray-700', 'hover:border-gray-300', 'dark:hover:border-gray-600');
                            label.classList.remove('border-gray-900', 'bg-gray-50', 'dark:bg-gray-800', 'dark:border-gray-300');

                            radioDisplay.classList.add('border-gray-300', 'dark:border-gray-600');
                            radioDisplay.classList.remove('border-gray-900', 'bg-gray-900', 'dark:border-white', 'dark:bg-white');
                            radioDot.classList.add('hidden');
                        }
                    });
                    updateFormVisibility();
                });
            });
            updateFormVisibility();

            // Initial attribute load if doctor is already selected (e.g., via old value)
            if (doctorSelect && doctorSelect.value) {
                loadDoctorAttributes(doctorSelect.value);
            }

            const createAvailableDays = document.getElementById('create_available_days');
            const createDateSelect = document.getElementById('create_date');
            const createSlotSelect = document.getElementById('create_slot');

            // Handle Exact Time Picker Validation based on selected slot
            const appointmentTimeInput = document.getElementById('appointment_time');
            const timeSlotHint = document.getElementById('time_slot_hint');

            // Helper: remove duration options that exceed the slot end time
            function filterDurationBySlot() {
                const type = getCurrentType();
                const durationSelect = document.getElementById('duration_minutes');
                if (!durationSelect || !createSlotSelect || !appointmentTimeInput) return;

                if (type !== '{{ \App\Models\Appointment::TYPE_SPECIFIC }}' || !createSlotSelect.value || !appointmentTimeInput.value) {
                    // Restore all hidden options
                    if (durationSelect._hiddenBank) {
                        durationSelect._hiddenBank.forEach(opt => durationSelect.add(opt));
                        durationSelect._hiddenBank = [];
                    }
                    return;
                }

                const [slotStart, slotEnd] = createSlotSelect.value.split('|');
                const apptTime = appointmentTimeInput.value; // HH:mm
                if (!apptTime) return;

                const [apptH, apptM] = apptTime.split(':').map(Number);
                const [endH, endM] = slotEnd.split(':').map(Number);
                const maxMinutes = (endH * 60 + endM) - (apptH * 60 + apptM);

                if (!durationSelect._hiddenBank) durationSelect._hiddenBank = [];
                // Restore all first
                durationSelect._hiddenBank.forEach(opt => durationSelect.add(opt));
                durationSelect._hiddenBank = [];

                Array.from(durationSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    if (parseInt(opt.value) > maxMinutes) {
                        if (opt.selected) durationSelect.value = '';
                        durationSelect._hiddenBank.push(opt);
                        durationSelect.remove(opt.index);
                    }
                });
            }

            if (createSlotSelect && appointmentTimeInput) {
                createSlotSelect.addEventListener('change', function() {
                    const type = getCurrentType();
                    if (type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}' && this.value) {
                        const [start, end] = this.value.split('|');
                        appointmentTimeInput.min = start;
                        appointmentTimeInput.max = end;

                        if (appointmentTimeInput.value) {
                            if (appointmentTimeInput.value < start || appointmentTimeInput.value > end) {
                                appointmentTimeInput.setCustomValidity(`{{ __('file.time_must_be_between') }} ${start} {{ __('file.and') }} ${end}`);
                                if (timeSlotHint) {
                                    timeSlotHint.textContent = `{{ __('file.please_select_a_time_between') }} ${start} {{ __('file.and') }} ${end}`;
                                    timeSlotHint.classList.remove('hidden');
                                }
                            } else {
                                appointmentTimeInput.setCustomValidity('');
                                if (timeSlotHint) timeSlotHint.classList.add('hidden');
                            }
                        } else {
                            if (timeSlotHint) {
                                timeSlotHint.textContent = `{{ __('file.please_select_a_time_between') }} ${start} {{ __('file.and') }} ${end}`;
                                timeSlotHint.classList.remove('hidden');
                            }
                        }
                    } else {
                        appointmentTimeInput.removeAttribute('min');
                        appointmentTimeInput.removeAttribute('max');
                        if (timeSlotHint) timeSlotHint.classList.add('hidden');
                        appointmentTimeInput.setCustomValidity('');
                    }
                    filterDurationBySlot();
                });

                appointmentTimeInput.addEventListener('input', function() {
                    const type = getCurrentType();
                    if (type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}' && createSlotSelect.value) {
                        const [start, end] = createSlotSelect.value.split('|');
                        if (this.value && (this.value < start || this.value > end)) {
                            this.setCustomValidity(`{{ __('file.time_must_be_between') }} ${start} {{ __('file.and') }} ${end}`);
                            if (timeSlotHint) {
                                timeSlotHint.textContent = `{{ __('file.please_select_a_time_between') }} ${start} {{ __('file.and') }} ${end}`;
                                timeSlotHint.classList.remove('hidden');
                            }
                        } else {
                            this.setCustomValidity('');
                            if (timeSlotHint && this.value) timeSlotHint.classList.add('hidden');
                        }
                    } else {
                        this.setCustomValidity('');
                    }
                    filterDurationBySlot();
                });
            }

            function loadCreateDays() {
                const doctorId = doctorSelect.value || '{{ $requestedDoctorId }}';
                const type = getCurrentType();
                if (!doctorId || type !== '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') {
                    if (createAvailableDays) {
                        createAvailableDays.classList.add('hidden');
                        createAvailableDays.innerHTML = '';
                    }
                    return;
                }

                fetch(`{{ url('doctors') }}/${doctorId}/available-days`)
                    .then(r => r.json())
                    .then(data => {
                        const days = data.days || [];
                        if (days.length === 0) {
                            createAvailableDays.classList.add('hidden');
                            createAvailableDays.innerHTML = '';
                            return;
                        }

                        createAvailableDays.classList.remove('hidden');
                        createAvailableDays.innerHTML = days.map(day => `
                            <button type="button" 
                                    onclick="selectCreateDate('${day.date}')"
                                    class="px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                                ${day.label}
                            </button>
                        `).join('');
                    })
                    .catch(() => {
                        if (createAvailableDays) createAvailableDays.classList.add('hidden');
                    });
            }

            window.selectCreateDate = function(dateStr) {
                createDateSelect.value = dateStr;
                loadCreateSlots();
            };

            function loadCreateSlots() {
                const doctorId = doctorSelect.value || '{{ $requestedDoctorId }}';
                const date = createDateSelect.value;
                const timeSlotHint = document.getElementById('time_slot_hint');
                if (timeSlotHint) timeSlotHint.classList.add('hidden');

                if (!doctorId || !date) {
                    if (createSlotSelect) createSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") }}</option>';
                    document.getElementById('create_date_warning').classList.add('hidden');
                    return;
                }
                fetch(`{{ url('doctors') }}/${doctorId}/available-slots?date=${date}`)
                    .then(r => r.json())
                    .then(data => {
                        const warningDiv = document.getElementById('create_date_warning');
                        const warningText = document.getElementById('create_date_warning_text');
                        
                        if (data.message && (!data.slots || data.slots.length === 0)) {
                            warningText.textContent = data.message;
                            warningDiv.classList.remove('hidden');
                        } else {
                            warningDiv.classList.add('hidden');
                        }

                        createSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") }}</option>';
                        const slots = data.slots || [];
                        const appointments = data.appointments || [];
                        const oldSlot = '{{ old("slot") }}';

                        if (slots.length === 0 && data.message) {
                            const opt = new Option(data.message, "");
                            opt.disabled = true;
                            createSlotSelect.add(opt);
                        }

                        slots.forEach(slot => {
                            const label = slot.label || `${slot.start} – ${slot.end}`;
                            const val = `${slot.start}|${slot.end}`;
                            const opt = new Option(label, val);
                            if (val === oldSlot) opt.selected = true;
                            createSlotSelect.add(opt);
                        });

                        if (slots.length === 1 && !oldSlot) {
                            createSlotSelect.value = `${slots[0].start}|${slots[0].end}`;
                            createSlotSelect.dispatchEvent(new Event('change'));
                        }

                        renderCreateUnavailableTimes(appointments);
                    });
            }

            function renderCreateUnavailableTimes(appointments) {
                const container = document.getElementById('create_unavailable_container');
                const list = document.getElementById('create_unavailable_list');
                
                if (!container || !list) return;
                
                if (!appointments || appointments.length === 0) {
                    container.classList.add('hidden');
                    return;
                }

                container.classList.remove('hidden');
                list.innerHTML = appointments.map(a => {
                    const formatTime = (t) => {
                        const [h, m] = t.split(':');
                        const hour = parseInt(h);
                        const ampm = hour >= 12 ? 'PM' : 'AM';
                        const h12 = hour % 12 || 12;
                        return `${h12}:${m} ${ampm}`;
                    };

                    return `
                        <div class="flex items-center justify-between px-3 py-2 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg text-xs">
                            <span class="font-medium text-red-700 dark:text-red-300">
                                ${formatTime(a.start)} - ${formatTime(a.end)}
                            </span>
                            <span class="text-[10px] uppercase tracking-wider text-red-500 dark:text-red-400 font-bold">
                                ${a.status}
                            </span>
                        </div>
                    `;
                }).join('');
            }

            if (createDateSelect) {
                createDateSelect.addEventListener('change', function() {
                    loadCreateSlots();
                    loadAvailabilityPanel();
                    checkRoomAvailability();
                });
            }

            if (appointmentTimeInput) {
                appointmentTimeInput.addEventListener('change', checkRoomAvailability);
            }

            document.getElementById('duration_minutes').addEventListener('change', checkRoomAvailability);

            function checkRoomAvailability() {
                const date = document.getElementById('create_date').value;
                const time = document.getElementById('appointment_time').value;
                const duration = document.getElementById('duration_minutes').value;
                const roomSelect = document.getElementById('room_id');

                if (!date || !time) return;

                fetch(`{{ route('appointments.check_room_availability') }}?date=${date}&time=${time}&duration=${duration}`)
                    .then(r => r.json())
                    .then(data => {
                        const busyRooms = data.busy_rooms || [];
                        const currentVal = roomSelect.value;

                        // Restore all hidden options first
                        Array.from(roomSelect.querySelectorAll('option[data-hidden="1"]')).forEach(opt => {
                            opt.removeAttribute('data-hidden');
                            opt.style.display = '';
                            roomSelect.add(opt);
                        });

                        // Store hidden options outside the select so they can be restored
                        if (!roomSelect._hiddenBank) roomSelect._hiddenBank = [];
                        // Restore previously hidden options
                        roomSelect._hiddenBank.forEach(opt => {
                            opt.style.display = '';
                            roomSelect.add(opt);
                        });
                        roomSelect._hiddenBank = [];

                        // Now hide busy ones
                        Array.from(roomSelect.options).forEach(opt => {
                            if (!opt.value) return;
                            const roomId = parseInt(opt.value);
                            if (busyRooms.includes(roomId)) {
                                if (opt.selected) roomSelect.value = '';
                                roomSelect._hiddenBank.push(opt);
                                roomSelect.remove(opt.index);
                            }
                        });
                    });
            }

            doctorSelect.addEventListener('change', function () {
                loadCreateDays();
                loadCreateSlots();
            });

            if (doctorSelect && doctorSelect.value) {
                loadCreateDays();
                loadCreateSlots();
            } else if ('{{ $requestedDoctorId }}') {
                loadCreateDays();
                loadCreateSlots();
            }

            function formatPatientSelection(patient) {
                if (patient.loading) return patient.text;

                const markup = `
                            <div class="flex items-center space-x-3 p-1">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 border border-gray-200 dark:border-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        ${patient.full_name || patient.text.split(' (MRN:')[0]}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-0.5 space-x-2">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-medium">
                                            MRN: ${patient.mrn || patient.text.match(/MRN: ([^)]+)/)?.[1] || 'N/A'}
                                        </span>
                                        ${patient.phone ? `<span>•</span> <span class="flex items-center"><svg class="w-3 h-3 mr-0.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>${patient.phone}</span>` : ''}
                                    </div>
                                </div>
                            </div>`;
                return $(markup);
            }

            function formatPatientSelectionSelection(patient) {
                if (!patient.id) return patient.text;
                // Check if we have the rich text match, otherwise just return the text
                const name = patient.full_name || patient.text.split(' (MRN:')[0];
                const mrn = patient.mrn || patient.text.match(/MRN: ([^)]+)/)?.[1] || 'N/A';
                return `${name} (MRN: ${mrn})`;
            }

            $('#patient-select').select2({
                placeholder: "{{ __('file.select_patient') }}",
                allowClear: true,
                minimumInputLength: 1,
                templateResult: formatPatientSelection,
                templateSelection: formatPatientSelectionSelection,
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

            @if(old('patient_id'))
                const oldPatientId = '{{ old('patient_id') }}';
                if (oldPatientId) {
                    fetch("{{ url('/patients') }}/" + oldPatientId + "/select2")
                        .then(response => response.json())
                        .then(data => {
                            const option = new Option(data.text, data.id, true, true);
                            // Add full data to the option so templateSelection can use it
                            $('#patient-select').append(option).trigger('change');
                            $('#patient-select').val(data.id).trigger({
                                type: 'select2:select',
                                params: {
                                    data: data
                                }
                            });
                        })
                        .catch(error => console.error('Error preloading old patient:', error));
                }
            @endif

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

            addPatientForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const saveBtn = document.getElementById('save-patient-btn');
                const originalText = saveBtn.innerHTML;

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
                        const newOption = new Option(
                            data.patient.full_name + ' (MRN: ' + (data.patient.medical_record_number || 'N/A') + ')',
                            data.patient.id,
                            true,
                            true
                        );
                        $('#patient-select').append(newOption).trigger('change');

                        closeDrawer();
                        alert('{{ __("file.patient_added_successfully") }}');
                    } else {
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

            document.addEventListener('keydown', function (e) {
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
            border-color: #e5e7eb;
            border-width: 1px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #111827;
            ring-width: 1px;
        }

        .dark .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #f3f4f6;
        }

        .dark .select2-container--default .select2-selection--single {
            background-color: #1a1a1a;
            border-color: #374151;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 12px;
            font-size: 0.875rem;
            color: #111827;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f3f4f6;
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