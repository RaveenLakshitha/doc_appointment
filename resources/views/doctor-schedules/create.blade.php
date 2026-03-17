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

                        <!-- Room Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.room') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="room_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                                                   focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                <option value="">{{ __('file.select_room') }}</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }}{{ $room->department ? ' - ' . $room->department->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Time Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.start_time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                            @error('start_time')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.end_time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="end_time" value="{{ old('end_time') }}" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                            @error('end_time')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Days of the Week -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('file.days_of_week') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-4">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                $oldDays = old('days_of_week', []);
                            @endphp
                            @foreach($days as $day)
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="days_of_week[]" value="{{ $day }}" {{ in_array($day, $oldDays) ? 'checked' : '' }}
                                        class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                        {{ __('file.' . $day) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('days_of_week')
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('days_of_week.*')
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
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