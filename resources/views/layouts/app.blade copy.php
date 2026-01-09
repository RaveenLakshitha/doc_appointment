<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        html { transition: background-color 0.3s ease, color 0.3s ease; }
        [data-tooltip] { position: relative; }
        [data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute; left: 100%; top: 50%; transform: translateY(-50%);
            margin-left: 8px; background:#1f2937; color:white;
            padding:4px 8px; border-radius:4px; font-size:12px;
            white-space:nowrap; opacity:0; pointer-events:none;
            transition:opacity .2s; z-index:10;
        }
        .sidebar-collapsed [data-tooltip]:hover::after { opacity:1; }
        .sidebar-collapsed .sidebar-text { display:none; }
        nav::-webkit-scrollbar { width:0; }
        nav { -ms-overflow-style:none; scrollbar-width:none; }
    </style>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
<div class="flex min-h-screen">

    @include('partials.sidebar')

    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

    <div class="flex-1 flex flex-col">

        @include('partials.navbar')

        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1 p-4 lg:p-2 bg-gray-50 dark:bg-gray-900/50">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const collapseIcon = document.getElementById('collapse-icon');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const fullscreenToggle = document.getElementById('fullscreen-toggle');
    const enterIcon = document.getElementById('enter-fullscreen-icon');
    const exitIcon = document.getElementById('exit-fullscreen-icon');

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

    toggleBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-collapsed');
        const collapsed = sidebar.classList.contains('sidebar-collapsed');
        sidebar.style.width = collapsed ? '64px' : '16rem';
        // collapseIcon.innerHTML = collapsed
        //     ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>'
        //     : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
        localStorage.setItem('sidebarCollapsed', collapsed);
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('sidebar-collapsed');
            sidebar.style.width = '64px';
           // collapseIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
        }
    });

    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle-navbar');
    const sunIcon = document.getElementById('sun-icon-navbar');
    const moonIcon = document.getElementById('moon-icon-navbar');

    function applyTheme(theme) {
        if (theme === 'dark') {
            html.classList.add('dark');
            sunIcon?.classList.remove('hidden');
            moonIcon?.classList.add('hidden');
        } else {
            html.classList.remove('dark');
            sunIcon?.classList.add('hidden');
            moonIcon?.classList.remove('hidden');
        }
        localStorage.setItem('theme', theme);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(saved || (prefersDark ? 'dark' : 'light'));
    });

    themeToggle?.addEventListener('click', () => {
        applyTheme(html.classList.contains('dark') ? 'light' : 'dark');
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