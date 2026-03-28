@extends('layouts.pos')
@section('title', __('file.point_of_sale'))

@section('content')

    @php
        $preloadedItems = $preloadedItems ?? [];
        $preselectedPatientId = $preselectedPatientId ?? null;
    @endphp

    <script>
        window.preloadedItems = @json($preloadedItems);
        window.preselectedPatientId = @json($preselectedPatientId);
        window.preselectedAppointmentId = @json($appointmentId ?? null);
        window.currentAppointment = @json($appointment);
    </script>

    <div class="w-full h-full flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-900" id="pos-wrapper">


        <!-- LEFT PANEL: Patient/Doctor selectors + Items grid -->
        <!-- On mobile: shown by default, hidden when cart-tab active -->
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden" id="items-panel">

            <!-- Top bar with back button, register status, and action buttons -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.back') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <div id="register-status-container" class="cursor-pointer" onclick="showRegisterDetails()">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white" id="register-label">
                                {{ __('file.register') }}:
                                {{ __('file.loading') }}...
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" id="register-subtitle">
                                {{ __('file.loading_status') }}...
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 ml-auto" id="current-time">
                            {{ __('file.loading_time') }}...
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showSalesStats()"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.sales_statistics') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </button>
                        <button onclick="showRegisterDetails()"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.register_details') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        <button onclick="showLastTransaction()"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.last_transaction') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        <button onclick="showCompletedAppointments()"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.completed_appointments') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        @if(isset($appointment))
                            <button onclick="showAppointmentSummary()"
                                class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors active:scale-95"
                                title="{{ __('file.appointment_summary') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </button>
                            @php
                                $latestPrescription = $appointment->prescriptions->sortByDesc('prescription_date')->first();
                            @endphp
                            @if($latestPrescription)
                                <a href="{{ route('prescriptions.print', $latestPrescription) }}?redirect=pos"
                                    class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors active:scale-95 flex items-center gap-1"
                                    title="Print Prescription">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            @endif
                        @endif

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="flex items-center space-x-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none rounded-md px-2 py-2 transition active:scale-95"
                                title="{{ __('file.language') }}">
                                <span class="text-xs font-bold">{{ strtoupper(app()->getLocale()) }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-36 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                                <form method="POST" action="{{ route('language.switch') }}">
                                    @csrf
                                    <input type="hidden" name="locale" value="en">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        {{ __('English') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('language.switch') }}">
                                    @csrf
                                    <input type="hidden" name="locale" value="es">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        {{ __('Español') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <button id="theme-toggle-navbar" aria-label="Toggle dark mode"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.theme') }}">
                            <svg id="sun-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l.707.707M6.343 6.343l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                            </svg>
                            <svg id="moon-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <button id="fullscreen-toggle" aria-label="Toggle fullscreen"
                            class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95"
                            title="{{ __('file.fullscreen') }}">
                            <svg id="enter-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 3H5a2 2 0 00-2 2v3M16 3h3a2 2 0 012 2v3M8 21H5a2 2 0 01-2-2v-3M16 21h3a2 2 0 002-2v-3" />
                            </svg>
                            <svg id="exit-fullscreen-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 9H5V5M15 9h4V5M9 15H5v4M15 15h4v4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Patient & Doctor selectors (left side, horizontal row) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <!-- Patient selector -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                            {{ __('file.patient') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <select id="patient-select"
                                class="block w-full pl-9 pr-8 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                <option value="">{{ __('file.select_patient') }}</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $preselectedPatientId == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->first_name }} {{ $patient->last_name }} -
                                        {{ $patient->medical_record_number }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor selector -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                            {{ __('file.doctor') }} <span class="text-red-500">*</span> <span
                                class="text-gray-400 normal-case font-normal">({{ __('file.required_for_treatments') }})</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A4 4 0 018 17h8a4 4 0 012.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <select id="doctor-select"
                                class="block w-full pl-9 pr-8 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                <option value="">{{ __('file.select_doctor') }}</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ $preselectedDoctorId == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->first_name }} {{ $doctor->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redesigned Search Bar -->
                <div class="relative mb-3">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="search-products" placeholder="{{ __('file.search_products_or_services') }}"
                        class="block w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <!-- Category filter buttons -->
                <div class="flex gap-2 overflow-x-auto pb-1 hide-scrollbar">
                    <button onclick="filterCategory('all')"
                        class="category-btn active px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-gray-900 text-white dark:bg-white dark:text-gray-900 active:scale-95 transition-all">
                        {{ __('file.all_items') }}
                    </button>
                    <button onclick="filterCategory('consultation')"
                        class="category-btn px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all border border-gray-200 dark:border-gray-600">
                        {{ __('file.consultations') }}
                    </button>
                    <button onclick="filterCategory('treatment')"
                        class="category-btn px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all border border-gray-200 dark:border-gray-600">
                        {{ __('file.treatments') }}
                    </button>
                    <button onclick="filterCategory('medication')"
                        class="category-btn px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all border border-gray-200 dark:border-gray-600">
                        {{ __('file.medications') }}
                    </button>
                </div>
            </div>

            <!-- Items grid (compact cards, no image placeholder) -->
            <!-- On mobile add pb-16 so content isn't hidden behind tab bar -->
            <div class="flex-1 overflow-y-auto p-3 pb-20 lg:pb-3 bg-gray-50 dark:bg-gray-900">
                <div id="products-grid"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2">

                    @foreach($services as $service)
                        <button type="button"
                            class="product-card flex flex-col justify-between p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-95 transition-all text-left group"
                            data-item-type="service" data-item-id="{{ $service->id }}" data-item-name="{{ $service->name }}"
                            data-item-price="{{ $service->price }}" data-category="consultation"
                            onclick="addToCart('service', {{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->price }})">
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </span>
                                    <span
                                        class="text-xs font-semibold text-blue-600 dark:text-blue-400">{{ __('file.service') }}</span>
                                </div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 leading-snug">
                                    {{ $service->name }}
                                </h3>
                            </div>
                            <div
                                class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $currency_code }}{{ number_format($service->price, 2) }}
                                </span>
                                <span
                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                            </div>
                        </button>
                    @endforeach

                    @foreach($inventoryItems as $item)
                        @php
                            $isStockReached = $item->current_stock <= $item->minimum_stock_level;
                        @endphp
                        <button type="button"
                            class="product-card flex flex-col justify-between p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-95 transition-all text-left group {{ $isStockReached ? 'opacity-60 cursor-not-allowed' : '' }}"
                            data-item-type="inventory" data-item-id="{{ $item->id }}"
                            data-item-name="{{ $item->name }} @if($item->generic_name) ({{ $item->generic_name }}) @endif"
                            data-item-price="{{ $item->unit_price }}" data-current-stock="{{ $item->current_stock }}"
                            data-min-stock="{{ $item->minimum_stock_level }}" data-category="medication"
                            onclick="{{ !$isStockReached ? "addToCart('inventory', {$item->id}, '" . addslashes($item->name . ($item->generic_name ? ' (' . $item->generic_name . ')' : '')) . "', {$item->unit_price}, {$item->current_stock}, {$item->minimum_stock_level})" : "showNotification('" . __('file.stock_level_reached_badge') . "', 'error')" }}"
                            {{ $isStockReached ? 'disabled' : '' }}>
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/40 flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </span>
                                    @if($isStockReached)
                                        <span
                                            class="text-xs px-1.5 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-full font-semibold">{{ __('file.stock_level_reached_badge') }}</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 leading-snug">
                                    {{ $item->name }}
                                    @if($item->generic_name)
                                        <span
                                            class="text-xs font-normal text-gray-400 dark:text-gray-500">({{ $item->generic_name }})</span>
                                    @endif
                                </h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ __('file.stock') }}: <strong
                                        class="{{ $isStockReached ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' }}">{{ $item->current_stock }}</strong>
                                </p>
                            </div>
                            <div
                                class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $currency_code }}{{ number_format($item->unit_price, 2) }}
                                </span>
                                <span
                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                            </div>
                        </button>
                    @endforeach

                    <!-- Dynamic doctor treatments container -->
                    <div id="treatments-container"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2 col-span-full">
                        <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400"
                            id="treatments-placeholder">
                            {{ __('file.select_doctor_for_treatments') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Cart only -->
        <!-- Desktop: fixed width side panel. Mobile: hidden by default, shown when cart tab active -->
        <div class="flex-col min-h-0 overflow-hidden bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex-shrink-0 hidden lg:flex"
            id="cart-panel" style="width: 480px; min-width: 400px; max-width: 550px;">


            <div
                class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.cart_items') }}: <span
                            id="items-count" class="font-bold text-gray-900 dark:text-white">0</span></span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <div id="cart-items" class="space-y-3">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.cart_is_empty') }}</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ __('file.add_items_to_get_started') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3 flex-shrink-0">

                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="space-y-1">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('file.subtotal') }}</span>
                        <p id="subtotal-amount" class="font-semibold text-gray-900 dark:text-white">{{ $currency_code }}0.00
                        </p>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.tax') }}</span>
                            <input type="number" id="tax-input" value="{{ $tax_percentage ?? 0 }}" min="0" max="100" step="0.1"
                                class="w-14 px-1 py-0.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-center"
                                onchange="updateTotals()">
                            <span class="text-gray-600 dark:text-gray-400">%</span>
                        </div>
                        <p id="tax-amount" class="font-semibold text-gray-900 dark:text-white">{{ $currency_code }}0.00</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('file.discount') }}</span>
                        <input type="number" id="discount-input" value="0" min="0" step="0.01"
                            class="w-full px-2 py-0.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-right"
                            placeholder="0.00" onchange="updateTotals()">
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('file.total_amount') }}</span>
                        <span id="grand-total"
                            class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currency_code }}0.00</span>
                    </div>

                    <div class="flex items-center gap-2 mb-3">
                        <input type="checkbox" id="print-invoice" checked
                            class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600">
                        <label for="print-invoice"
                            class="text-sm font-medium text-gray-900 dark:text-gray-300">{{ __('file.print_invoice') ?? 'Invoiced' }}</label>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('file.payment_method') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button onclick="openPaymentModal('cash')"
                                class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ __('file.cash') }}
                            </button>
                            <button onclick="openPaymentModal('card')"
                                class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                {{ __('file.card') }}
                            </button>
                            <button onclick="openPaymentModal('other')"
                                class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('file.other') }}
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="saveAsDraft()"
                            class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all ">
                            {{ __('file.save_as_draft') }}
                        </button>
                        <button type="button" onclick="openPartialPaymentModal()"
                            class="py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                            {{ __('file.partial_pay') }}
                        </button>
                        <button onclick="clearCart()"
                            class="py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 active:scale-95 transition-all">
                            {{ __('file.cancel_sale') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======== MOBILE CART PANEL (full-screen overlay on mobile/tablet) ======== -->
        <div id="mobile-cart-panel" class="lg:hidden fixed inset-0 z-30 flex-col bg-white dark:bg-gray-800"
            style="display:none; padding-bottom:56px;">

            <div
                class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.cart_items') }}: <span
                            id="items-count-m" class="font-bold text-gray-900 dark:text-white">0</span></span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <div id="cart-items-m" class="space-y-3">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.cart_is_empty') }}</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ __('file.add_items_to_get_started') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3 flex-shrink-0">
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.subtotal') }}</p>
                        <p id="subtotal-m" class="font-semibold text-gray-900 dark:text-white">{{ $currency_code }}0.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.tax') }}</p>
                        <p id="tax-m" class="font-semibold text-gray-900 dark:text-white">{{ $currency_code }}0.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.discount') }}</p>
                        <p id="disc-m" class="font-semibold text-gray-900 dark:text-white">{{ $currency_code }}0.00</p>
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-300 dark:border-gray-600 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('file.total_amount') }}</span>
                    <span id="total-m"
                        class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currency_code }}0.00</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="openPaymentModal('cash')"
                        class="py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-gray-400 transition-all">
                        <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ __('file.cash') }}
                    </button>
                    <button onclick="openPaymentModal('card')"
                        class="py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-gray-400 transition-all">
                        <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        {{ __('file.card') }}
                    </button>
                    <button onclick="openPartialPaymentModal()"
                        class="py-2 text-xs font-medium rounded-lg border-2 border-blue-500 bg-blue-600 text-white hover:bg-blue-700 transition-all">
                        {{ __('file.partial_pay') }}
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="saveAsDraft()"
                        class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">{{ __('file.save_as_draft') }}</button>
                    <button onclick="clearCart()"
                        class="py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg">{{ __('file.cancel_sale') }}</button>
                </div>
            </div>
        </div>

        <!-- ======== MOBILE BOTTOM TAB BAR ======== -->
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex"
            style="height:56px;" id="mobile-tab-bar">
            <button onclick="toggleMobileTab('items')" id="tab-btn-items"
                class="flex-1 flex flex-col items-center justify-center gap-0.5 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="text-xs font-semibold">{{ __('file.all_items') }}</span>
            </button>
            <button onclick="toggleMobileTab('cart')" id="tab-btn-cart"
                class="flex-1 flex flex-col items-center justify-center gap-0.5 text-gray-500 dark:text-gray-400 transition-all relative">
                <div class="relative inline-block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span id="mobile-cart-badge"
                        class="hidden absolute -top-1.5 -right-2.5 bg-red-500 text-white text-xs font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center leading-none"></span>
                </div>
                <span class="text-xs font-semibold">{{ __('file.cart_items') }}</span>
            </button>
        </div>

    </div>

    <!-- ==================== Appointment Summary Modal ==================== -->
    <div id="appointment-summary-modal"
        class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div
                class="p-5 border-b dark:border-gray-700 flex justify-between items-center bg-indigo-50 dark:bg-indigo-900">
                <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-100">{{ __('file.appointment_summary') }}</h3>
                <button onclick="closeAppointmentSummary()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-all active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto max-h-[70vh]" id="appointment-summary-content">
                <!-- Content injected via JS -->
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700">
                <button onclick="closeAppointmentSummary()"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all active:scale-[0.98]">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <div id="payment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.complete_payment') }}</h3>
                <button onclick="closePaymentModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 active:scale-95 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.amount_due') }}</p>
                    <p id="modal-grand-total" class="text-4xl font-bold text-gray-900 dark:text-white">
                        {{ $currency_code }}0.00
                    </p>
                </div>

                <div id="cash-section" class="space-y-4 hidden">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.amount_tendered') }}</label>
                        <input type="number" id="cash-received" step="0.01" min="0" value="0"
                            class="w-full px-4 py-3 text-xl text-right font-semibold border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white"
                            oninput="updateCashChange()">
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <button onclick="addCashDenomination(100)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">100</button>
                        <button onclick="addCashDenomination(50)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">50</button>
                        <button onclick="addCashDenomination(20)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">20</button>
                        <button onclick="addCashDenomination(10)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">10</button>
                        <button onclick="addCashDenomination(5)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">5</button>
                        <button onclick="addCashDenomination(1)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">1</button>
                        <button onclick="addCashDenomination(0.25)"
                            class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium text-xs">0.25</button>
                        <button onclick="clearCashReceived()"
                            class="py-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg font-bold text-xs">{{ strtoupper(__('file.clear')) }}</button>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ __('file.change_due') }}</p>
                        <p id="cash-change" class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ $currency_code }}0.00
                        </p>
                    </div>
                </div>

                <div id="card-section" class="hidden text-center py-8">
                    <svg class="w-20 h-20 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.card_payment') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        {{ __('file.process_card_payment_externally') }}
                    </p>
                </div>

                <div id="other-section" class="hidden text-center py-8">
                    <svg class="w-20 h-20 mx-auto text-purple-500 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.other_payment') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('file.record_payment_manually') }}</p>
                </div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg space-y-3">
                <button id="complete-payment-btn" onclick="processPayment()"
                    class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 active:scale-98 transition-all text-base">
                    {{ __('file.complete_payment') }}
                </button>
                <button onclick="closePaymentModal()"
                    class="w-full py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                    {{ __('file.cancel') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== Register Details Modal ==================== -->
    <div id="register-details-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold">{{ __('file.cash_register_session') }}</h3>
                <button onclick="closeRegisterDetailsModal()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4" id="register-details-content">
                <!-- This will be replaced dynamically by JS -->
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    {{ __('file.loading_register_details') }}...
                </div>
            </div>

            <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
                <button onclick="closeRegisterDetailsModal()"
                    class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    {{ __('file.close') }}
                </button>
                <button onclick="showCloseRegisterForm()"
                    class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                    {{ __('file.close_and_reconcile_register') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Last Transaction Modal -->
    <div id="last-transaction-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.last_transaction') }}</h3>
                <button onclick="closeLastTransaction()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.invoice_number_abbr') }}</span>
                    <span id="last-inv-number" class="text-sm font-semibold text-gray-900 dark:text-white">---</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.patient') }}</span>
                    <span id="last-inv-patient" class="text-sm font-semibold text-gray-900 dark:text-white">---</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.time') }}</span>
                    <span id="last-inv-time" class="text-sm font-semibold text-gray-900 dark:text-white">---</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.payment_method') }}</span>
                    <span id="last-inv-method" class="text-sm font-semibold text-gray-900 dark:text-white">---</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span
                            class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.total_amount') }}</span>
                        <span id="last-inv-total"
                            class="text-base font-bold text-gray-900 dark:text-white">{{ $currency_code }}0.00</span>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex gap-2">
                <button id="last-inv-view-btn" onclick="viewInvoice()"
                    class="flex-1 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors text-sm">
                    {{ __('file.view_invoice') }}
                </button>
                <button id="last-inv-print-btn" onclick="printLastInvoice()"
                    class="flex-1 py-2 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors text-sm">
                    {{ __('file.print') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Completed Appointments Modal -->
    <div id="completed-appointments-modal" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('file.completed_appointments') ?? 'Completed Appointments' }}
                </h3>
                <button onclick="closeCompletedAppointments()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-0 overflow-y-auto flex-1">
                <div id="completed-appointments-content" class="min-h-[200px] flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <!-- Dynamic content -->
                </div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex justify-end">
                <button onclick="closeCompletedAppointments()" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <div id="partial-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold">{{ __('file.record_payment') }}</h3>
                <button onclick="closePartialModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-5">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('file.total_due') }}</p>
                    <p id="partial-total" class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ $currency_code }}0.00
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('file.amount_paying_now') }}</label>
                    <input type="number" step="0.01" min="0" id="partial-amount"
                        class="w-full text-2xl text-right font-semibold p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('file.payment_method') }}</label>
                    <select id="partial-method"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="cash">{{ __('file.cash') }}</option>
                        <option value="card">{{ __('file.card') }}</option>
                        <option value="bank_transfer">{{ __('file.bank_transfer') }}</option>
                        <option value="cheque">{{ __('file.cheque') }}</option>
                        <option value="other">{{ __('file.other') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('file.reference_receipt_optional') }}</label>
                    <input type="text" id="partial-reference"
                        class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="pt-2 border-t dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('file.remaining_balance') }}</span>
                        <span id="partial-remaining" class="font-bold">{{ $currency_code }}0.00</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 mt-4 pt-2 border-t dark:border-gray-700">
                    <input type="checkbox" id="partial-print-invoice" checked class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600">
                    <label for="partial-print-invoice" class="text-sm font-medium text-gray-900 dark:text-gray-300">{{ __('file.print_invoice') ?? 'Print Bill' }}</label>
                </div>
            </div>

            <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
                <button onclick="closePartialModal()"
                    class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    {{ __('file.cancel') }}
                </button>
                <button onclick="submitPartialPayment()"
                    class="flex-1 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('file.record_payment') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== Register Open Modal ==================== -->
    <div id="open-register-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold">{{ __('file.open_cash_register') }}</h3>
                <button onclick="closeOpenRegisterModal()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="open-register-form" class="p-5 space-y-5">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-sm text-yellow-800 dark:text-yellow-200">
                    {{ __('file.no_cash_register_open_message') }}
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('file.opening_balance_counted_cash') }}</label>
                    <input type="number" name="opening_balance" step="0.01" min="0" required autofocus
                        class="w-full text-2xl text-right font-bold p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('file.notes_optional') }}</label>
                    <textarea name="notes" rows="3"
                        class="w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600"></textarea>
                </div>

                <!-- Buttons INSIDE the form now -->
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeOpenRegisterModal()"
                        class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                        {{ __('file.open_register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== Register Details Modal ==================== -->
    <div id="register-details-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold">{{ __('file.cash_register_session') }}</h3>
                <button onclick="closeRegisterDetailsModal()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-4" id="register-details-content">
                <!-- Filled by JavaScript -->
            </div>

            <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
                <button onclick="closeRegisterDetailsModal()"
                    class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    {{ __('file.close') }}
                </button>
                <button onclick="showCloseRegisterForm()" class="w-full py-3 bg-red-600 ...">
                    {{ __('file.close_and_reconcile_register') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== Sale Statistics Modal ==================== -->
    <div id="sales-stats-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('file.sale_statistics') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="stats-session-info"></p>
                </div>
                <button onclick="closeSalesStats()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl">
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('file.total_sales') }}</p>
                        <p class="text-2xl font-bold dark:text-white" id="stats-total-sales">--</p>
                        <div class="mt-2 text-xs flex justify-between text-blue-800/70 dark:text-blue-300/70">
                            <span>Cash: <span id="stats-cash-sales">--</span></span>
                            <span>Card: <span id="stats-card-sales">--</span></span>
                        </div>
                    </div>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl">
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-1">Expenses/Purchases</p>
                        <p class="text-2xl font-bold dark:text-white" id="stats-total-outflow">--</p>
                        <div class="mt-2 text-xs flex justify-between text-red-800/70 dark:text-red-300/70">
                            <span>Exp: <span id="stats-expenses">--</span></span>
                            <span>Pur: <span id="stats-purchases">--</span></span>
                        </div>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl">
                        <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-1">{{ __('file.expected_cash') }}</p>
                        <p class="text-2xl font-bold dark:text-white" id="stats-expected-cash">--</p>
                        <p class="mt-2 text-xs text-green-800/70 dark:text-green-300/70">Balance in Register</p>
                    </div>
                </div>

                <!-- Transaction History -->
                <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recent Transactions
                    </h4>
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 border-b dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-2 font-medium">Time</th>
                                    <th class="px-4 py-2 font-medium">Type</th>
                                    <th class="px-4 py-2 font-medium">Ref</th>
                                    <th class="px-4 py-3 font-medium text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="stats-transactions-body" class="divide-y dark:divide-gray-700">
                                <!-- JS items here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <button onclick="closeSalesStats()" class="w-full py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

    <!-- Hidden iframe for printing -->
    <iframe id="print-iframe" style="display:none;"></iframe>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .active\:scale-95:active {
            transform: scale(0.95);
        }

        .active\:scale-98:active {
            transform: scale(0.98);
        }
    </style>

    <script>
        let cart = [];
        let selectedPaymentMethod = '';
        let currentRegister = null;
        let currentDoctorId = null;

        document.getElementById('doctor-select')?.addEventListener('change', function () {
            currentDoctorId = this.value;

            if (!currentDoctorId) {
                document.getElementById('treatments-placeholder').style.display = 'block';
                document.querySelectorAll('#treatments-container .product-card').forEach(el => el.remove());
                return;
            }

            loadDoctorTreatments(currentDoctorId);
        });

        async function loadDoctorTreatments(doctorId) {
            const container = document.getElementById('treatments-container');
            const placeholder = document.getElementById('treatments-placeholder');

            if (!doctorId) {
                placeholder.innerHTML = '{{ __("file.select_doctor_for_treatments") }}';
                placeholder.style.display = 'block';
                document.querySelectorAll('#treatments-container .product-card').forEach(el => el.remove());
                return;
            }

            placeholder.innerHTML = '{{ __("file.loading_treatments") }}';
            placeholder.style.display = 'block';

            try {
                // Use named route with placeholder replacement
                const routeBase = '{{ route("pos.doctor.treatments", ":doctor") }}';
                const url = routeBase.replace(':doctor', doctorId);

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load treatments');
                }

                // Clear old cards
                document.querySelectorAll('#treatments-container .product-card').forEach(el => el.remove());

                if (data.treatments.length === 0) {
                    placeholder.innerHTML = '{{ __("file.no_treatments_for_doctor") }}';
                    return;
                }

                placeholder.style.display = 'none';

                data.treatments.forEach(t => {
                    const card = document.createElement('button');
                    card.type = 'button';
                    card.className = 'product-card flex flex-col justify-between p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md active:scale-95 transition-all text-left group';
                    card.dataset.itemType = 'treatment';
                    card.dataset.itemId = t.id;
                    card.dataset.itemName = t.name;
                    card.dataset.itemPrice = t.price;
                    card.dataset.category = 'treatment';

                    if (parseFloat(t.price) <= 0) {
                        card.disabled = true;
                        card.classList.add('opacity-60');
                    } else {
                        card.onclick = () => addToCart('treatment', t.id, t.name, t.price, Infinity, 0, doctorId);
                    }

                    card.innerHTML = `
                                                                                                                                                                    <div class="flex flex-col justify-between h-full">
                                                                                                                                                                        <div>
                                                                                                                                                                            <div class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                                                                                                                                                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex-shrink-0">
                                                                                                                                                                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                                                                                                                                                    </svg>
                                                                                                                                                                                </span>
                                                                                                                                                                                ${t.code ? `<span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-full font-semibold">${t.code}</span>` : ''}
                                                                                                                                                                            </div>
                                                                                                                                                                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 leading-snug">
                                                                                                                                                                                ${t.name}
                                                                                                                                                                            </h3>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                                                                                                                                                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                                                                                                                                                $${t.display}
                                                                                                                                                                            </span>
                                                                                                                                                                            <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition-colors">
                                                                                                                                                                                <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                                                                                                                                                </svg>
                                                                                                                                                                            </span>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                `;

                    container.appendChild(card);
                });

            } catch (err) {
                console.error('Error loading treatments:', err);
                placeholder.innerHTML = '{{ __("file.error_loading_treatments") }}';
            }
        }

        // Optional: load on page load if preselected
        document.addEventListener('DOMContentLoaded', () => {
            const doctorSelect = document.getElementById('doctor-select');
            if (doctorSelect && doctorSelect.value) {
                loadDoctorTreatments(doctorSelect.value);
            }
        });

        function addToCart(type, id, name, price, currentStock = Infinity, minStock = 0, doctorId = null) {
            const itemKey = `${type}-${id}`;
            const existingItem = cart.find(item => item.key === itemKey);

            if (type === 'inventory') {
                const currentQty = existingItem ? existingItem.quantity : 0;
                if (currentStock - (currentQty + 1) < minStock) {
                    showNotification('{{ __("file.stock_level_reached_badge") }}', 'error');
                    return;
                }
            }

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    key: itemKey,
                    type,
                    id,
                    name,
                    price: parseFloat(price),
                    quantity: 1,
                    currentStock,
                    minStock,
                    doctorId: doctorId || (type === 'treatment' ? currentDoctorId : null)
                });
            }
            renderCart();
            updateTotals();
        }

        function removeFromCart(key) {
            cart = cart.filter(item => item.key !== key);
            renderCart();
            updateTotals();
        }

        function updateQuantity(key, change) {
            const item = cart.find(item => item.key === key);
            if (item) {
                if (item.type === 'treatment') return;

                if (change > 0 && item.type === 'inventory') {
                    if (item.currentStock - (item.quantity + change) < item.minStock) {
                        showNotification('{{ __("file.stock_level_reached_badge") }}', 'error');
                        return;
                    }
                }
                const newQty = item.quantity + change;
                if (newQty < 1) return;
                item.quantity = newQty;
                renderCart();
                updateTotals();
            }
        }

        function updateQuantityValue(key, value) {
            const item = cart.find(item => item.key === key);
            if (item) {
                if (item.type === 'treatment') {
                    renderCart();
                    return;
                }
                const newQty = parseInt(value);
                if (isNaN(newQty) || newQty < 1) {
                    renderCart(); // Reset to current
                    return;
                }

                if (item.type === 'inventory') {
                    if (item.currentStock - newQty < item.minStock) {
                        showNotification('{{ __("file.warning") }}', '{{ __("file.stock_level_reached_badge") }}', 'warning');
                        renderCart();
                        return;
                    }
                }
                item.quantity = newQty;
                renderCart();
                updateTotals();
            }
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const containerMobile = document.getElementById('cart-items-m');

            if (cart.length === 0) {
                const emptyHtml = `
                                                                                                                                                                <div class="text-center py-12">
                                                                                                                                                                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                                                                                                                                    </svg>
                                                                                                                                                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.cart_is_empty') }}</p>
                                                                                                                                                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ __('file.add_items_to_get_started') }}</p>
                                                                                                                                                                </div>`;
                if (container) container.innerHTML = emptyHtml;
                if (containerMobile) containerMobile.innerHTML = emptyHtml;
                return;
            }

            const cartHtml = cart.map(item => `
                                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-100 dark:border-gray-700">
                                                                <!-- Row 1: Item Name and Each Price -->
                                                                <div class="flex items-center justify-between mb-2">
                                                                    <div class="min-w-0 pr-2">
                                                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate flex items-center gap-2">
                                                                            ${item.name}
                                                                            ${item.source ? `<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 uppercase">
                                                                                ${item.source === 'appointment' ? 'Treatment' : 'Rx'}
                                                                             </span>` : ''}
                                                                        </h4>
                                                                    </div>
                                                                    <div class="flex items-center gap-3 shrink-0">
                                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">${{ $currency_code }}${item.price.toFixed(2)}</p>
                                                                        <button onclick="removeFromCart('${item.key}')" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 active:scale-90 transition-all p-1">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <!-- Row 2: Controls and Subtotal -->
                                                            <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-600/50">
                                                                <div class="flex items-center gap-1.5">
                                                                    ${item.type === 'treatment' ? `
                                                                        <span class="w-16 h-7 flex items-center justify-center text-sm font-bold text-gray-500 bg-gray-100 dark:bg-gray-800/50 rounded-md">
                                                                            ${item.quantity}
                                                                        </span>
                                                                    ` : `
                                                                        <button onclick="updateQuantity('${item.key}', -1)" class="w-7 h-7 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-90 transition-all">
                                                                            <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                                        </button>
                                                                        <input type="number" 
                                                                               value="${item.quantity}" 
                                                                               onchange="updateQuantityValue('${item.key}', this.value)"
                                                                               class="w-16 h-7 text-center text-sm font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                                               min="1">
                                                                        <button onclick="updateQuantity('${item.key}', 1)" class="w-7 h-7 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-90 transition-all">
                                                                            <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                                                        </button>
                                                                    `}
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">${{ $currency_code }}${(item.price * item.quantity).toFixed(2)}</p>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        `).join('');

            if (container) container.innerHTML = cartHtml;
            if (containerMobile) containerMobile.innerHTML = cartHtml;
        }

        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxRate = parseFloat(document.getElementById('tax-input').value) || 0;
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const taxAmount = (subtotal * taxRate) / 100;
            const grandTotal = Math.max(0, subtotal + taxAmount - discount);
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

            // Desktop
            const itemsCountEl = document.getElementById('items-count');
            if (itemsCountEl) itemsCountEl.textContent = totalItems;
            const subtotalAmountEl = document.getElementById('subtotal-amount');
            if (subtotalAmountEl) subtotalAmountEl.innerText = '{{ $currency_code }}' + subtotal.toFixed(2);
            const taxAmountEl = document.getElementById('tax-amount');
            if (taxAmountEl) taxAmountEl.textContent = '{{ $currency_code }}' + taxAmount.toFixed(2);
            const grandTotalEl = document.getElementById('grand-total');
            if (grandTotalEl) grandTotalEl.textContent = '{{ $currency_code }}' + grandTotal.toFixed(2);

            // Mobile
            const itemsCountM = document.getElementById('items-count-m');
            if (itemsCountM) itemsCountM.textContent = totalItems;
            const subtotalM = document.getElementById('subtotal-m');
            if (subtotalM) subtotalM.textContent = '{{ $currency_code }}' + subtotal.toFixed(2);
            const taxM = document.getElementById('tax-m');
            if (taxM) taxM.textContent = '{{ $currency_code }}' + taxAmount.toFixed(2);
            const discM = document.getElementById('disc-m');
            if (discM) discM.textContent = '{{ $currency_code }}' + discount.toFixed(2);
            const totalM = document.getElementById('total-m');
            if (totalM) totalM.textContent = '{{ $currency_code }}' + grandTotal.toFixed(2);

            const badge = document.getElementById('mobile-cart-badge');
            if (badge) {
                if (totalItems > 0) {
                    badge.textContent = totalItems;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        function toggleMobileTab(tab) {
            const itemsPanel = document.getElementById('items-panel');
            const cartPanel = document.getElementById('mobile-cart-panel');
            const tabItems = document.getElementById('tab-btn-items');
            const tabCart = document.getElementById('tab-btn-cart');

            if (tab === 'items') {
                itemsPanel.style.display = 'flex';
                // cartPanel is fixed overlay, hide it
                cartPanel.style.display = 'none';

                tabItems.classList.replace('text-gray-500', 'text-gray-900');
                tabItems.classList.replace('dark:text-gray-400', 'dark:text-white');
                tabItems.classList.replace('bg-white', 'bg-gray-50');
                tabItems.classList.replace('dark:bg-gray-800', 'dark:bg-gray-700');

                tabCart.classList.replace('text-gray-900', 'text-gray-500');
                tabCart.classList.replace('dark:text-white', 'dark:text-gray-400');
                tabCart.classList.replace('bg-gray-50', 'bg-white');
                tabCart.classList.replace('dark:bg-gray-700', 'dark:bg-gray-800');
            } else {
                // Items panel can stay flex underneath, overlay goes on top
                cartPanel.style.display = 'flex';

                tabCart.classList.replace('text-gray-500', 'text-gray-900');
                tabCart.classList.replace('dark:text-gray-400', 'dark:text-white');
                tabCart.classList.replace('bg-white', 'bg-gray-50');
                tabCart.classList.replace('dark:bg-gray-800', 'dark:bg-gray-700');

                tabItems.classList.replace('text-gray-900', 'text-gray-500');
                tabItems.classList.replace('dark:text-white', 'dark:text-gray-400');
                tabItems.classList.replace('bg-gray-50', 'bg-white');
                tabItems.classList.replace('dark:bg-gray-700', 'dark:bg-gray-800');
            }
        }

        function openPartialPaymentModal() {
            if (!currentRegister) {
                showNotification('{{ __("file.error") }}', 'No cash register is currently open. You must open a register first.', 'error');
                showOpenRegisterModal();
                return;
            }
            if (cart.length === 0) return showNotification('{{ __("file.info") }}', "Cart is empty", 'info');
            if (!document.getElementById('patient-select').value) return showNotification('{{ __("file.warning") }}', "Please select patient", 'warning');
            const total = parseFloat(document.getElementById('grand-total').textContent.replace(/[^0-9.]/g, '')) || 0;
            document.getElementById('partial-total').textContent = '{{ $currency_code }}' + total.toFixed(2);
            document.getElementById('partial-amount').value = '';
            updatePartialRemaining();
            document.getElementById('partial-modal').classList.remove('hidden');
        }

        function closePartialModal() {
            document.getElementById('partial-modal').classList.add('hidden');
        }

        function updatePartialRemaining() {
            const total = parseFloat(document.getElementById('partial-total').textContent.replace(/[^0-9.]/g, '')) || 0;
            const paidNow = parseFloat(document.getElementById('partial-amount').value) || 0;
            const remaining = Math.max(0, total - paidNow);
            document.getElementById('partial-remaining').textContent = '{{ $currency_code }}' + remaining.toFixed(2);
        }

        document.getElementById('partial-amount')?.addEventListener('input', updatePartialRemaining);

        function submitPartialPayment() {
            const amountInput = document.getElementById('partial-amount');
            const amount = parseFloat(amountInput.value) || 0;

            if (amount < 0) {
                showNotification('{{ __("file.error") }}', "Amount cannot be negative", 'error');
                return;
            }

            const total = parseFloat(document.getElementById('partial-total').textContent.replace(/[^0-9.]/g, '')) || 0;

            if (amount > total) {
                showNotification('{{ __("file.warning") }}', "Cannot pay more than total due", 'warning');
                return;
            }

            if (amount === 0) {
                showNotification('{{ __("file.warning") }}', "Please enter an amount greater than zero", 'warning');
                return;
            }

            const payload = {
                patient_id: document.getElementById('patient-select').value,
                doctor_id: document.getElementById('doctor-select')?.value || null,
                items: cart.map(item => ({
                    type: item.type,
                    id: item.id,
                    quantity: item.quantity,
                    doctor_id: item.doctorId
                })),
                tax_rate: parseFloat(document.getElementById('tax-input')?.value) || 0,
                discount_amount: parseFloat(document.getElementById('discount-input')?.value) || 0,
                payment_method: document.getElementById('partial-method').value,
                payment_reference: document.getElementById('partial-reference').value.trim(),
                notes: '',
                amount_paid_now: amount,
                appointment_id: window.preselectedAppointmentId || null,
                is_printed: document.getElementById('partial-print-invoice').checked,
                _token: document.querySelector('meta[name="csrf-token"]')?.content
            };

            fetch('{{ route("invoices.pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': payload._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || '{{ __('file.payment_failed') }}');
                    }

                    if (data.is_printed) {
                        const printUrl = '{{ url("") }}/invoices/' + data.invoice_id + '/print?redirect=pos';
                        printInvoice(printUrl);
                    }

                    showNotification('{{ __('file.payment_recorded_success', ['invoice' => ':invoice']) }}'.replace(':invoice', data.invoice_number), 'success');

                    clearCart();
                    closePartialModal();
                    loadRegisterStatus();
                })
                .catch(err => {
                    showNotification('Error: ' + err.message, 'error');
                });
        }

        function openPaymentModal(method) {
            if (!currentRegister) {
                showNotification('{{ __("file.error") }}', 'No cash register is currently open. You must open a register first.', 'error');
                showOpenRegisterModal();
                return;
            }
            selectedPaymentMethod = method;
            const modal = document.getElementById('payment-modal');
            const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace(/[^0-9.]/g, '')) || 0;
            document.getElementById('modal-grand-total').textContent = '{{ $currency_code }} ' + grandTotal.toFixed(2);
            document.getElementById('cash-section').classList.add('hidden');
            document.getElementById('card-section').classList.add('hidden');
            document.getElementById('other-section').classList.add('hidden');
            if (method === 'cash') {
                document.getElementById('cash-section').classList.remove('hidden');
                document.getElementById('cash-received').value = '0.00';
                updateCashChange();
            } else if (method === 'card') {
                document.getElementById('card-section').classList.remove('hidden');
            } else if (method === 'other') {
                document.getElementById('other-section').classList.remove('hidden');
            }
            modal.classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }

        function addCashDenomination(amount) {
            const input = document.getElementById('cash-received');
            const current = parseFloat(input.value) || 0;
            
            // If it's the first time and value is 0, replace it, otherwise add
            if (current === 0) {
                input.value = amount.toFixed(2);
            } else {
                input.value = (current + amount).toFixed(2);
            }
            updateCashChange();
        }

        function clearCashReceived() {
            document.getElementById('cash-received').value = '0.00';
            updateCashChange();
        }

        function updateCashChange() {
            const grandTotal = parseFloat(document.getElementById('modal-grand-total').textContent.replace(/[^0-9.]/g, '')) || 0;
            const received = parseFloat(document.getElementById('cash-received').value) || 0;
            const change = Math.max(0, received - grandTotal);
            document.getElementById('cash-change').textContent = '{{ $currency_code }} ' + change.toFixed(2);
            const btn = document.getElementById('complete-payment-btn');
            if (received >= grandTotal) {
                btn.classList.remove('opacity-60');
                btn.disabled = false;
            } else {
                btn.classList.add('opacity-60');
                btn.disabled = true;
            }
        }

        function clearCart() {
            cart = [];
            document.getElementById('patient-select').value = '';
            document.getElementById('discount-input').value = '0';
            renderCart();
            updateTotals();
        }

        function clearAll() {
            cart = [];
            document.getElementById('patient-select').value = '';
            document.getElementById('discount-input').value = '0';

            const doctorSelect = document.getElementById('doctor-select');
            if (doctorSelect) doctorSelect.value = '';

            // Reset ALL category buttons to inactive
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
            });

            // Activate "All" category
            const allBtn = document.querySelector('.category-btn[onclick="filterCategory(\'all\')"]');
            if (allBtn) {
                allBtn.classList.add('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
                allBtn.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
            }

            // Show all products
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = 'block';
            });

            // Clean treatments
            document.querySelectorAll('#treatments-container .product-card').forEach(el => el.remove());
            const placeholder = document.getElementById('treatments-placeholder');
            if (placeholder) {
                placeholder.style.display = 'block';
                placeholder.innerHTML = '{{ __("file.select_doctor_for_treatments") }}';
            }

            renderCart();
            updateTotals();
        }

        function filterCategory(category) {
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
            });
            event.target.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
            event.target.classList.add('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
            document.querySelectorAll('.product-card').forEach(product => {
                product.style.display = (category === 'all' || product.dataset.category === category) ? 'block' : 'none';
            });
        }

        function processPayment() {
            if (!currentRegister) {
                showNotification("No active cash register session.\nPlease open the register.", 'error');
                showOpenRegisterModal();
                return;
            }
            const patientId = document.getElementById('patient-select').value;
            if (!patientId) return showNotification('Please select a patient first', 'error');
            if (cart.length === 0) return showNotification('Cart is empty', 'error');
            const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace(/[^0-9.]/g, ''));
            if (selectedPaymentMethod === 'cash') {
                const amountReceived = parseFloat(document.getElementById('cash-received').value) || 0;
                if (amountReceived < grandTotal) return showNotification('Insufficient amount received.', 'error');
            }
            const formData = {
                patient_id: patientId,
                items: cart.map(item => ({ type: item.type, id: item.id, quantity: item.quantity, doctor_id: item.doctorId })),
                tax_rate: parseFloat(document.getElementById('tax-input').value) || 0,
                discount_amount: parseFloat(document.getElementById('discount-input').value) || 0,
                payment_method: selectedPaymentMethod,
                amount_paid_now: selectedPaymentMethod === 'cash'
                    ? parseFloat(document.getElementById('cash-received').value) || 0
                    : grandTotal,
                appointment_id: window.preselectedAppointmentId || null,
                notes: '',
                is_printed: document.getElementById('print-invoice').checked,
                _token: '{{ csrf_token() }}'
            };
            fetch('{{ route("invoices.pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_printed) {
                            const printUrl = '{{ url("") }}/invoices/' + data.invoice_id + '/print?redirect=pos';
                            printInvoice(printUrl);
                        }

                        showNotification(data.message || '{{ __("file.sale_completed") }}', 'success');

                        closePaymentModal();
                        clearCart();
                        loadRegisterStatus();
                    } else {
                        showNotification('{{ __('file.error') }}: ' + (data.message || '{{ __('file.payment_failed') }}'), 'error');
                    }
                })
                .catch(() => showNotification('Network error. Please try again.', 'error'));
        }

        function showAppointmentSummary() {
            if (!window.currentAppointment) return;
            const modal = document.getElementById('appointment-summary-modal');
            const content = document.getElementById('appointment-summary-content');
            const appointment = window.currentAppointment;

            let treatmentsHtml = '';
            if (appointment.treatments && appointment.treatments.length > 0) {
                treatmentsHtml = `
                                                                                                    <div class="mt-6">
                                                                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider flex items-center gap-2">
                                                                                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                                                                            {{ __('file.treatments') }}
                                                                                                        </h4>
                                                                                                        <div class="space-y-2">
                                                                                                            ${appointment.treatments.map(t => `
                                                                                                                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                                                                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${t.name}</span>
                                                                                                                    <span class="text-xs font-bold px-2 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full">{{ __('file.qty') }}: ${t.pivot.quantity}</span>
                                                                                                                </div>
                                                                                                            `).join('')}
                                                                                                        </div>
                                                                                                    </div>`;
            }

            let medicationsHtml = '';
            const latestPrescription = appointment.prescriptions && appointment.prescriptions.length > 0
                ? [...appointment.prescriptions].sort((a, b) => new Date(b.prescription_date) - new Date(a.prescription_date))[0]
                : null;

            if (latestPrescription && latestPrescription.medications && latestPrescription.medications.length > 0) {
                medicationsHtml = `
                                                                                                    <div class="mt-6">
                                                                                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider flex items-center gap-2">
                                                                                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                                                                                            {{ __('file.prescription_items') }}
                                                                                                        </h4>
                                                                                                        <div class="space-y-2">
                                                                                                            ${latestPrescription.medications.map(m => {
                    const item = m.inventory_item;
                    let stockStatus = '';
                    if (item) {
                        const isOutOfStock = item.current_stock <= 0;
                        const isLowStock = item.current_stock <= item.minimum_stock_level;
                        stockStatus = isOutOfStock
                            ? '<span class="text-[10px] uppercase font-bold text-red-500 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded border border-red-200 dark:border-red-800">{{ __('file.out_of_stock') }}</span>'
                            : (isLowStock ? `<span class="text-[10px] uppercase font-bold text-orange-500 bg-orange-50 dark:bg-orange-900/20 px-1.5 py-0.5 rounded border border-orange-200 dark:border-orange-800">{{ __('file.low_stock') }}: ${item.current_stock}</span>` : `<span class="text-[10px] uppercase font-medium text-green-500 bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded border border-green-200 dark:border-green-800">{{ __('file.in_stock') }}: ${item.current_stock}</span>`);
                    } else {
                        stockStatus = '<span class="text-[10px] uppercase font-medium text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-1.5 py-0.5 rounded border border-gray-200 dark:border-gray-600">{{ __('file.no_linked_inventory') }}</span>';
                    }
                    return `
                                                                                                                    <div class="flex flex-col bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                                                                                                        <div class="flex justify-between items-start mb-1">
                                                                                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">${m.name}</span>
                                                                                                                            <span class="text-xs font-bold px-2 py-1 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 rounded-full">{{ __('file.qty') }}: ${(m.per_day || 1) * (m.duration_days || 1)}</span>
                                                                                                                        </div>
                                                                                                                        <div class="flex items-center gap-2">
                                                                                                                            ${stockStatus}
                                                                                                                            <span class="text-[10px] text-gray-400 font-medium">${m.dosage || ''} ${m.frequency || ''}</span>
                                                                                                                        </div>
                                                                                                                    </div>`;
                }).join('')}
                                                                                                        </div>
                                                                                                    </div>`;
            }

            content.innerHTML = `
                                                                                                <div class="space-y-6">
                                                                                                    <div class="bg-indigo-50/50 dark:bg-indigo-900/10 p-4 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
                                                                                                        <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                                                                                            <div>
                                                                                                                <p class="text-[10px] uppercase font-bold text-indigo-400 dark:text-indigo-500 tracking-wider mb-0.5">{{ __('file.appointment_id') }}</p>
                                                                                                                <p class="text-sm font-bold text-gray-900 dark:text-white">#${appointment.appointment_number || appointment.id}</p>
                                                                                                            </div>
                                                                                                            <div>
                                                                                                                <p class="text-[10px] uppercase font-bold text-indigo-400 dark:text-indigo-500 tracking-wider mb-0.5">{{ __('file.date') }}</p>
                                                                                                                <p class="text-sm font-bold text-gray-900 dark:text-white">${new Date(appointment.scheduled_start).toLocaleDateString()}</p>
                                                                                                            </div>
                                                                                                            <div>
                                                                                                                <p class="text-[10px] uppercase font-bold text-indigo-400 dark:text-indigo-500 tracking-wider mb-0.5">{{ __('file.patient') }}</p>
                                                                                                                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">${appointment.patient.first_name} ${appointment.patient.last_name}</p>
                                                                                                            </div>
                                                                                                            <div>
                                                                                                                <p class="text-[10px] uppercase font-bold text-indigo-400 dark:text-indigo-500 tracking-wider mb-0.5">{{ __('file.doctor') }}</p>
                                                                                                                <p class="text-sm font-bold text-gray-900 dark:text-white">Dr. ${appointment.doctor.first_name} ${appointment.doctor.last_name}</p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    ${appointment.reason_for_visit ? `
                                                                                                        <div class="px-1">
                                                                                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('file.reason_for_visit') }}</h4>
                                                                                                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic border-l-4 border-indigo-200 dark:border-indigo-800 pl-3 py-1 bg-gray-50/50 dark:bg-gray-800/50 rounded-r-lg">${appointment.reason_for_visit}</p>
                                                                                                        </div>` : ''}
                                                                                                    ${treatmentsHtml}
                                                                                                    ${medicationsHtml}
                                                                                                </div>`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAppointmentSummary() {
            const modal = document.getElementById('appointment-summary-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function saveAsDraft() {
            if (cart.length === 0) return showNotification('Cart is empty', 'error');
            showNotification('Draft saved successfully', 'success');
        }

        function showSalesStats() {
            if (!currentRegister) {
                return showNotification('{{ __("file.register_not_open") }}', 'error');
            }

            // Fetch latest data to be accurate
            fetch('{{ route("cash-registers.current") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.open && data.register) {
                        const r = data.register;
                        
                        // Populate Header
                        document.getElementById('stats-session-info').textContent = `Session started: ${r.opened_at_formatted}`;
                        
                        // Populate Cards
                        const totalSales = (parseFloat(r.cash_sales_formatted.replace(/,/g, '')) + parseFloat(r.card_sales_formatted.replace(/,/g, ''))).toLocaleString(undefined, {minimumFractionDigits: 2});
                        document.getElementById('stats-total-sales').textContent = `{{ $currency_code }}${totalSales}`;
                        document.getElementById('stats-cash-sales').textContent = `{{ $currency_code }}${r.cash_sales_formatted}`;
                        document.getElementById('stats-card-sales').textContent = `{{ $currency_code }}${r.card_sales_formatted}`;
                        
                        const totalOutflow = (parseFloat(r.expenses_total_formatted.replace(/,/g, '')) + parseFloat(r.purchases_total_formatted.replace(/,/g, ''))).toLocaleString(undefined, {minimumFractionDigits: 2});
                        document.getElementById('stats-total-outflow').textContent = `{{ $currency_code }}${totalOutflow}`;
                        document.getElementById('stats-expenses').textContent = `{{ $currency_code }}${r.expenses_total_formatted}`;
                        document.getElementById('stats-purchases').textContent = `{{ $currency_code }}${r.purchases_total_formatted}`;
                        
                        document.getElementById('stats-expected-cash').textContent = `{{ $currency_code }}${r.expected_closing_formatted}`;
                        
                        // Populate Transactions
                        const tbody = document.getElementById('stats-transactions-body');
                        tbody.innerHTML = '';
                        
                        if (r.transactions && r.transactions.length > 0) {
                            r.transactions.forEach(t => {
                                const row = document.createElement('tr');
                                row.className = 'hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors';
                                row.innerHTML = `
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">${t.happened_at_formatted}</td>
                                    <td class="px-4 py-2 font-medium dark:text-gray-200">${t.type_formatted}</td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">${t.reference}</td>
                                    <td class="px-4 py-2 text-right font-bold ${t.is_outflow ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">
                                        {{ $currency_code }}${t.amount_formatted}
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No transactions recorded yet in this session.</td></tr>`;
                        }

                        document.getElementById('sales-stats-modal').classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error fetching stats:', err);
                    showNotification('Error loading statistics', 'error');
                });
        }

        function closeSalesStats() {
            document.getElementById('sales-stats-modal').classList.add('hidden');
        }

        function loadRegisterStatus() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ route("cash-registers.current") }}', true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        var label = document.getElementById('register-label');
                        var subtitle = document.getElementById('register-subtitle');
                        if (label && subtitle) {
                            if (data.open && data.register) {
                                currentRegister = data.register;
                                label.textContent = `{{ __('file.register') }}: ${data.register.id} ({{ __('file.open') }})`;
                                subtitle.innerHTML = `<span class="text-green-600 dark:text-green-400 font-medium flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                    {{ __('file.expected_cash') }}: {{ $currency_code }}${data.register.expected_closing_formatted}
                                </span>`;
                            } else {
                                currentRegister = null;
                                label.textContent = '{{ __('file.register') }}: {{ __('file.not_open') }}';
                                subtitle.innerHTML = '<span class="text-red-600 dark:text-red-400">{{ __('file.click_to_open_register') }}</span>';
                            }
                        }
                    } catch (e) {
                        console.error("JSON parse error:", e);
                    }
                }
            };
            xhr.onerror = function () {
                console.error("Network error");
            };
            xhr.send();
        }

        document.getElementById('open-register-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = '{{ __('file.opening') }}...';

            const formData = new FormData(this);

            fetch('{{ route("cash-registers.open") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('{{ __('file.register_opened_successfully') }}', 'success');
                        closeOpenRegisterModal();
                        loadRegisterStatus();  // Refresh status bar
                    } else {
                        showNotification(data.message || '{{ __('file.failed_to_open_register') }}', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Network error: ' + error.message, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
        });


        function showOpenRegisterModal() {
            document.getElementById('open-register-modal').classList.remove('hidden');
        }

        function closeOpenRegisterModal() {
            document.getElementById('open-register-modal').classList.add('hidden');
        }

        function showRegisterDetails() {
            if (!currentRegister) {
                showOpenRegisterModal();
                return;
            }
            const content = document.getElementById('register-details-content');
            if (!content) return;
            content.innerHTML = `
                                                                                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.register_id") }}</span>
                                                                                                <span class="text-sm font-semibold">#${currentRegister.id}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.opened_at") }}</span>
                                                                                                <span class="text-sm font-semibold">${currentRegister.opened_at_formatted}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.opening_balance") }}</span>
                                                                                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $currency_code }}${currentRegister.opening_balance_formatted}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.cash_sales") }}</span>
                                                                                                <span class="text-sm font-semibold">{{ $currency_code }}${currentRegister.cash_sales_formatted || '0.00'}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.card_sales") }}</span>
                                                                                                <span class="text-sm font-semibold">{{ $currency_code }}${currentRegister.card_sales_formatted || '0.00'}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.total_expenses") }}</span>
                                                                                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $currency_code }}${currentRegister.expenses_total_formatted || '0.00'}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.total_purchases") }}</span>
                                                                                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $currency_code }}${currentRegister.purchases_total_formatted || '0.00'}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center">
                                                                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __("file.total_transactions") }}</span>
                                                                                                <span class="text-sm font-semibold">${currentRegister.transaction_count || 0}</span>
                                                                                            </div>
                                                                                            <div class="py-3 flex justify-between items-center font-bold">
                                                                                                <span class="text-base text-gray-900 dark:text-white">{{ __("file.expected_cash") }}</span>
                                                                                                <span class="text-base text-gray-900 dark:text-white">{{ $currency_code }}${currentRegister.expected_closing_formatted || '0.00'}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                        `;
            document.getElementById('register-details-modal').classList.remove('hidden');
        }

        function closeRegisterDetailsModal() {
            document.getElementById('register-details-modal').classList.add('hidden');
        }

        function showCloseRegisterForm() {
            const counted = prompt('{{ __("file.enter_counted_cash") }}', currentRegister.expected_closing_formatted || "0.00");
            if (counted === null) return;
            const amount = parseFloat(counted);
            if (isNaN(amount) || amount < 0) {
                showNotification('{{ __("file.error") }}', '{{ __("file.please_enter_a_valid_amount") }}', 'error');
                return;
            }
            if (!confirm('{{ __("file.close_register_confirm") }}' + `\n{{ __("file.counted") }}: $${amount.toFixed(2)} \n{{ __("file.expected") }}: $${currentRegister.expected_closing_formatted || "?"} `)) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("cash-registers.close", ":id") }}'.replace(':id', currentRegister.id), true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            showNotification('{{ __("file.success") }}', '{{ __("file.register_closed_successfully") }}' + `\n{{ __("file.difference") }}: $${data.difference || 0} `, 'success');
                            currentRegister = null;
                            loadRegisterStatus();
                            closeRegisterDetailsModal();
                            window.location.href = '{{ route("home") }}';
                        } else {
                            showNotification('{{ __("file.error") }}', data.message || "Failed to close register", 'error');
                        }
                    } catch (e) {
                        showNotification('{{ __("file.error") }}', "Response error", 'error');
                    }
                } else {
                    showNotification('{{ __("file.error") }}', "Server error: " + xhr.status, 'error');
                }
            };
            xhr.onerror = function () {
                showNotification('{{ __("file.error") }}', "Connection error", 'error');
            };
            xhr.send(JSON.stringify({
                actual_closing_balance: amount,
                notes: '{{ __('file.closed_from_pos') }}'
            }));
        }

        function showCompletedAppointments() {
            document.getElementById('completed-appointments-modal').classList.remove('hidden');
            const content = document.getElementById('completed-appointments-content');
            
            content.innerHTML = `
                <div class="flex flex-col items-center gap-3 py-8">
                    <svg class="w-8 h-8 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.loading') }}...</span>
                </div>
            `;

            fetch('{{ route("invoices.pos.completed-appointments") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.appointments.length > 0) {
                        let html = '<div class="divide-y divide-gray-100 dark:divide-gray-700/50">';
                        data.appointments.forEach(app => {
                            html += `
                                <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-bold px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded">#${app.appointment_number}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">${app.completed_at}</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">${app.patient_name}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Dr. ${app.doctor_name}</p>
                                    </div>
                                    <div>
                                        <button onclick="loadCompletedAppointment(${app.id})" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-sm font-medium rounded-lg transition-colors">
                                            {{ __('file.load') ?? 'Load' }}
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = `
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('file.no_completed_appointments') ?? 'No Completed Appointments' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('file.no_completed_appointments_message') ?? 'There are no appointments ready for billing at this time.' }}</p>
                            </div>
                        `;
                    }
                } else {
                    throw new Error(data.message || 'Server returned an error');
                }
            })
            .catch(err => {
                console.error('Error fetching completed appointments:', err);
                content.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium">{{ __('file.error_fetching_data') ?? 'Error fetching data' }}</p>
                    </div>
                `;
            });
        }

        function closeCompletedAppointments() {
            document.getElementById('completed-appointments-modal').classList.add('hidden');
        }

        function loadCompletedAppointment(id) {
            window.location.href = '{{ route("invoices.pos") }}?appointment_id=' + id;
        }

        let lastInvoiceId = null;

        function showLastTransaction() {
            fetch('{{ route("invoices.pos.last-transaction") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('last-inv-number').textContent = data.invoice_number;
                        document.getElementById('last-inv-patient').textContent = data.patient_name;
                        document.getElementById('last-inv-time').textContent = data.time;
                        document.getElementById('last-inv-method').textContent = data.payment_method;
                        document.getElementById('last-inv-total').textContent = '{{ $currency_code }}' + data.total;
                        lastInvoiceId = data.id;
                        document.getElementById('last-transaction-modal').classList.remove('hidden');
                    } else {
                        showNotification('{{ __("file.info") }}', data.message || 'No transactions found', 'info');
                    }
                })
                .catch(err => {
                    console.error('Error fetching last transaction:', err);
                    showNotification('{{ __("file.error") }}', 'Failed to fetch last transaction', 'error');
                });
        }

        function closeLastTransaction() {
            document.getElementById('last-transaction-modal').classList.add('hidden');
        }

        function viewInvoice() {
            if (lastInvoiceId) {
                window.location.href = '{{ url("invoices") }}/' + lastInvoiceId;
            }
        }

        function printLastInvoice() {
            if (lastInvoiceId) {
                const printUrl = '{{ url("invoices") }}/' + lastInvoiceId + '/print?redirect=pos';
                printInvoice(printUrl);
            }
        }

        document.getElementById('search-products').addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = name.includes(term) ? 'block' : 'none';
            });
        });

        function updateTime() {
            const now = new Date();
            const options = { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
            const timeElement = document.getElementById('current-time');
            if (timeElement) timeElement.textContent = now.toLocaleString('en-US', options);
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateTotals();
            updateTime();
            setInterval(updateTime, 60000);
            if (window.preselectedPatientId) {
                const patientSelect = document.getElementById('patient-select');
                if (patientSelect) patientSelect.value = window.preselectedPatientId;
            }
            if (window.preloadedItems && window.preloadedItems.length > 0) {
                window.preloadedItems.forEach(item => {
                    const key = `${item.type}-${item.id}`; // Fixed space in key
                    if (cart.some(cartItem => cartItem.key === key)) return;
                    cart.push({
                        key,
                        type: item.type,
                        id: item.id,
                        name: item.name + (item.source ? ` (${item.source})` : ''),
                        price: parseFloat(item.price),
                        quantity: parseInt(item.quantity) || 1,
                        source: item.source || null,
                        doctorId: item.doctor_id || null
                    });
                });
                renderCart();
                updateTotals();
            }
            loadRegisterStatus();
        });

        function printInvoice(url) {
            const iframe = document.getElementById('print-iframe');
            if (iframe) {
                iframe.src = url;
            }
        }

    </script>

@endsection