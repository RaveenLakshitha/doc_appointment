<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-white dark:bg-gray-800 shadow-lg border-r dark:border-gray-700 transition-all duration-300 z-50 flex flex-col lg:translate-x-0 -translate-x-full" 
style="width: 16rem; max-height: 100vh;">
    
    <!-- Logo Section with Collapse Button -->
    <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 flex-shrink-0">
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
        <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex-shrink-0" aria-label="Toggle sidebar">
            <!-- Icon when sidebar is EXPANDED (pointing left - will collapse) -->
            <svg id="icon-expanded" class="w-5 h-5 text-gray-600 dark:text-gray-300 transition-opacity duration-200 opacity-100" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            
            <!-- Icon when sidebar is COLLAPSED (pointing right - will expand) -->
            <svg id="icon-collapsed" class="w-5 h-5 text-gray-600 dark:text-gray-300 transition-opacity duration-200 opacity-0 absolute" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 space-y-2 flex-1 overflow-y-auto overflow-x-hidden">
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
                <div x-data="{ open: {{ request()->routeIs('doctors.*') || request()->routeIs('doctor-schedules.*') || request()->routeIs('age-groups.*') || request()->routeIs('specializations.*') ? 'true' : 'false' }} }">
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
                        <a href="{{ route('doctor-schedules.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('doctor-schedules.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('doctor-schedules.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-schedules.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('doctor-schedules.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.All_Schedules') }}
                        </a>
                        <a href="{{ route('doctor-schedules.calendar') }}"" 
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('doctor-schedules.calendar') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('doctor-schedules.calendar') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-schedules.calendar') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('doctor-schedules.calendar') ? $primary_color : 'inherit' }}'">
                            {{ __('file.doctor_schedule') }}
                        </a>
                        <a href="{{ route('specializations.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('specializations.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('specializations.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('specializations.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('specializations.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.specializations') }}
                        </a>
                        <a href="{{ route('age-groups.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('age-groups.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('age-groups.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('age-groups.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('age-groups.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.age_groups') }}
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

                <div x-data="{ open: {{ request()->routeIs('appointments.*') || request()->routeIs('queues.*') ? 'true' : 'false' }} }">
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

                    <a href="{{ route('queues.daily') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('queues.daily') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('queues.daily') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('queues.daily') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('queues.daily') ? $primary_color : 'inherit' }}'">
                        {{ __('file.Queues') }}
                    </a>
                </div>
            </div>

                <div x-data="{ open: {{ request()->routeIs('prescriptions.*') || request()->routeIs('medicine-templates.*') ? 'true' : 'false' }} }">
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
                        <a href="{{ route('prescriptions.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('prescriptions.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('prescriptions.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('prescriptions.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('prescriptions.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.all_prescriptions') }}
                        </a>
                        <a href="{{ route('medicine-templates.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('medicine-templates.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('medicine-templates.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('medicine-templates.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('medicine-templates.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.medicine_templates') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? 'true' : 'false' }} }">
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
                        <a href="{{ route('invoices.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('invoices.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('invoices.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('invoices.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('invoices.index') ? $primary_color : 'inherit' }}'">
                            {{ __('file.invoices_list') }}
                        </a>
                        <a href="{{ route('invoices.pos') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('invoices.pos') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('invoices.pos') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('invoices.pos') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('invoices.pos') ? $primary_color : 'inherit' }}'">
                            {{ __('file.pos') }}
                        </a>
                        <a href="{{ route('payments.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('payments.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('payments.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('payments.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('payments.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.payments_history') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('departments.*') || request()->routeIs('services.*') ||request()->routeIs('treatments.*') || request()->routeIs('rooms.*') ? 'true' : 'false' }} }">
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
                        <a href="{{ route('rooms.index') }}"
                            class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                            style="color: {{ request()->routeIs('rooms.*') ? $primary_color : 'inherit' }}; 
                                    background-color: {{ request()->routeIs('rooms.*') ? $primary_color.'10' : 'transparent' }}"
                            onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                            onmouseout="this.style.backgroundColor='{{ request()->routeIs('rooms.*') ? $primary_color.'10' : 'transparent' }}'; 
                                        this.style.color='{{ request()->routeIs('rooms.*') ? $primary_color : 'inherit' }}'">
                                {{ __('file.rooms') }}
                            </a>
                        <a href="{{ route('services.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('services.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('services.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('services.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('services.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.services_offered') }}
                        </a>
                        <a href="{{ route('treatments.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('treatments.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('treatments.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('treatments.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('treatments.*') ? $primary_color : 'inherit' }}'">
                            {{ __('file.treatments') }}
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('inventory.*') || request()->routeIs('suppliers.*') || request()->routeIs('categories.*') || request()->routeIs('subcategories.*') || request()->routeIs('unit-of-measures.*') ? 'true' : 'false' }} }">
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

                <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
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
                        <a href="{{ route('users.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('users.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('users.index') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('users.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('users.index') ? $primary_color : 'inherit' }}'">
                           {{ __('file.user_management') }}
                        </a>
                        <a href="{{ route('roles.index') }}"
                           class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                           style="color: {{ request()->routeIs('roles.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('roles.*') ? $primary_color.'10' : 'transparent' }}"
                           onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                           onmouseout="this.style.backgroundColor='{{ request()->routeIs('roles.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('roles.*') ? $primary_color : 'inherit' }}'">
                            Roles Management
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('employees.*') || request()->routeIs('attendances.*') || request()->routeIs('leave-requests.*') || request()->routeIs('leave-types.*') || request()->routeIs('leave-entitlements.*')  ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative text-gray-700 dark:text-gray-300"
                        style="background-color: transparent; color: inherit; box-shadow: none"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">{{ __('file.hr') }}</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    <a href="{{ route('employees.index') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('employees.index') || request()->routeIs('employees.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('employees.*') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('employees.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('employees.*') ? $primary_color : 'inherit' }}'">
                        {{ __('file.employees') }}
                    </a>

                    <a href="{{ route('attendances.index') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('attendances.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('attendances.*') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('attendances.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('attendances.*') ? $primary_color : 'inherit' }}'">
                        {{ __('file.attendance') }}
                    </a>

                    <a href="{{ route('leave-requests.index') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('leave-requests.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('leave-requests.*') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('leave-requests.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('leave-requests.*') ? $primary_color : 'inherit' }}'">
                        {{ __('file.leave_requests') }}
                    </a>

                    <a href="{{ route('leave-types.index') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('leave-types.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('leave-types.*') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('leave-types.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('leave-types.*') ? $primary_color : 'inherit' }}'">
                        {{ __('file.leave_types') }}
                    </a>

                    <a href="{{ route('leave-entitlements.index') }}"
                    class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                    style="color: {{ request()->routeIs('leave-entitlements.*') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('leave-entitlements.*') ? $primary_color.'10' : 'transparent' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('leave-entitlements.*') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('leave-entitlements.*') ? $primary_color : 'inherit' }}'">
                        {{ __('file.leave_entitlements') }}
                    </a>
                </div>
            </div>

            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
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
                    <a href="{{ route('reports.appointments') }}"
                        class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                        style="color: {{ request()->routeIs('reports.appointments') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('reports.appointments') ? $primary_color.'10' : 'transparent' }}"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('reports.appointments') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('reports.appointments') ? $primary_color : 'inherit' }}'">
                        {{ __('file.appointment_reports') }}
                    </a>
                    <a href="{{ route('reports.financial') }}"
                        class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                        style="color: {{ request()->routeIs('reports.financial') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('reports.financial') ? $primary_color.'10' : 'transparent' }}"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('reports.financial') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('reports.financial') ? $primary_color : 'inherit' }}'">
                        {{ __('file.financial_reports') }}
                    </a>
                    <a href="{{ route('reports.inventory') }}"
                        class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                        style="color: {{ request()->routeIs('reports.inventory') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('reports.inventory') ? $primary_color.'10' : 'transparent' }}"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('reports.inventory') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('reports.inventory') ? $primary_color : 'inherit' }}'">
                        {{ __('file.inventory_reports') }}
                    </a>
                </div>
            </div>

            <div x-data="{ open: {{ request()->routeIs('settings.*') || request()->routeIs('cash-registers.*') || request()->routeIs('dropdowns.*')  ? 'true' : 'false' }} }">
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
                <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    <a href="{{ route('cash-registers.index') }}"
                        class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                        style="color: {{ request()->routeIs('cash-registers.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('cash-registers.index') ? $primary_color.'10' : 'transparent' }}"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('cash-registers.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('cash-registers.index') ? $primary_color : 'inherit' }}'">
                        {{ __('file.cash registers') }}
                    </a>
                </div>
                <div x-show="open" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    <a href="{{ route('dropdowns.index') }}"
                        class="block px-3 py-1.5 text-sm rounded-md transition-all duration-200"
                        style="color: {{ request()->routeIs('dropdowns.index') ? $primary_color : 'inherit' }}; background-color: {{ request()->routeIs('dropdowns.index') ? $primary_color.'10' : 'transparent' }}"
                        onmouseover="this.style.backgroundColor='{{ $primary_color }}10'; this.style.color='{{ $primary_color }}'"
                        onmouseout="this.style.backgroundColor='{{ request()->routeIs('dropdowns.index') ? $primary_color.'10' : 'transparent' }}'; this.style.color='{{ request()->routeIs('dropdowns.index') ? $primary_color : 'inherit' }}'">
                        {{ __('file.dropdowns') }}
                    </a>
                </div>
            </div>

            @else
            @endrole

        @role('doctor')

            <div class="mt-6 pt-4 border-t dark:border-gray-700">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2 sidebar-text">
                    Doctor Panel
                </p>

                <!-- My Calendar -->
                <a href="{{ route('doctor-panel.calendar') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
                    style="background-color: {{ request()->routeIs('doctor-panel.calendar') ? $primary_color : 'transparent' }};
                            color: {{ request()->routeIs('doctor-panel.calendar') ? '#ffffff' : 'inherit' }};
                            box-shadow: {{ request()->routeIs('doctor-panel.calendar') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; 
                                    this.style.color='#ffffff'; 
                                    this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-panel.calendar') ? $primary_color : 'transparent' }}';
                                this.style.color='{{ request()->routeIs('doctor-panel.calendar') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('doctor-panel.calendar') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">My Calendar</span>
                        
                        @if(request()->routeIs('doctor-panel.calendar'))
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Now</span>
                        @endif
                    </a>

                <a href="{{ route('doctor-panel.prescriptions.index') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
                    style="background-color: {{ request()->routeIs('doctor-panel.prescriptions.*') ? $primary_color : 'transparent' }};
                  color: {{ request()->routeIs('doctor-panel.prescriptions.*') ? '#ffffff' : 'inherit' }};
                  box-shadow: {{ request()->routeIs('doctor-panel.prescriptions.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-panel.prescriptions.*') ? $primary_color : 'transparent' }}';
                                this.style.color='{{ request()->routeIs('doctor-panel.prescriptions.*') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('doctor-panel.prescriptions.*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">My Prescriptions</span>
                        @if(request()->routeIs('doctor-panel.prescriptions.*'))
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Active</span>
                        @endif
                    </a>
                
                <a href="{{ route('doctor-panel.schedule-calendar') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
                    style="background-color: {{ request()->routeIs('doctor-panel.schedule-calendar*') ? $primary_color : 'transparent' }};
                            color: {{ request()->routeIs('doctor-panel.schedule-calendar*') ? '#ffffff' : 'inherit' }};
                            box-shadow: {{ request()->routeIs('doctor-panel.schedule-calendar*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-panel.schedule-calendar*') ? $primary_color : 'transparent' }}';
                                this.style.color='{{ request()->routeIs('doctor-panel.schedule-calendar*') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('doctor-panel.schedule-calendar*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">My Schedule</span>
                        @if(request()->routeIs('doctor-panel.schedule-calendar*'))
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Active</span>
                        @endif
                    </a>
                <a href="{{ route('doctor-panel.appointment-calendar') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
                    style="background-color: {{ request()->routeIs('doctor-panel.appointment-calendar*') ? $primary_color : 'transparent' }};
                            color: {{ request()->routeIs('doctor-panel.appointment-calendar*') ? '#ffffff' : 'inherit' }};
                            box-shadow: {{ request()->routeIs('doctor-panel.appointment-calendar*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-panel.appointment-calendar*') ? $primary_color : 'transparent' }}';
                                this.style.color='{{ request()->routeIs('doctor-panel.appointment-calendar*') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('doctor-panel.appointment-calendar*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4"/> <!-- check mark -->
                        </svg>
                        <span class="ml-3 sidebar-text">My Appointment</span>
                        @if(request()->routeIs('doctor-panel.appointment-calendar*'))
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Active</span>
                        @endif
                    </a>
                <a href="{{ route('doctor-panel.queue') }}"
                    class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative overflow-hidden"
                    style="background-color: {{ request()->routeIs('doctor-panel.queue*') ? $primary_color : 'transparent' }};
                            color: {{ request()->routeIs('doctor-panel.queue*') ? '#ffffff' : 'inherit' }};
                            box-shadow: {{ request()->routeIs('doctor-panel.queue*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}"
                    onmouseover="this.style.backgroundColor='{{ $primary_color }}'; this.style.color='#fff'; this.style.boxShadow='0 4px 15px {{ $primary_color }}40'"
                    onmouseout="this.style.backgroundColor='{{ request()->routeIs('doctor-panel.queue*') ? $primary_color : 'transparent' }}';
                                this.style.color='{{ request()->routeIs('doctor-panel.queue*') ? '#ffffff' : 'inherit' }}';
                                this.style.boxShadow='{{ request()->routeIs('doctor-panel.queue*') ? '0 4px 15px ' . $primary_color . '40' : 'none' }}'">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="ml-3 sidebar-text">My Queue</span>
                        @if(request()->routeIs('doctor-panel.queue*'))
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20">Active</span>
                        @endif
                </a>  

                    {{-- <!-- My Patients -->
                
                    <a href="{{ route('patients.my-patients') }}"  <!-- you need to create this route/controller -->
                   class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                   ... >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">My Patients</span>
                
                </a>

                <!-- Prescriptions I Wrote -->
                <a href="{{ route('prescriptions.my-prescriptions') }}"  <!-- create this route if needed -->
                   class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                   ... >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="ml-3 sidebar-text">My Prescriptions</span>
                </a>
 --}}

            </div>

        @else
        @endrole

        <!-- Divider + Profile / Logout (common for authenticated users) -->
        <div class="pt-4 mt-4 border-t dark:border-gray-700">
            <a href="#"
               class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all"
               ... >
                <svg class="h-5 w-5" ... > ... </svg>
                <span class="ml-3 sidebar-text">Profile</span>
            </a>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="ml-3 sidebar-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>


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

