{{-- resources/views/therapists/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Therapist')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('doctors.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Therapists</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">Edit Therapist</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Edit Therapist</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update therapist information</p>
    </div>

    <form method="POST" action="{{ route('therapists.update', $therapist) }}" class="space-y-8">
        @csrf @method('PATCH')

        <!-- Personal Information Section -->
        <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Basic therapist details</p>
            </div>

            <div class="px-6 py-6 space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $therapist->name) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                           placeholder="Enter full name">
                    @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Email & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $therapist->email) }}" required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                               placeholder="therapist@example.com">
                        @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $therapist->phone) }}" required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                               placeholder="+94XXXXXXXXX">
                        @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- License Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        License Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="license_number" value="{{ old('license_number', $therapist->license_number) }}" required
                           class="w-full px-4 py-2.5 text-sm font-mono border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                           placeholder="LIC-XXXXXX">
                    @error('license_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- DOB & Gender -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date of Birth
                        </label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $therapist->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                        @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender
                        </label>
                        <select name="gender"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender', $therapist->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $therapist->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $therapist->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Address
                    </label>
                    <textarea name="address" rows="3"
                              class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow resize-none"
                              placeholder="Enter full address">{{ old('address', $therapist->address) }}</textarea>
                    @error('address') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Professional Information</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Specialty, rate, and status</p>
            </div>

            <div class="px-6 py-6 space-y-6">
                <!-- Specialty -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Specialty
                    </label>
                    <input type="text" name="specialty" value="{{ old('specialty', $therapist->specialty) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                           placeholder="e.g. CBT, Family Therapy">
                    @error('specialty') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Hourly Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Hourly Rate (USD)
                    </label>
                    <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $therapist->hourly_rate) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                           placeholder="e.g. 120.00">
                    @error('hourly_rate') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $therapist->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700">
                    </div>
                    <div class="ml-3">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Active Therapist
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Mark this therapist as active in the system
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Therapist
            </button>

            <a href="{{ route('doctors.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection