<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 shadow-xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-all duration-300 ease-in-out flex flex-col overflow-hidden border-r border-gray-200 dark:border-gray-700"
       x-data="{ sidebarOpen: $store.sidebar.open }"
       :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }"
       @click.away="if (window.innerWidth < 1024) $store.sidebar.open = false">

    <div class="absolute inset-y-0 right-0 w-0.5 bg-gradient-to-b from-green-400 via-green-500 to-green-600 dark:from-green-500 dark:via-green-600 dark:to-green-700"></div>

    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-transparent dark:from-gray-800 dark:to-transparent">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            @if($clinic_logo)
                <img src="{{ $clinic_logo }}" alt="Clinic Logo" class="sidebar-text h-9 w-9 rounded-lg object-cover ring-2 ring-green-500/20">
            @else
                <div class="sidebar-text h-9 w-9 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5"/>
                    </svg>
                </div>
            @endif
            <span class="text-xl font-bold sidebar-text truncate" style="color: {{ $primary_color }} !important;">
                {{ $clinic_name }}
            </span>
        </a>

        <div class="flex items-center space-x-2">
            <button id="toggle-sidebar" class="hidden lg:block text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors" @click="$store.sidebar.toggle()">
                <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': !$store.sidebar.open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-red-:600 dark:text-gray-400 dark:hover:text-red-400 transition-colors" @click="$store.sidebar.open = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 mt-6 space-y-1 px-3 overflow-y-auto overflow-x-hidden">
        @auth
            <a href="{{ route('dashboard') }}"
            class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
            style="background-color: {{ request()->routeIs('dashboard') ? $primary_color : 'transparent' }};
                    color: {{ request()->routeIs('dashboard') ? '#ffffff' : 'inherit' }};
                    box-shadow: {{ request()->routeIs('dashboard') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
            onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
            onmouseout="this.style.backgroundColor='{{ request()->routeIs('dashboard') ? $primary_color : 'transparent' }}';
                        this.style.color='{{ request()->routeIs('dashboard') ? '#ffffff' : 'inherit' }}';
                        this.style.boxShadow='{{ request()->routeIs('dashboard') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'"
            data-tooltip="{{ __('file.dashboard') }}">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="ml-3 sidebar-text">{{ __('file.dashboard') }}</span>
            </a>

            @role('admin')
                <div x-data="{ open: {{ request()->routeIs('doctors.*') || request()->routeIs('doctor-schedules.*') || request()->routeIs('specializations.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.doctors') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('doctors.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('doctors.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('doctors.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctors.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('doctors.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.doctors_list') }}
                        </a>
                        <a href="#" 
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('doctor-schedules.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('doctor-schedules.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-schedules.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('doctor-schedules.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.doctor_schedule') }}
                        </a>
                        <a href="{{ route('specializations.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('specializations.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('specializations.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('specializations.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('specializations.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.specializations') }}
                        </a>
                    </div>
                </div>

                <a href="{{ route('patients.index') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative"
                    style="background-color: {{ request()->routeIs('patients.*') ? $primary_color : 'transparent' }};
                            color: {{ request()->routeIs('patients.*') ? '#ffffff' : 'inherit' }};
                            box-shadow: {{ request()->routeIs('patients.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('patients.*') ? $primary_color : 'transparent' }}'; 
                                this.style.color='{{ request()->routeIs('patients.*') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('patients.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'"
                    data-tooltip="{{ __('file.patients') }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.patients') }}</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('appointments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                           class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.appointments') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('appointments.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('appointments.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('appointments.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('appointments.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('appointments.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.all_appointments') }}
                        </a>
                        <a href="{{ route('appointments.calendar') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('appointments.calendar') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('appointments.calendar') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('appointments.calendar') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('appointments.calendar') ? $primary_color : 'inherit' }}'">
                            {{ __('file.appointment_calendar') }}
                        </a>
                        <a href="{{ route('appointments.index') }}?status=pending"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->is('appointments') && request('status') === 'pending' ? $primary_color : 'inherit' }}; background-color: {{ request()->is('appointments') && request('status') === 'pending' ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->is('appointments') && request('status') === 'pending' ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->is('appointments') && request('status') === 'pending' ? $primary_color : 'inherit' }}'">
                            {{ __('file.appointment_requests') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('admin.prescriptions.*') || request()->routeIs('admin.medicine-templates.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.prescriptions') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('admin.prescriptions.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.prescriptions.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.prescriptions.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.prescriptions.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.prescriptions.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.all_prescriptions') }}
                        </a>
                        <a href="{{ route('admin.prescriptions.create') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.prescriptions.create') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.prescriptions.create') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.prescriptions.create') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.prescriptions.create') ? $primary_color : 'inherit' }}'">
                            {{ __('file.create_prescription') }}
                        </a>
                        <a href="{{ route('medication-templates.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.medicine-templates.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.medicine-templates.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.medicine-templates.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.medicine-templates.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.medicine_templates') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('admin.ambulance-calls.*') || request()->routeIs('admin.ambulances.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.ambulance') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('admin.ambulance-calls.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.ambulance-calls.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.ambulance-calls.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.ambulance-calls.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.ambulance-calls.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.ambulance_call_list') }}
                        </a>
                        <a href="{{ route('admin.ambulances.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.ambulances.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.ambulances.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.ambulances.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.ambulances.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.ambulance_list') }}
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.pharmacy.index') }}"
                class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative"
                style="background-color: {{ request()->routeIs('admin.pharmacy.*') ? $primary_color : 'transparent' }};
                        color: {{ request()->routeIs('admin.pharmacy.*') ? '#ffffff' : 'inherit' }};
                        box-shadow: {{ request()->routeIs('admin.pharmacy.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.pharmacy.*') ? $primary_color : 'transparent' }}'; 
                            this.style.color='{{ request()->routeIs('admin.pharmacy.*') ? '#ffffff' : 'inherit' }}';
                            this.style.boxShadow='{{ request()->routeIs('admin.pharmacy.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'"
                data-tooltip="{{ __('file.pharmacy') }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">{{ __('file.pharmacy') }}</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.billing') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('admin.invoices.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.invoices.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.invoices.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.invoices.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.invoices.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.invoices_list') }}
                        </a>
                        <a href="{{ route('admin.invoices.create') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.invoices.create') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.invoices.create') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.invoices.create') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.invoices.create') ? $primary_color : 'inherit' }}'">
                            {{ __('file.create_invoice') }}
                        </a>
                        <a href="{{ route('admin.payments.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.payments.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.payments.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.payments.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.payments.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.payments_history') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('departments.*') || request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.departments') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('departments.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('departments.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('departments.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('departments.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('departments.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.department_list') }}
                        </a>
                        <a href="{{ route('admin.services.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.services.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.services.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.services.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.services.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.services_offered') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('inventoryitems.*') || request()->routeIs('suppliers.*') || request()->routeIs('categories.*') || request()->routeIs('subcategories.*') || request()->routeIs('unit-of-measures.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-2-2m2 2l-2 2m2-2H4m4 14h8m-4-7v7m-4-4h8"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.inventory') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('inventory.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('inventory.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('inventory.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('inventory.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('inventory.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.inventory_list') }}
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('suppliers.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('suppliers.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('suppliers.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('suppliers.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.suppliers') }}
                        </a>
                        <a href="{{ route('categories.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('categories.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('categories.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('categories.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('categories.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.categories') }}
                        </a>
                        <a href="{{ route('unit-of-measures.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('unit-of-measures.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('unit-of-measures.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('unit-of-measures.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('unit-of-measures.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.unit_of_measures') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.attendance.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                           class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292c5.291 0 9.938-3.546 9.938-8.646C22 3.78 19.756 1.938 17 1.938c-2.02 0-3.938 1.027-5 2.646zM12 4.354a4 4 0 100 5.292"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 21H9a6 6 0 01-6-6v-2a2 2 0 012-2h14a2 2 0 012 2v2a6 6 0 01-6 6z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('User & Access') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('admin.users.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.users.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.users.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.users.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.users.index') ? $primary_color : 'inherit' }}'">
                           {{ __('file.user_management') }}
                        </a>
                        <a href="{{ route('admin.roles.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.roles.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.roles.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.roles.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.roles.*') ? $primary_color : 'inherit' }}'">
                            Roles Management
                        </a>
                        <a href="{{ route('admin.attendance.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.attendance.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.attendance.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.attendance.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.attendance.*') ? $primary_color : 'inherit' }}'">
                            Attendance
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.reports') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('admin.reports.appointments') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.reports.appointments') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.reports.appointments') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.reports.appointments') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.reports.appointments') ? $primary_color : 'inherit' }}'">
                            {{ __('file.appointment_reports') }}
                        </a>
                        <a href="{{ route('admin.reports.financial') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.reports.financial') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.reports.financial') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.reports.financial') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.reports.financial') ? $primary_color : 'inherit' }}'">
                            {{ __('file.financial_reports') }}
                        </a>
                        <a href="{{ route('admin.reports.patient-visits') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.reports.patient-visits') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.reports.patient-visits') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.reports.patient-visits') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.reports.patient-visits') ? $primary_color : 'inherit' }}'">
                            {{ __('file.patient_visit_reports') }}
                        </a>
                        <a href="{{ route('admin.reports.inventory') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('admin.reports.inventory') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('admin.reports.inventory') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('admin.reports.inventory') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('admin.reports.inventory') ? $primary_color : 'inherit' }}'">
                            {{ __('file.inventory_reports') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                            style="background-color: transparent; color: inherit; box-shadow: none"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ml-3 sidebar-text">{{ __('file.settings') }}</span>
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                        <a href="{{ route('settings.general') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('settings.general') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('settings.general') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('settings.general') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('settings.general') ? $primary_color : 'inherit' }}'">
                            {{ __('file.general_settings') }}
                        </a>
                    </div>
                </div>

            @else
                @role('therapist')
                    <a href="{{ route('therapist.appointments.index') }}"
                       class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.my_appointments') }}</span>
                    </a>
                @endrole

                @role('primary-therapist')
                    <a href="{{ route('patients.index') }}"
                       class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative {{ request()->routeIs('patients.*') ? 'bg-green-500 text-white shadow-md shadow-green-500/30 dark:shadow-green-600/40' : 'text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400' }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.patients') }}</span>
                    </a>
                @endrole

                @role('counter')
                    <a href="{{ route('counter.invoices.index') }}"
                       class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.issue_invoice') }}</span>
                    </a>
                @endrole

                @role('hr')
                    <a href="{{ route('hr.dashboard') }}"
                       class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.real_time_ops') }}</span>
                    </a>
                @endrole

                @role('patient')
                    <a href="{{ route('patient.book') }}"
                       class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.book_appointment') }}</span>
                    </a>
                @endrole
            @endrole

        @else
            <a href="{{ route('login') }}"
               class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16v-4m0 0V8m0 4h4m-4 0H7"/>
                </svg>
                <span class="ml-3 sidebar-text">{{ __('file.log_in') }}</span>
            </a>
            <a href="{{ route('register') }}"
               class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM6 21v-1a4 4 0 014-4h4a4 4 0 014 4v1"/>
                </svg>
                <span class="ml-3 sidebar-text">{{ __('file.register') }}</span>
            </a>
        @endauth
    </nav>
</aside>

<div x-data="{ sidebarOpen: $store.sidebar.open }"
     x-show="sidebarOpen && window.innerWidth < 1024"
     @click="$store.sidebar.open = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
     x-cloak>
</div>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: window.innerWidth >= 1024,
        toggle() {
            this.open = !this.open;
            if (!this.open) this.collapseAllMenuItems();
        },
        collapseAllMenuItems() {
            document.querySelectorAll('[x-data]').forEach(el => {
                const data = Alpine.$data(el);
                if (data && typeof data.open === 'boolean') data.open = false;
            });
        }
    });
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>