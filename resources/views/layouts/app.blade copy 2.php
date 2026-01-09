<!DOCTYPE html>
<html lang="en" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
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
    </style>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen bg-white dark:bg-gray-800 shadow-lg border-r dark:border-gray-700 transition-all duration-300 z-50 overflow-y-auto overflow-x-hidden flex flex-col lg:translate-x-0 -translate-x-full" style="width: 16rem;">
    
    <!-- Logo Section with Collapse Button -->
    <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0">
                L
            </div>
            <span class="sidebar-text font-semibold text-lg truncate">Logo</span>
        </div>
        <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex-shrink-0">
            <svg id="collapse-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 space-y-2 flex-1">
        <!-- Dashboard -->
        <a href="#" data-tooltip="Dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <!-- Users -->
        <a href="#" data-tooltip="Users" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="sidebar-text">Users</span>
        </a>

        <!-- Products -->
        <a href="#" data-tooltip="Products" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="sidebar-text">Products</span>
        </a>

        <!-- Orders -->
        <a href="#" data-tooltip="Orders" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span class="sidebar-text">Orders</span>
        </a>

        <!-- Analytics -->
        <a href="#" data-tooltip="Analytics" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="sidebar-text">Analytics</span>
        </a>

        <!-- Settings -->
        <a href="#" data-tooltip="Settings" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="sidebar-text">Settings</span>
        </a>

        <!-- Messages -->
        <a href="#" data-tooltip="Messages" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <span class="sidebar-text">Messages</span>
        </a>

        <!-- Reports -->
        <a href="#" data-tooltip="Reports" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="sidebar-text">Reports</span>
        </a>
    </nav>

</aside>

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
            <h1 class="text-xl font-semibold">Dashboard</h1>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
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
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full"></div>
                <span class="hidden sm:block font-medium">John Doe</span>
            </div>
        </div>
    </nav>

    z
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const navbar = document.getElementById('navbar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const collapseIcon = document.getElementById('collapse-icon');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileOverlay = document.getElementById('mobile-overlay');

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

    // Sidebar Toggle for Desktop
    toggleBtn?.addEventListener('click', () => {
        if (!isDesktop()) {
            // On mobile, just close the sidebar
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            return;
        }

        sidebar.classList.toggle('sidebar-collapsed');
        const collapsed = sidebar.classList.contains('sidebar-collapsed');
        
        if (collapsed) {
            sidebar.style.width = '64px';
            mainContent.style.marginLeft = '64px';
            navbar.style.left = '64px';
            collapseIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
        } else {
            sidebar.style.width = '16rem';
            mainContent.style.marginLeft = '16rem';
            navbar.style.left = '16rem';
            collapseIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
        }
        
        localStorage.setItem('sidebarCollapsed', collapsed);
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
                sidebar.style.width = '64px';
                mainContent.style.marginLeft = '64px';
                navbar.style.left = '64px';
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
        sidebar.style.width = '64px';
        mainContent.style.marginLeft = '64px';
        navbar.style.left = '64px';
        collapseIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
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
</script>

</body>
</html>