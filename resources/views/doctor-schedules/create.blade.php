@extends('layouts.app')

@section('title', __('file.add_doctor_schedule'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('doctor-schedules.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.doctor_schedules') }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.add_schedule') }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_schedule') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_recurring_schedule') }}</p>
        </div>

        <form method="POST" action="{{ route('doctor-schedules.store') }}" class="space-y-8">
            @csrf

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 space-y-6">
                    <!-- Doctor Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.doctor') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="doctor_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                                   focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                <option value="">{{ __('file.select_doctor') }}</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->getFullNameAttribute() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>

                    <!-- Session Durations -->
                    <div class="pb-6 border-b border-gray-100 dark:border-gray-800">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('file.session_durations') }} ({{ __('file.mins') }})
                        </label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($sessionDurations as $id => $name)
                                <label class="flex items-center space-x-2.5 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-white dark:hover:bg-gray-800 transition-colors group">
                                    <input type="checkbox" name="session_durations[]" value="{{ $name }}"
                                        {{ (is_array(old('session_durations')) && in_array($name, old('session_durations'))) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-900">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                        {{ $name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('session_durations')
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Days of the Week & Rooms -->
                    <div x-data="{
                        selectedDays: {{ json_encode(old('days_of_week', [])) }},
                        commonStart: '',
                        commonEnd: '',
                        times: {
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                '{{ $day }}': { 
                                    start: '{{ old("start_times.{$day}", "") }}', 
                                    end: '{{ old("end_times.{$day}", "") }}' 
                                },
                            @endforeach
                        },
                        toggleDay(day) {
                            if (this.selectedDays.includes(day)) {
                                this.selectedDays = this.selectedDays.filter(d => d !== day);
                            } else {
                                this.selectedDays.push(day);
                            }
                        },
                        applyCommon() {
                            if (!this.commonStart || !this.commonEnd) return;
                            this.selectedDays.forEach(day => {
                                this.times[day].start = this.commonStart;
                                this.times[day].end = this.commonEnd;
                            });
                        }
                    }">
                        <div class="flex flex-col sm:flex-row items-end gap-4 mb-8">
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.helper_start_time') }}
                                </label>
                                <input type="time" x-model="commonStart"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                            </div>
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.helper_end_time') }}
                                </label>
                                <input type="time" x-model="commonEnd"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                            </div>
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <button type="button" @click="applyCommon"
                                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2 h-[38px]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('file.apply_to_all_days') }}
                                </button>
                            </div>
                        </div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('file.days_of_week') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                $oldDays = old('days_of_week', []);
                            @endphp
                            @foreach($days as $day)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 p-3 border rounded-lg dark:border-gray-700 transition-colors"
                                     :class="selectedDays.includes('{{ $day }}') ? 'bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800' : 'bg-white dark:bg-gray-800'">
                                    
                                    <label class="flex items-center space-x-3 cursor-pointer min-w-[140px] mb-2 sm:mb-0">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $day }}" 
                                            @change="toggleDay('{{ $day }}')"
                                            {{ in_array($day, $oldDays) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize"
                                              :class="selectedDays.includes('{{ $day }}') ? 'text-blue-700 dark:text-blue-400' : ''">
                                            {{ __('file.' . $day) }}
                                        </span>
                                    </label>
                                    
                                    <div class="flex-1 grid grid-cols-1 gap-4" x-show="selectedDays.includes('{{ $day }}')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                                        <div class="col-span-1 flex items-center space-x-3">
                                            <div class="flex-1">
                                                <input type="time" name="start_times[{{ $day }}]" x-model="times['{{ $day }}'].start" :required="selectedDays.includes('{{ $day }}')"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                                @error("start_times.{$day}")
                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="flex-shrink-0 text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </div>
                                            <div class="flex-1">
                                                <input type="time" name="end_times[{{ $day }}]" x-model="times['{{ $day }}'].end" :required="selectedDays.includes('{{ $day }}')"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                                @error("end_times.{$day}")
                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('days_of_week')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Validity Period (Optional) -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('file.validity_period') }} ({{ __('file.optional') }})
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.valid_from') }}
                                </label>
                                <input type="date" name="valid_from" value="{{ old('valid_from') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                      focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent 
                                                      dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('valid_from')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.valid_until') }}
                                </label>
                                <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                      focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent 
                                                      dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('valid_until')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('file.validity_help') }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('file.active_schedule') }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.create_schedule') }}
                </button>
                <a href="{{ route('doctor-schedules.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection