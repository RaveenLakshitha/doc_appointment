{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { primary: '#1d4ed8' } } }
        }
    </script>
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors">
    <div class="flex h-full">

        {{-- ====================== SIDEBAR ====================== --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">
                    {{ config('app.name') }}
                </a>
                <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="mt-6 space-y-1 px-3">

                {{-- ==== DASHBOARD (All logged‑in users) ==== --}}
                @auth
                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <x-icon name="home"/> Dashboard
                    </x-sidebar-link>
                @endauth

                {{-- ==== THERAPIST ==== --}}
                @role('therapist')
                    <x-sidebar-link href="{{ route('therapist.appointments.index') }}">
                        <x-icon name="calendar"/> My Appointments
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('therapist.charges.index') }}">
                        <x-icon name="dollar"/> Charges
                    </x-sidebar-link>
                @endrole

                {{-- ==== PRIMARY THERAPIST ==== --}}
                @role('primary-therapist')
                    <x-sidebar-link href="{{ route('primary.patients.index') }}">
                        <x-icon name="users"/> Assign Patients
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('primary.appointments.index') }}">
                        <x-icon name="calendar-check"/> Manage Appointments
                    </x-sidebar-link>
                @endrole

                {{-- ==== ADMIN ==== --}}
                @role('admin')
                    <x-sidebar-link href="{{ route('admin.appointments.create') }}">
                        <x-icon name="plus"/> First Appointment
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.appointments.index') }}">
                        <x-icon name="edit"/> Edit Appointments
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.charges.create') }}">
                        <x-icon name="dollar"/> Charge
                    </x-sidebar-link>
                @endrole

                {{-- ==== COUNTER ==== --}}
                @role('counter')
                    <x-sidebar-link href="{{ route('counter.invoices.index') }}">
                        <x-icon name="file-invoice"/> Issue Invoice
                    </x-sidebar-link>
                @endrole

                {{-- ==== HR ==== --}}
                @role('hr')
                    <x-sidebar-link href="{{ route('hr.dashboard') }}">
                        <x-icon name="chart"/> Real‑Time Ops
                    </x-sidebar-link>
                @endrole

                {{-- ==== PATIENT ==== --}}
                @role('patient')
                    <x-sidebar-link href="{{ route('patient.book') }}">
                        <x-icon name="calendar-plus"/> Book Appointment
                    </x-sidebar-link>
                @endrole

                {{-- ==== USER PROFILE & LOGOUT ==== --}}
                @auth
                    <hr class="my-4 border-gray-300 dark:border-gray-600">
                    <x-sidebar-link href="{{ route('profile.edit') }}">
                        <x-icon name="user"/> Profile
                    </x-sidebar-link>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            <x-icon name="logout"/> Log Out
                        </button>
                    </form>
                @endauth

                {{-- ==== GUEST ==== --}}
                @guest
                    <x-sidebar-link href="{{ route('login') }}">
                        <x-icon name="login"/> Log In
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('register') }}">
                        <x-icon name="register"/> Register
                    </x-sidebar-link>
                @endguest
            </nav>

            {{-- ==== DARK MODE TOGGLE ==== --}}
            <div class="absolute bottom-0 w-full p-4 border-t dark:border-gray-700">
                <button id="theme-toggle"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                    <svg id="sun-icon" class="w-5 h-5 mr-2 hidden dark:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m8.485-11.485l-.707.707M5.636 18.364l-.707.707m12.021 0l-.707-.707M6.343 5.636l-.707-.707m12.728 12.728l-.707-.707M6.343 18.364l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                    <svg id="moon-icon" class="w-5 h-5 mr-2 inline dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <span id="theme-text">Dark Mode</span>
                </button>
            </div>
        </aside>

        {{-- Overlay for mobile --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

        {{-- ====================== MAIN CONTENT ====================== --}}
        <div class="flex-1 flex flex-col">
            {{-- Mobile Header --}}
            <header class="lg:hidden bg-white dark:bg-gray-800 shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <button id="open-sidebar" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="text-lg font-bold text-primary">
                        {{ config('app.name') }}
                    </a>
                    <div class="w-6"></div>
                </div>
            </header>

            {{-- Page Header --}}
            @if(isset($header))
                <header class="bg-white dark:bg-gray-800 shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- JS: Sidebar + Dark Mode --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        openBtn?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });
        closeBtn?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Dark Mode
        const html = document.documentElement;
        const themeToggle = document.getElementById('theme-toggle');
        const sun = document.getElementById('sun-icon');
        const moon = document.getElementById('moon-icon');
        const text = document.getElementById('theme-text');

        function setTheme(mode) {
            if (mode === 'dark') {
                html.classList.add('dark');
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
                text.textContent = 'Light Mode';
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
                text.textContent = 'Dark Mode';
                localStorage.setItem('theme', 'light');
            }
        }

        const saved = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        setTheme(saved || (prefersDark ? 'dark' : 'light'));

        themeToggle?.addEventListener('click', () => {
            setTheme(html.classList.contains('dark') ? 'light' : 'dark');
        });
    </script>
</body>
</html>