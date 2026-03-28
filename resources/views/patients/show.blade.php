{{-- resources/views/patients/show.blade.php --}}
@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <!-- Breadcrumb -->
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('patients.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.patients') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($patient->full_name, 30) }}</span>
            </div>
        </div>

        <!-- Main Layout: Profile Card (Left) + Content (Right) -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- LEFT SIDE: Profile Card -->
            <div class="lg:w-80 xl:w-96 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 sticky top-6">
                    <!-- Profile Header -->
                    <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $patient->full_name }}</h2>

                        <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium">
                                MRN: {{ $patient->medical_record_number ?? '—' }}
                            </span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $patient->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                {{ $patient->is_active ? __('file.active') : __('file.inactive') }}
                            </span>
                            @php
                                $allergiesList = is_string($patient->allergies) ? [$patient->allergies] : (is_array($patient->allergies) ? $patient->allergies : []);
                            @endphp
                            @if(count($allergiesList) > 0)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                    {{ __('file.allergies') }}
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('patients.edit', $patient) }}"
                            class="inline-flex items-center justify-center w-full px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('file.edit_patient') }}
                        </a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">
                                {{ __('file.total_appointments') }}
                            </div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $patient->appointments_count ?? $patient->appointments->count() }}
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">
                                {{ __('file.blood_type') }}
                            </div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $patient->blood_type ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.date_of_birth') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $patient->date_of_birth?->format('d M Y') ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.age') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $patient->age ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.gender') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($patient->gender ?? '—') }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.phone') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->phone ?? '—' }}</p>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Tabbed Content -->
            <div class="w-full">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <!-- Mobile Tab Selector (Visible only on mobile) -->
                        <div class="sm:hidden p-4 bg-white dark:bg-gray-800 rounded-t-lg">
                            <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                            <select id="mobile-tab-select" onchange="switchTab(this.value)"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900 dark:focus:ring-gray-500">
                                <option value="overview">{{ __('file.overview') }}</option>
                                <option value="appointments">{{ __('file.appointments') }}</option>
                                <option value="prescriptions">{{ __('file.prescriptions') }}</option>
                                <option value="tax">{{ __('file.tax_information') }}</option>
                            </select>
                        </div>

                        <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                        <nav class="hidden sm:flex space-x-4 px-4 overflow-x-auto no-scrollbar "
                            aria-label="Tabs">
                            <button type="button" onclick="switchTab('overview')" id="tab-overview"
                                class="tab-button py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap border-blue-500 text-blue-600 dark:text-blue-400">
                                {{ __('file.overview') }}
                            </button>
                            <button type="button" onclick="switchTab('appointments')" id="tab-appointments"
                                class="tab-button py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                {{ __('file.appointments') }}
                            </button>
                            <button type="button" onclick="switchTab('prescriptions')" id="tab-prescriptions"
                                class="tab-button py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                {{ __('file.prescriptions') }}
                            </button>
                            <button type="button" id="tab-tax" onclick="switchTab('tax')" class="tab-button py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                {{ __('file.tax_information') }}
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Overview Tab -->
                        <div id="content-overview" class="tab-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.address') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $patient->address ? "$patient->address, $patient->city, $patient->state $patient->zip_code" : '—' }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.height_weight') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ ($patient->height_cm ?? '—') . ' cm / ' . ($patient->weight_kg ?? '—') . ' kg' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-8 mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('file.psychotherapy_info') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.attended_psychotherapy') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->attended_psychotherapy ? __('file.yes') : __('file.no') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.preferred_session_time') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->preferred_session_time ? date('h:i A', strtotime($patient->preferred_session_time)) : '—' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.recommended_by') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->recommended_by ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Patient document moved below medical info --}}

                            @php
                                $allergiesList = is_string($patient->allergies) ? [$patient->allergies] : (is_array($patient->allergies) ? $patient->allergies : []);
                                $medicationsList = is_string($patient->current_medications) ? [$patient->current_medications] : (is_array($patient->current_medications) ? $patient->current_medications : []);
                                $conditionsList = is_string($patient->chronic_conditions) ? [$patient->chronic_conditions] : (is_array($patient->chronic_conditions) ? $patient->chronic_conditions : []);
                                $surgeriesList = is_string($patient->past_surgeries) ? [$patient->past_surgeries] : (is_array($patient->past_surgeries) ? $patient->past_surgeries : []);
                                $hospitalizationsList = is_string($patient->previous_hospitalizations) ? [$patient->previous_hospitalizations] : (is_array($patient->previous_hospitalizations) ? $patient->previous_hospitalizations : []);
                            @endphp
                            @if(count($allergiesList) > 0 || count($medicationsList) > 0 || count($conditionsList) > 0 || count($surgeriesList) > 0 || count($hospitalizationsList) > 0)
                                <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        {{ __('file.medical_information') }}
                                    </h3>
                                    
                                    <div class="space-y-6">
                                        @if(count($allergiesList) > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.allergies') }}</h4>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($allergiesList as $allergy)
                                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 text-xs font-medium rounded-full">
                                                            {{ $allergy }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(count($medicationsList) > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.current_medications') }}</h4>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($medicationsList as $medication)
                                                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs font-medium rounded-full">
                                                            {{ $medication }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(count($conditionsList) > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.chronic_conditions') }}</h4>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($conditionsList as $condition)
                                                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300 text-xs font-medium rounded-full">
                                                            {{ $condition }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(count($surgeriesList) > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.past_surgeries') }}</h4>
                                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                    @foreach($surgeriesList as $surgery)
                                                        <li>{{ $surgery }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if(count($hospitalizationsList) > 0)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.previous_hospitalizations') }}</h4>
                                                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                                    @foreach($hospitalizationsList as $hospitalization)
                                                        <li>{{ $hospitalization }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($patient->document)
                                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        {{ __('file.document') }}
                                    </h3>
                                    @php
                                        $extension = strtolower(pathinfo($patient->document, PATHINFO_EXTENSION));
                                        $docUrl = asset($patient->document);
                                    @endphp
                                    
                                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                        <div class="mt-2 text-center bg-gray-50 dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 inline-block">
                                            <a href="{{ $docUrl }}" target="_blank" title="Click to view full size">
                                                <img src="{{ $docUrl }}" class="max-w-full h-auto rounded shadow-sm object-contain" alt="Patient Document" style="max-height: 500px;">
                                            </a>
                                        </div>
                                    @elseif($extension === 'pdf')
                                        <div class="mt-2 w-full">
                                            <iframe src="{{ $docUrl }}" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm" style="height: 600px;"></iframe>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <a href="{{ $docUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                {{ __('file.download_document') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('file.emergency_contact') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.name') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->emergency_contact_name ?? '—' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.emergency_relationship') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->emergency_contact_relationship ?? '—' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.phone') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->emergency_contact_phone ?? '—' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.email') }}</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">
                                            {{ $patient->emergency_contact_email ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointments Tab -->
                        <div id="content-appointments" class="tab-content hidden">
                            <div
                                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <table id="appointments-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.date') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.time') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.doctor') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.status') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Prescriptions Tab -->
                        <div id="content-prescriptions" class="tab-content hidden">
                            <div
                                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <table id="prescriptions-table"
                                    class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.date') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.doctor') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.diagnosis') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.type') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.medications') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('file.actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tax Information Tab -->
                        <div id="content-tax" class="tab-content hidden space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('file.tax_id') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->tax_id ?? __('file.not_specified') }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('file.tax_full_name') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->tax_full_name ?? __('file.not_specified') }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('file.tax_postal_code') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->tax_postal_code ?? __('file.not_specified') }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('file.tax_regime') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->tax_regime ?? __('file.not_specified') }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('file.tax_invoice_usage') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->tax_invoice_usage_label }}</p>
                                </div>
                                <div></div> {{-- Empty cell for alignment --}}
                            </div>

                            @if($patient->tax_cfdi_path)
                                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ __('file.tax_document') }}
                                        </h3>
                                        <a href="{{ asset($patient->tax_cfdi_path) }}" target="_blank" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                            <i class="fas fa-external-link-alt mr-2"></i> {{ __('file.view_document') }}
                                        </a>
                                    </div>
                                    @php
                                        $taxDocUrl = asset($patient->tax_cfdi_path);
                                        $taxDocExtension = strtolower(pathinfo($patient->tax_cfdi_path, PATHINFO_EXTENSION));
                                    @endphp
                                    @if($taxDocExtension === 'pdf')
                                        <div class="w-full">
                                            <iframe src="{{ $taxDocUrl }}" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm" style="height: 600px;"></iframe>
                                        </div>
                                    @else
                                        {{-- Should only be PDF based on validation, but adding fallback --}}
                                        <div class="text-center bg-gray-50 dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 inline-block">
                                            <img src="{{ $taxDocUrl }}" class="max-w-full h-auto rounded shadow-sm object-contain" alt="Tax Document" style="max-height: 500px;">
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        </div>
                    </div>
                </div>
            </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all contents and reset buttons
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(b => {
                b.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                b.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Update mobile select if present
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tabName;

            // Show selected content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Activate selected button
            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                // Scroll the tab into view on mobile
                const nav = btn.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = btn.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('#appointments-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('patients.appointments.datatable', $patient) }}',
                    order: [[0, 'desc']],
                    lengthChange: false,
                    pageLength: 10,
                    columns: [
                        { data: 'scheduled_start' },
                        { data: 'time' },
                        { data: 'doctor_name' },
                        { data: 'status_html' }
                    ],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_appointments') }}",
                        emptyTable: "{{ __('file.no_appointments_found') }}",
                        processing: "{{ __('file.processing') }}",
                    }
                });

                $('#prescriptions-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('patients.prescriptions.datatable', $patient) }}',
                    order: [[0, 'desc']],
                    lengthChange: false,
                    pageLength: 10,
                    columns: [
                        { data: 'prescription_date' },
                        { data: 'doctor_name' },
                        { data: 'diagnosis' },
                        { data: 'type' },
                        { data: 'medications_count', className: 'text-center' },
                        { data: 'actions', orderable: false, searchable: false, className: 'text-right' }
                    ],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_prescriptions') }}",
                        emptyTable: "{{ __('file.no_prescriptions_found') }}",
                        processing: "{{ __('file.processing') }}",
                    }
                });

                $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('patients.billing.datatable', $patient) }}',
                    order: [[1, 'desc']],
                    lengthChange: false,
                    pageLength: 10,
                    columnDefs: [
                        { orderable: false, targets: [0, 1, 2, 3, 4, 5] },
                        { searchable: false, targets: [2, 3, 4, 5] }
                    ],
                    columns: [
                        { data: 'invoice_number' },
                        { data: 'invoice_date' },
                        { data: 'total', className: 'text-right font-medium' },
                        { data: 'paid_amount', className: 'text-right font-medium' },
                        { data: 'balance_due', className: 'text-right font-medium' },
                        { data: 'status_html' }
                    ],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_invoices') }}",
                        emptyTable: "{{ __('file.no_billings_found') }}",
                        processing: "{{ __('file.processing') }}",
                        zeroRecords: "{{ __('file.no_billings_found') }}"
                    }
                });
            });
        </script>
    @endpush
@endsection