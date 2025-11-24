{{-- resources/views/patients/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Patient')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('patients.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Patients</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">Add Patient</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Patient</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new patient record in the system</p>
    </div>

    <form method="POST" action="{{ route('patients.store') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('personal')" id="tab-personal"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">Personal Information</span>
                            <span class="sm:hidden">Personal</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('medical')" id="tab-medical"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="hidden sm:inline">Medical Information</span>
                            <span class="sm:hidden">Medical</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('insurance')" id="tab-insurance"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="hidden sm:inline">Insurance & Billing</span>
                            <span class="sm:hidden">Insurance</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('consent')" id="tab-consent"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="hidden sm:inline">Consent & Documents</span>
                            <span class="sm:hidden">Consent</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div id="content-personal" class="tab-content">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="First name">
                                @error('first_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="Middle name">
                                @error('middle_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="Last name">
                                @error('last_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-gray-500 transition-shadow">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Marital Status</label>
                            <select name="marital_status"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="">Select status</option>
                                <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('marital_status') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea name="address" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Street, apartment, etc.">{{ old('address') }}</textarea>
                            @error('address') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="City">
                                @error('city') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province</label>
                                <input type="text" name="state" value="{{ old('state') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="State">
                                @error('state') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP/Postal Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="ZIP code">
                                @error('zip_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="+1234567890">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alternative Phone</label>
                                <input type="text" name="alternative_phone" value="{{ old('alternative_phone') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="+1234567890">
                                @error('alternative_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                   placeholder="email@example.com">
                            @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Contact Method</label>
                            <select name="preferred_contact_method"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('preferred_contact_method') == 'sms' ? 'selected' : '' }}>SMS</option>
                            </select>
                            @error('preferred_contact_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Full name">
                                    @error('emergency_contact_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="e.g. Spouse, Parent">
                                    @error('emergency_contact_relationship') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="+1234567890">
                                    @error('emergency_contact_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input type="email" name="emergency_contact_email" value="{{ old('emergency_contact_email') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="contact@example.com">
                                    @error('emergency_contact_email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medical Record Number <span class="text-red-500">*</span></label>
                            <input type="text" name="medical_record_number" value="{{ old('medical_record_number') }}" required
                                   class="w-full px-2 py-2 text-sm font-mono border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                   placeholder="MRN-XXXX-XXXX">
                            @error('medical_record_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-medical" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Type</label>
                                <select name="blood_type"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    <option value="">Select</option>
                                    <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                @error('blood_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Height (cm)</label>
                                <input type="number" name="height_cm" value="{{ old('height_cm') }}" min="50" max="250"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="170">
                                @error('height_cm') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weight (kg)</label>
                                <input type="number" name="weight_kg" value="{{ old('weight_kg') }}" min="20" max="300" step="0.1"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="70.5">
                                @error('weight_kg') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allergies (comma-separated)</label>
                            <textarea name="allergies" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Penicillin, Shellfish, Latex">{{ old('allergies') ? implode(', ', old('allergies')) : '' }}</textarea>
                            @error('allergies') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Medications (comma-separated)</label>
                            <textarea name="current_medications" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Metformin 500mg, Aspirin 81mg">{{ old('current_medications') ? implode(', ', old('current_medications')) : '' }}</textarea>
                            @error('current_medications') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Chronic Conditions (comma-separated)</label>
                            <textarea name="chronic_conditions" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Diabetes Type 2, Hypertension">{{ old('chronic_conditions') ? implode(', ', old('chronic_conditions')) : '' }}</textarea>
                            @error('chronic_conditions') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Past Surgeries (one per line: Surgery - Date)</label>
                            <textarea name="past_surgeries" rows="4"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Appendectomy - 2018-03-15">{{ old('past_surgeries') ? implode("\n", old('past_surgeries')) : '' }}</textarea>
                            @error('past_surgeries') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Hospitalizations (one per line: Reason - Date)</label>
                            <textarea name="previous_hospitalizations" rows="4"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="Pneumonia - 2020-11-10">{{ old('previous_hospitalizations') ? implode("\n", old('previous_hospitalizations')) : '' }}</textarea>
                            @error('previous_hospitalizations') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Family Medical History</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_diabetes" value="1" {{ old('family_history_diabetes') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Diabetes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_hypertension" value="1" {{ old('family_history_hypertension') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Hypertension</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_heart_disease" value="1" {{ old('family_history_heart_disease') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Heart Disease</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_cancer" value="1" {{ old('family_history_cancer') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cancer</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_asthma" value="1" {{ old('family_history_asthma') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Asthma</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="family_history_mental_health" value="1" {{ old('family_history_mental_health') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mental Health Conditions</span>
                                </label>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                                <textarea name="family_history_notes" rows="3"
                                          class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                          placeholder="Any other relevant family history">{{ old('family_history_notes') }}</textarea>
                                @error('family_history_notes') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Lifestyle</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Smoking Status</label>
                                    <select name="smoking_status"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                        <option value="never" {{ old('smoking_status') == 'never' ? 'selected' : '' }}>Never</option>
                                        <option value="former" {{ old('smoking_status') == 'former' ? 'selected' : '' }}>Former</option>
                                        <option value="current" {{ old('smoking_status') == 'current' ? 'selected' : '' }}>Current</option>
                                    </select>
                                    @error('smoking_status') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alcohol Consumption</label>
                                    <select name="alcohol_consumption"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                        <option value="none" {{ old('alcohol_consumption') == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="occasional" {{ old('alcohol_consumption') == 'occasional' ? 'selected' : '' }}>Occasional</option>
                                        <option value="moderate" {{ old('alcohol_consumption') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                        <option value="heavy" {{ old('alcohol_consumption') == 'heavy' ? 'selected' : '' }}>Heavy</option>
                                    </select>
                                    @error('alcohol_consumption') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exercise Frequency</label>
                                    <select name="exercise_frequency"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                        <option value="never" {{ old('exercise_frequency') == 'never' ? 'selected' : '' }}>Never</option>
                                        <option value="rarely" {{ old('exercise_frequency') == 'rarely' ? 'selected' : '' }}>Rarely</option>
                                        <option value="weekly" {{ old('exercise_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="daily" {{ old('exercise_frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    </select>
                                    @error('exercise_frequency') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dietary Habits</label>
                                    <input type="text" name="dietary_habits" value="{{ old('dietary_habits') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Vegetarian, Low-carb, etc.">
                                    @error('dietary_habits') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-insurance" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Primary Insurance</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provider</label>
                                    <input type="text" name="primary_insurance_provider" value="{{ old('primary_insurance_provider') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Insurance company">
                                    @error('primary_insurance_provider') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Policy Number</label>
                                    <input type="text" name="primary_policy_number" value="{{ old('primary_policy_number') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="POL123456789">
                                    @error('primary_policy_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group Number</label>
                                    <input type="text" name="primary_group_number" value="{{ old('primary_group_number') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="GRP987">
                                    @error('primary_group_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Policy Holder Name</label>
                                    <input type="text" name="primary_policy_holder_name" value="{{ old('primary_policy_holder_name') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Full name">
                                    @error('primary_policy_holder_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship to Patient</label>
                                    <input type="text" name="primary_relationship_to_patient" value="{{ old('primary_relationship_to_patient') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Self, Spouse, Parent">
                                    @error('primary_relationship_to_patient') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance Phone</label>
                                    <input type="text" name="primary_insurance_phone" value="{{ old('primary_insurance_phone') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="+1-800-555-1234">
                                    @error('primary_insurance_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Secondary Insurance (Optional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provider</label>
                                    <input type="text" name="secondary_insurance_provider" value="{{ old('secondary_insurance_provider') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="Insurance company">
                                    @error('secondary_insurance_provider') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Policy Number</label>
                                    <input type="text" name="secondary_policy_number" value="{{ old('secondary_policy_number') }}"
                                           class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                           placeholder="POL987654321">
                                    @error('secondary_policy_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Billing Preferences</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Billing Method</label>
                                <select name="preferred_billing_method"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    <option value="insurance_first" {{ old('preferred_billing_method') == 'insurance_first' ? 'selected' : '' }}>Insurance First</option>
                                    <option value="self_pay" {{ old('preferred_billing_method') == 'self_pay' ? 'selected' : '' }}>Self Pay</option>
                                </select>
                                @error('preferred_billing_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Methods (select all that apply)</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="credit_card" {{ is_array(old('payment_methods')) && in_array('credit_card', old('payment_methods')) ? 'checked' : '' }}
                                               class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Credit Card</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="debit_card" {{ is_array(old('payment_methods')) && in_array('debit_card', old('payment_methods')) ? 'checked' : '' }}
                                               class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Debit Card</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="cash" {{ is_array(old('payment_methods')) && in_array('cash', old('payment_methods')) ? 'checked' : '' }}
                                               class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cash</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="bank_transfer" {{ is_array(old('payment_methods')) && in_array('bank_transfer', old('payment_methods')) ? 'checked' : '' }}
                                               class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Bank Transfer</span>
                                    </label>
                                </div>
                                @error('payment_methods') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-consent" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Photo</label>
                            <input type="file" name="profile_photo" accept="image/*"
                                   class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            @error('profile_photo') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Communication Preferences</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_appointment_reminders" value="1" {{ old('receive_appointment_reminders') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Receive appointment reminders</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_lab_results" value="1" {{ old('receive_lab_results') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Receive lab results via email</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_prescription_notifications" value="1" {{ old('receive_prescription_notifications') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Receive prescription refill notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_newsletter" value="1" {{ old('receive_newsletter') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Receive clinic newsletter</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Consent Forms</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="consent_hipaa" value="1" {{ old('consent_hipaa') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">I have read and agree to the HIPAA Privacy Notice</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="consent_treatment" value="1" {{ old('consent_treatment') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">I consent to medical treatment</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="consent_financial" value="1" {{ old('consent_financial') ? 'checked' : '' }}
                                           class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">I agree to the financial responsibility policy</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Additional Documents</h3>
                            <input type="file" name="additional_documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Upload ID, insurance card, referral, etc.</p>
                            @error('additional_documents') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Patient
            </button>
            <a href="{{ route('doctors.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove(
            'text-gray-900',
            'dark:text-white',
            'border-b-2',
            'border-gray-900',
            'dark:border-gray-400',
            'bg-gray-50',
            'dark:bg-gray-900/50'
        );
        b.classList.add(
            'text-gray-500',
            'dark:text-gray-400',
            'hover:text-gray-700',
            'dark:hover:text-gray-300',
            'hover:bg-gray-100',
            'dark:hover:bg-gray-800',
            'bg-white',
            'dark:bg-transparent'
        );
    });
    
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    const btn = document.getElementById('tab-' + tabName);
    btn.classList.remove(
        'text-gray-500',
        'dark:text-gray-400',
        'hover:text-gray-700',
        'dark:hover:text-gray-300',
        'hover:bg-gray-100',
        'dark:hover:bg-gray-800',
        'bg-white',
        'dark:bg-gray-800'
    );
    btn.classList.add(
        'text-gray-900',
        'dark:text-white',
        'border-b-2',
        'border-gray-900',
        'dark:border-gray-400',
        'bg-gray-50',
    );
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection