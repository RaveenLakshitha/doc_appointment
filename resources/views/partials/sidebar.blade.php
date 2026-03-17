<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-white dark:bg-gray-800 shadow-lg border-r dark:border-gray-700 transition-all duration-300 z-50 flex flex-col lg:translate-x-0 -translate-x-full"
style="width: 16rem; max-height: 100vh;">

    <!-- Logo -->
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
            <svg id="icon-expanded" class="w-5 h-5 text-gray-600 dark:text-gray-300 opacity-100"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg id="icon-collapsed" class="w-5 h-5 text-gray-600 dark:text-gray-300 opacity-0 absolute"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav x-data="{ 
            activeGroup: '{{ 
                request()->routeIs('doctors.*','doctor-schedules.*','specializations.*','age-groups.*') ? 'doctors' : (
                request()->routeIs('appointments.*','queues.*') ? 'appointments' : (
                request()->routeIs('prescriptions.*','medicine-templates.*') ? 'prescriptions' : (
                request()->routeIs('invoices.*','payments.*','cash-registers.*', 'expense-categories.*', 'expenses.*') ? 'billing' : (
                request()->routeIs('departments.*','rooms.*','services.*','treatments.*') ? 'clinic' : (
                request()->routeIs('inventory.*','purchases.*','suppliers.*','categories.*','unit-of-measures.*') ? 'inventory' : (
                request()->routeIs('employees.*','attendances.*','leave-requests.*','leave-types.*','leave-entitlements.*') ? 'hr' : (
                request()->routeIs('reports.*') ? 'reports' : (
                request()->routeIs('users.*','roles.*','settings.*','dropdowns.*','admin.notification-settings.*') ? 'admin' : 'none'
            )))))))) }}'
        }" 
        class="p-4 space-y-1 flex-1 overflow-y-auto overflow-x-hidden">
        @auth

        {{-- ── DASHBOARD ──────────────────────────────────────────────────────── --}}
        @include('partials.sidebar-item', [
            'route'   => 'dashboard',
            'active'  => request()->routeIs('dashboard'),
            'label'   => __('file.dashboard'),
            'tooltip' => __('file.dashboard'),
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ])

        {{-- ── DOCTOR PANEL (only for doctor role users) ─────────────────────── --}}
        @if(auth()->user()->hasRole('doctor'))
        <div class="mt-4 pt-3 border-t dark:border-gray-700">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1 sidebar-text">
                {{ __('file.doctor_panel') }}
            </p>

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.calendar',
                'active'  => request()->routeIs('doctor-panel.calendar*'),
                'label'   => __('file.my_calendar'),
                'tooltip' => __('file.my_calendar'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
            ])

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.prescriptions.index',
                'active'  => request()->routeIs('doctor-panel.prescriptions.*'),
                'label'   => __('file.my_prescriptions'),
                'tooltip' => __('file.my_prescriptions'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            ])

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.schedule-calendar',
                'active'  => request()->routeIs('doctor-panel.schedule-calendar*'),
                'label'   => __('file.my_schedule'),
                'tooltip' => __('file.my_schedule'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
            ])

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.my-appointments',
                'active'  => request()->routeIs('doctor-panel.my-appointments*'),
                'label'   => __('file.my_appointments'),
                'tooltip' => __('file.my_appointments'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
            ])

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.queue',
                'active'  => request()->routeIs('doctor-panel.queue*'),
                'label'   => __('file.my_queue'),
                'tooltip' => __('file.my_queue'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            ])

            @include('partials.sidebar-item', [
                'route'   => 'doctor-panel.invoices.index',
                'active'  => request()->routeIs('doctor-panel.invoices.*'),
                'label'   => __('file.my_invoices'),
                'tooltip' => __('file.my_invoices'),
                'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            ])
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════════
             MAIN NAV — one flat list, no extra section headers
        ════════════════════════════════════════════════════════════════ --}}
        @if(auth()->user()->hasRole('admin') || !auth()->user()->hasRole('doctor'))
        <div class="mt-3 pt-3 border-t dark:border-gray-700 space-y-1">
            {{-- Doctors & Schedules --}}
            @canany(['doctors.index', 'doctor-schedules.index', 'specializations.index', 'age-groups.index'])
            <div x-data="{ group: 'doctors' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'doctors',
                    'label' => __('file.doctors'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                ])
                <div x-show="activeGroup === 'doctors'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('doctors.index')
                        @include('partials.sidebar-sub-item', ['route' => 'doctors.index',           'active' => request()->routeIs('doctors.*'),            'label' => __('file.doctors_list')])
                    @endcan
                    @can('doctor-schedules.index')
                        @include('partials.sidebar-sub-item', ['route' => 'doctor-schedules.index',  'active' => request()->routeIs('doctor-schedules.index'), 'label' => __('file.All_Schedules')])
                        @include('partials.sidebar-sub-item', ['route' => 'doctor-schedules.calendar','active' => request()->routeIs('doctor-schedules.calendar'),'label' => __('file.doctor_schedule')])
                    @endcan
                    @can('specializations.index')
                        @include('partials.sidebar-sub-item', ['route' => 'specializations.index',   'active' => request()->routeIs('specializations.*'),     'label' => __('file.specializations')])
                    @endcan
                    @can('age-groups.index')
                        @include('partials.sidebar-sub-item', ['route' => 'age-groups.index',        'active' => request()->routeIs('age-groups.*'),          'label' => __('file.age_groups')])
                    @endcan
                </div>
            </div>
            @endcanany
        </div>
        @endif

        {{-- Patients --}}
        @can('patients.index')
        @include('partials.sidebar-item', [
            'route'   => 'patients.index',
            'active'  => request()->routeIs('patients.*'),
            'label'   => __('file.patients'),
            'tooltip' => __('file.patients'),
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        ])
        @endcan

        @if(auth()->user()->hasRole('admin') || !auth()->user()->hasRole('doctor'))
        <div class="mt-3 pt-3 border-t dark:border-gray-700 space-y-1">

            {{-- Appointments --}}
            @canany(['appointments.index', 'queues.index', 'appointment_requests.index'])
            <div x-data="{ group: 'appointments' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'appointments',
                    'label' => __('file.appointments'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                ])
                <div x-show="activeGroup === 'appointments'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('appointments.index')
                        @include('partials.sidebar-sub-item', ['route' => 'appointments.index',          'active' => request()->routeIs('appointments.index'),    'label' => __('file.all_appointments')])
                        @include('partials.sidebar-sub-item', ['route' => 'appointments.calendar',       'active' => request()->routeIs('appointments.calendar'),  'label' => __('file.appointment_calendar')])
                    @endcan
                    @can('queues.index')
                        @include('partials.sidebar-sub-item', ['route' => 'queues.daily',                'active' => request()->routeIs('queues.*'),               'label' => __('file.Queues')])
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Prescriptions & Medicines --}}
            @canany(['prescriptions.index', 'medicine-templates.index'])
            <div x-data="{ group: 'prescriptions' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'prescriptions',
                    'label' => __('file.prescriptions'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                ])
                <div x-show="activeGroup === 'prescriptions'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('prescriptions.index')
                        @include('partials.sidebar-sub-item', ['route' => 'prescriptions.index',      'active' => request()->routeIs('prescriptions.*'),     'label' => __('file.all_prescriptions')])
                    @endcan
                    @can('medicine-templates.index')
                        @include('partials.sidebar-sub-item', ['route' => 'medicine-templates.index', 'active' => request()->routeIs('medicine-templates.*'), 'label' => __('file.medicine_templates')])
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Billing --}}
            @canany(['invoices.index', 'payments.index', 'cash-registers.index', 'expense-categories.index', 'expenses.index'])
            <div x-data="{ group: 'billing' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'billing',
                    'label' => __('file.billing'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                ])
                <div x-show="activeGroup === 'billing'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('invoices.index')
                        @include('partials.sidebar-sub-item', ['route' => 'invoices.index',       'active' => request()->routeIs('invoices.index'),     'label' => __('file.invoices_list')])
                        @include('partials.sidebar-sub-item', ['route' => 'invoices.pos',         'active' => request()->routeIs('invoices.pos'),       'label' => __('file.pos')])
                    @endcan
                    @can('payments.index')
                        @include('partials.sidebar-sub-item', ['route' => 'payments.index',       'active' => request()->routeIs('payments.*'),         'label' => __('file.payments_history')])
                    @endcan
                    @can('cash-registers.index')
                        @include('partials.sidebar-sub-item', ['route' => 'cash-registers.index', 'active' => request()->routeIs('cash-registers.*'),   'label' => __('file.cash_registers')])
                    @endcan
                    @can('expense-categories.index')
                        @include('partials.sidebar-sub-item', ['route' => 'expense-categories.index', 'active' => request()->getRequestUri() === route('expense-categories.index', [], false) || request()->routeIs('expense-categories.*'),   'label' => __('file.expense_categories')])
                    @endcan
                    @can('expenses.index')
                        @include('partials.sidebar-sub-item', ['route' => 'expenses.index', 'active' => request()->getRequestUri() === route('expenses.index', [], false) || request()->routeIs('expenses.*'),   'label' => __('file.expenses')])
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Clinic (Departments, Rooms, Services, Treatments) --}}
            @canany(['departments.index', 'rooms.index', 'services.index', 'treatments.index'])
            <div x-data="{ group: 'clinic' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'clinic',
                    'label' => __('file.clinic'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                ])
                <div x-show="activeGroup === 'clinic'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('departments.index')
                        @include('partials.sidebar-sub-item', ['route' => 'departments.index', 'active' => request()->routeIs('departments.*'), 'label' => __('file.department_list')])
                    @endcan
                    @can('rooms.index')
                        @include('partials.sidebar-sub-item', ['route' => 'rooms.index',       'active' => request()->routeIs('rooms.*'),       'label' => __('file.rooms')])
                    @endcan
                    @can('services.index')
                        @include('partials.sidebar-sub-item', ['route' => 'services.index',    'active' => request()->routeIs('services.*'),    'label' => __('file.services_offered') ?? __('file.services')])
                    @endcan
                    @can('treatments.index')
                        @include('partials.sidebar-sub-item', ['route' => 'treatments.index',  'active' => request()->routeIs('treatments.*'),  'label' => __('file.treatments')])
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Inventory --}}
            @canany(['inventory.index', 'purchases.index', 'suppliers.index', 'categories.index', 'unit-of-measures.index'])
            <div x-data="{ group: 'inventory' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'inventory',
                    'label' => __('file.inventory'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
                ])
                <div x-show="activeGroup === 'inventory'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('inventory.index')
                        @include('partials.sidebar-sub-item', ['route' => 'inventory.index',        'active' => request()->routeIs('inventory.*'),        'label' => __('file.inventory_list')])
                    @endcan
                    @can('suppliers.index')
                        @include('partials.sidebar-sub-item', ['route' => 'suppliers.index',        'active' => request()->routeIs('suppliers.*'),        'label' => __('file.suppliers')])
                    @endcan
                    @can('categories.index')
                        @include('partials.sidebar-sub-item', ['route' => 'categories.index',       'active' => request()->routeIs('categories.*'),       'label' => __('file.categories')])
                    @endcan
                    @can('unit-of-measures.index')
                        @include('partials.sidebar-sub-item', ['route' => 'unit-of-measures.index', 'active' => request()->routeIs('unit-of-measures.*'), 'label' => __('file.unit_of_measures')])
                    @endcan
                    @can('purchases.index')
                        @include('partials.sidebar-sub-item', ['route' => 'purchases.index',        'active' => request()->routeIs('purchases.*'),        'label' => __('file.purchases')])
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- HR (Employees, Attendance, Leave) --}}
            @canany(['employees.index', 'attendance.index', 'leave-requests.index', 'leave-types.index', 'leave-entitlements.index'])
            <div x-data="{ group: 'hr' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'hr',
                    'label' => __('file.hr_management'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                ])
                <div x-show="activeGroup === 'hr'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('employees.index')
                        @include('partials.sidebar-sub-item', ['route' => 'employees.index',          'active' => request()->routeIs('employees.*'),          'label' => __('file.employees')])
                    @endcan
                    @can('attendance.index')
                        @include('partials.sidebar-sub-item', ['route' => 'attendances.index',        'active' => request()->routeIs('attendances.*'),        'label' => __('file.attendance')])
                    @endcan
                    @can('leave-requests.index')
                        @include('partials.sidebar-sub-item', ['route' => 'leave-requests.index',     'active' => request()->routeIs('leave-requests.*'),     'label' => __('file.leave_requests')])
                    @endcan
                    @can('leave-types.index')
                        @include('partials.sidebar-sub-item', ['route' => 'leave-types.index',        'active' => request()->routeIs('leave-types.*'),        'label' => __('file.leave_types')])
                    @endcan
                    @include('partials.sidebar-sub-item', ['route' => 'holidays.index',           'active' => request()->routeIs('holidays.*'),           'label' => __('file.holidays') ?? 'Holidays'])
                </div>
            </div>
            @endcanany

            {{-- Reports --}}
            @can('reports.index')
            <div x-data="{ group: 'reports' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'reports',
                    'label' => __('file.reports'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                ])
                <div x-show="activeGroup === 'reports'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @include('partials.sidebar-sub-item', ['route' => 'reports.appointments', 'active' => request()->routeIs('reports.appointments*'), 'label' => __('file.appointment_reports')])
                    @include('partials.sidebar-sub-item', ['route' => 'reports.financial',    'active' => request()->routeIs('reports.financial*'),    'label' => __('file.financial_reports')])
                    @include('partials.sidebar-sub-item', ['route' => 'reports.inventory',    'active' => request()->routeIs('reports.inventory*'),    'label' => __('file.inventory_reports')])
                </div>
            </div>
            @endcan

            {{-- Users, Roles & Settings --}}
            @canany(['users.index', 'roles.index', 'settings.index', 'dropdowns.index'])
            <div x-data="{ group: 'admin' }">
                @include('partials.sidebar-group-btn', [
                    'name'  => 'admin',
                    'label' => __('file.administration'),
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                ])
                <div x-show="activeGroup === 'admin'" x-transition x-cloak class="ml-8 space-y-1 mt-1">
                    @can('users.index')
                        @include('partials.sidebar-sub-item', ['route' => 'users.index',      'active' => request()->routeIs('users.*'),      'label' => __('file.user_management')])
                    @endcan
                    @can('roles.index')
                        @include('partials.sidebar-sub-item', ['route' => 'roles.index',      'active' => request()->routeIs('roles.*'),      'label' => __('file.roles_management')])
                    @endcan
                    @can('settings.index')
                        @include('partials.sidebar-sub-item', ['route' => 'settings.general', 'active' => request()->routeIs('settings.*'),   'label' => __('file.general_settings')])
                    @endcan
                    @can('dropdowns.index')
                        @include('partials.sidebar-sub-item', ['route' => 'dropdowns.index',  'active' => request()->routeIs('dropdowns.*'),  'label' => __('file.dropdowns')])
                    @endcan
                    @can('settings.index')
                        @include('partials.sidebar-sub-item', ['route' => 'admin.notification-settings.index', 'active' => request()->routeIs('admin.notification-settings.*'), 'label' => __('file.notification_settings')])
                    @endcan
                </div>
            </div>
            @endcanany

        </div>{{-- end main nav --}}
        @endif

        {{-- ── LOGOUT ──────────────────────────────────────────────────────────── --}}
        <div class="pt-4 mt-auto border-t dark:border-gray-700 p-4">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="group flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300">
                <svg class="h-5 w-5 flex-shrink-0 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="ml-3 sidebar-text">{{ __('file.logout') }}</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>

        @else
            <a href="{{ route('login') }}"
               class="group flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 hover:text-green-600 dark:hover:text-green-400">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16v-4m0 0V8m0 4h4m-4 0H7"/>
                </svg>
                <span class="ml-3 sidebar-text">{{ __('file.log_in') }}</span>
            </a>
        @endauth
    </nav>
</aside>
