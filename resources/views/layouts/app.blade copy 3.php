<!DOCTYPE html>
<html lang="en" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>

    <style>
        [data-tooltip] { position: relative; }
        [data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 12px;
            background: #1f2937;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 50;
        }
        .sidebar-collapsed [data-tooltip]:hover::after {
            opacity: 1;
        }
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        .sidebar-collapsed .flex.items-center.space-x-3 {
            justify-content: center;
        }
        .sidebar-collapsed .h-16.flex.items-center.justify-between {
            justify-content: center;
        }
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        html {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #sidebar {
            max-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #sidebar nav {
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Add to your existing <style> section */
            .sidebar-collapsed [x-show] {
                display: none !important;
            }

            .sidebar-collapsed button[@click*="open"] .transition-transform {
                display: none;
            }

            /* Prevent pointer events on dropdown buttons when collapsed */
            .sidebar-collapsed button[@click*="open"] {
                pointer-events: none;
            }

            /* Re-enable pointer events on main navigation items */
            .sidebar-collapsed a {
                pointer-events: auto;
            }
    </style>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

@include('partials.sidebar')

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

<!-- Main Content Area -->
<div id="main-content" class="transition-all duration-300 lg:ml-64" style="margin-left: 0;">
    
    <!-- Navbar (Fixed) -->
    <nav id="navbar" class="fixed top-0 h-16 bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 z-40 flex items-center justify-between px-6 transition-all duration-300 left-0 right-0 lg:left-64">
        
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        
        <div class="flex items-center space-x-4">
        </div>

        <div class="flex items-center space-x-4">
            <div x-data="{ open: false }" x-init="open = false" class="relative">
                <button @click="open = !open"
                        class="flex items-center space-x-1 text-sm font-medium text-gray-700 dark:text-gray-300 
                               hover:text-primary dark:hover:text-primary-400 
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-36 origin-top-right bg-white dark:bg-gray-800 
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('English') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="es">
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Español') }}
                        </button>
                    </form>
                </div>
            </div>
            <!-- Theme Toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <button id="fullscreen-toggle"
                aria-label="Toggle fullscreen"
                class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 
                    dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 
                    focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                    dark:focus:ring-offset-gray-800 transition">
                <svg id="enter-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                </svg>
                <svg id="exit-fullscreen-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-8v4m0-4h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0-4h-4m4 4l-5-5"/>
                </svg>
            </button>

            <!-- Notifications -->
            <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Profile -->
            <div class="relative" x-data="{ open: false }" x-cloak x-init="open = false">
                <button id="user-menu-button"
                        @click="open = !open"
                        class="flex items-center space-x-3 text-sm font-medium text-gray-700 dark:text-gray-300 
                               hover:text-primary dark:hover:text-primary-400 
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-300 dark:ring-gray-600"
                         src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name) . '&background=6366f1&color=fff' }}"
                         alt="{{ auth()->user()?->name }}">
                    <span class="hidden sm:block">{{ auth()->user()?->name }}</span>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 origin-top-right bg-white dark:bg-gray-800 
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 
                              hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content (Scrollable) -->
    <main class="pt-20 p-4 sm:p-6 min-h-screen bg-gray-50 dark:bg-gray-900/50">
        @yield('content')
    </main>
</div>

@stack('scripts')

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const navbar = document.getElementById('navbar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileOverlay = document.getElementById('mobile-overlay');
    
    const fullscreenToggle = document.getElementById('fullscreen-toggle');
    const enterIcon = document.getElementById('enter-fullscreen-icon');
    const exitIcon = document.getElementById('exit-fullscreen-icon');

    // Mobile Menu Toggle
    mobileMenuBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        mobileOverlay.classList.toggle('hidden');
    });

    mobileOverlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
    });

    // Check if desktop view
    function isDesktop() {
        return window.innerWidth >= 1024;
    }

    // Function to close all Alpine dropdowns
    function closeAllDropdowns() {
        sidebar.querySelectorAll('[x-data]').forEach(el => {
            // Method 1: Using Alpine's internal structure
            if (el._x_dataStack && el._x_dataStack[0]) {
                if (typeof el._x_dataStack[0].open !== 'undefined') {
                    el._x_dataStack[0].open = false;
                }
            }
            
            // Method 2: Trigger Alpine's reactivity
            if (window.Alpine && el.__x) {
                Alpine.evaluate(el, 'open = false');
            }
        });
        
        // Force hide all dropdowns
        setTimeout(() => {
            sidebar.querySelectorAll('[x-show]').forEach(dropdown => {
                if (dropdown.style) {
                    dropdown.style.display = 'none';
                }
            });
        }, 50);
    }

    // Sidebar Toggle for Desktop
    toggleBtn?.addEventListener('click', () => {
    if (!isDesktop()) {
        sidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
        return;
    }

    // Toggle the collapsed class
    const collapsed = sidebar.classList.toggle('sidebar-collapsed');

    // Get the two icons
    const iconExpanded = document.getElementById('icon-expanded');   // double left arrows (visible when expanded)
    const iconCollapsed = document.getElementById('icon-collapsed'); // double right arrows (visible when collapsed)

    if (collapsed) {
        // Sidebar is now collapsed
        closeAllDropdowns();
        sidebar.style.width = '74px';
        mainContent.style.marginLeft = '74px';
        navbar.style.left = '74px';

        // Swap icons: hide expanded (left), show collapsed (right)
        iconExpanded.classList.replace('opacity-100', 'opacity-0');
        iconCollapsed.classList.replace('opacity-0', 'opacity-100');
    } else {
        // Sidebar is now expanded
        sidebar.style.width = '16rem';
        mainContent.style.marginLeft = '16rem';
        navbar.style.left = '16rem';

        // Swap icons: show expanded (left), hide collapsed (right)
        iconExpanded.classList.replace('opacity-0', 'opacity-100');
        iconCollapsed.classList.replace('opacity-100', 'opacity-0');
    }

    // Save the state
    localStorage.setItem('sidebarCollapsed', collapsed);
});

    // Prevent dropdowns from opening when sidebar is collapsed
    document.addEventListener('DOMContentLoaded', () => {
        sidebar?.addEventListener('click', (e) => {
            const collapsed = sidebar.classList.contains('sidebar-collapsed');
            if (collapsed) {
                const button = e.target.closest('button[\\@click]');
                if (button && button.getAttribute('@click')?.includes('open')) {
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                }
            }
        }, true); // Use capture phase
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (isDesktop()) {
            // Reset mobile overlay when switching to desktop
            mobileOverlay.classList.add('hidden');
            sidebar.classList.remove('-translate-x-full');
            
            // Apply saved collapse state
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (collapsed) {
                sidebar.classList.add('sidebar-collapsed');
                closeAllDropdowns();
                sidebar.style.width = '74px';
                mainContent.style.marginLeft = '74px';
                navbar.style.left = '74px';
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.style.width = '16rem';
                mainContent.style.marginLeft = '16rem';
                navbar.style.left = '16rem';
            }
        } else {
            // Reset to mobile view
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.style.width = '16rem';
            mainContent.style.marginLeft = '0';
            navbar.style.left = '0';
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Load saved sidebar state on desktop only
    if (isDesktop() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('sidebar-collapsed');
        closeAllDropdowns();
        sidebar.style.width = '74px';
        mainContent.style.marginLeft = '74px';
        navbar.style.left = '74px';
    } else if (isDesktop()) {
        mainContent.style.marginLeft = '16rem';
        navbar.style.left = '16rem';
    }

    // Theme Toggle
    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');

    function applyTheme(theme) {
        if (theme === 'dark') {
            html.classList.add('dark');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        } else {
            html.classList.remove('dark');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        }
    }

    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));

    themeToggle?.addEventListener('click', () => {
        const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });

    function enterFullscreen() {
        const elem = document.documentElement;
        const promise = elem.requestFullscreen?.() ||
                        elem.webkitRequestFullscreen?.() ||
                        elem.msRequestFullscreen?.();
        if (promise) {
            promise
                .then(() => {
                    localStorage.setItem('autoFullscreen', 'true');
                    localStorage.setItem('lastFullscreenTime', Date.now().toString());
                })
                .catch(() => {});
        }
    }

    function exitFullscreen() {
        const promise = document.exitFullscreen?.() ||
                        document.webkitExitFullscreen?.() ||
                        document.msExitFullscreen?.();
        if (promise) promise.catch(() => {});
        localStorage.removeItem('autoFullscreen');
        localStorage.removeItem('lastFullscreenTime');
    }

    function updateFullscreenIcon() {
        const isFullscreen = !!(
            document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement
        );
        enterIcon?.classList.toggle('hidden', isFullscreen);
        exitIcon?.classList.toggle('hidden', !isFullscreen);
    }

    fullscreenToggle?.addEventListener('click', () => {
        if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
            exitFullscreen();
        } else {
            enterFullscreen();
        }
    });

    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(event => {
        document.addEventListener(event, () => {
            updateFullscreenIcon();
            if (document.fullscreenElement) {
                localStorage.setItem('lastFullscreenTime', Date.now().toString());
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateFullscreenIcon();
        if (localStorage.getItem('autoFullscreen') === 'true') {
            const lastTime = localStorage.getItem('lastFullscreenTime');
            const now = Date.now();
            if (lastTime && (now - parseInt(lastTime)) < 5000) {
                setTimeout(enterFullscreen, 50);
            }
        }
    });
</script>

</body>
</html>