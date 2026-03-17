<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if(View::hasSection('title'))@yield('title') | {{ $clinic_name }}@else{{ $clinic_name }}@endif</title>

    <link rel="icon" href="{{ $clinic_logo ?? asset('images/default-logo.png') }}" type="image/png">


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
        :root {
            --primary-color:
                {{ $primary_color }}
            ;
            --primary-color-hover:
                {{ $primary_color }}
                15;
            --primary-color-active:
                {{ $primary_color }}
            ;
            --primary-shadow: 0 4px 15px
                {{ $primary_color }}
                40;
        }

        .sidebar-item-active {
            background-color: var(--primary-color-active) !important;
            color: #ffffff !important;
            box-shadow: var(--primary-shadow) !important;
        }

        .sidebar-item-hover:hover {
            background-color: var(--primary-color-hover) !important;
            color: var(--primary-color) !important;
        }

        .sidebar-sub-active {
            background-color: var(--primary-color-hover) !important;
            color: var(--primary-color) !important;
        }

        [data-tooltip] {
            position: relative;
        }

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

        .sidebar-collapsed [x-show] {
            display: none !important;
        }

        .sidebar-collapsed button[@click*="open"] .transition-transform {
            display: none;
        }

        .sidebar-collapsed button[@click*="open"] {
            pointer-events: none;
        }

        .sidebar-collapsed a {
            pointer-events: auto;
        }

        #notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            pointer-events: none;
            width: auto;
            max-width: 420px;
        }

        #notification-container::-webkit-scrollbar {
            width: 6px;
        }

        #notification-container::-webkit-scrollbar-track {
            background: transparent;
        }

        #notification-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .notification {
            pointer-events: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            min-width: 320px;
            max-width: 100%;
            position: relative;
        }

        .dark .notification {
            background: #1f2937;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .notification.removing {
            animation: slideOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(120%) scale(0.8);
                opacity: 0;
            }

            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            to {
                transform: translateX(120%) scale(0.8);
                opacity: 0;
            }
        }

        .notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            min-width: 60px;
        }

        .notification-icon svg {
            width: 28px;
            height: 28px;
        }

        .notification-content {
            flex: 1;
            padding: 16px 12px 16px 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .notification-title {
            font-weight: 600;
            font-size: 15px;
            line-height: 1.4;
        }

        .notification-message {
            font-size: 14px;
            line-height: 1.5;
            opacity: 0.85;
        }

        .notification-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.05);
            border: none;
            border-radius: 6px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0;
        }

        .dark .notification-close {
            background: rgba(255, 255, 255, 0.1);
        }

        .notification-close:hover {
            background: rgba(0, 0, 0, 0.1);
            transform: scale(1.1);
        }

        .dark .notification-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .notification-close svg {
            width: 14px;
            height: 14px;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            transform-origin: left;
            animation: progress var(--duration, 5s) linear forwards;
        }

        @keyframes progress {
            from {
                transform: scaleX(1);
            }

            to {
                transform: scaleX(0);
            }
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.success .notification-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .notification.success .notification-title {
            color: #059669;
        }

        .dark .notification.success .notification-title {
            color: #10b981;
        }

        .notification.success .notification-message {
            color: #064e3b;
        }

        .dark .notification.success .notification-message {
            color: #d1fae5;
        }

        .notification.success .notification-progress {
            color: #10b981;
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.error .notification-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .notification.error .notification-title {
            color: #dc2626;
        }

        .dark .notification.error .notification-title {
            color: #ef4444;
        }

        .notification.error .notification-message {
            color: #7f1d1d;
        }

        .dark .notification.error .notification-message {
            color: #fecaca;
        }

        .notification.error .notification-progress {
            color: #ef4444;
        }

        .notification.warning {
            border-left: 4px solid #f59e0b;
        }

        .notification.warning .notification-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .notification.warning .notification-title {
            color: #d97706;
        }

        .dark .notification.warning .notification-title {
            color: #f59e0b;
        }

        .notification.warning .notification-message {
            color: #78350f;
        }

        .dark .notification.warning .notification-message {
            color: #fde68a;
        }

        .notification.warning .notification-progress {
            color: #f59e0b;
        }

        .notification.info {
            border-left: 4px solid #3b82f6;
        }

        .notification.info .notification-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .notification.info .notification-title {
            color: #2563eb;
        }

        .dark .notification.info .notification-title {
            color: #3b82f6;
        }

        .notification.info .notification-message {
            color: #1e3a8a;
        }

        .dark .notification.info .notification-message {
            color: #dbeafe;
        }

        .notification.info .notification-progress {
            color: #3b82f6;
        }

        @media (max-width: 640px) {
            #notification-container {
                top: 70px;
                right: 10px;
                left: 10px;
                max-width: none;
                gap: 10px;
                max-height: calc(100vh - 80px);
            }

            .notification {
                min-width: 0;
                border-radius: 10px;
            }

            .notification-icon {
                padding: 16px;
                min-width: 50px;
            }

            .notification-icon svg {
                width: 24px;
                height: 24px;
            }

            .notification-content {
                padding: 14px 10px 14px 0;
            }

            .notification-title {
                font-size: 14px;
            }

            .notification-message {
                font-size: 13px;
            }
        }

        /* Global Responsive DataTable Card Layout */
        @media (max-width: 767px) {
            .overflow-x-auto {
                overflow-x: visible !important;
            }

            #docapp-table {
                border: 0 !important;
                width: 100% !important;
                table-layout: fixed !important;
                display: block !important;
            }

            #docapp-table thead {
                display: none !important;
            }

            #docapp-table tbody {
                display: block !important;
                width: 100% !important;
            }

            #docapp-table tr {
                display: block !important;
                margin-bottom: 1.5rem !important;
                background: white !important;
                border: 1px solid #e5e7eb !important;
                border-radius: 0.75rem !important;
                padding: 0.5rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
                width: 100% !important;
            }

            .dark #docapp-table tr {
                background: #1f2937 !important;
                border-color: #374151 !important;
            }

            #docapp-table td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.75rem 1rem !important;
                border-bottom: 1px solid #f3f4f6 !important;
                text-align: right !important;
                font-size: 0.875rem !important;
                white-space: normal !important;
                word-break: break-all !important;
                min-height: 3rem !important;
            }

            .dark #docapp-table td {
                border-bottom-color: #374151 !important;
            }

            #docapp-table td:last-child {
                border-bottom: 0 !important;
                justify-content: center !important;
                background: #f9fafb !important;
                margin-top: 0.75rem !important;
                border-radius: 0.5rem !important;
                padding: 1rem !important;
            }

            .dark #docapp-table td:last-child {
                background: #111827 !important;
            }

            #docapp-table td:before {
                content: attr(data-label);
                font-weight: 700 !important;
                text-transform: uppercase !important;
                font-size: 0.7rem !important;
                color: #4b5563 !important;
                padding-right: 1.5rem !important;
                text-align: left !important;
                flex: 0 0 40% !important;
                max-width: 40% !important;
                letter-spacing: 0.025em !important;
            }

            .dark #docapp-table td:before {
                color: #9ca3af !important;
            }

            /* Handle checkboxes/first column */
            #docapp-table td:first-child {
                justify-content: flex-start !important;
                background: #fefce8 !important;
                border-top-left-radius: 0.5rem !important;
                border-top-right-radius: 0.5rem !important;
                border-bottom: 1px solid #fde047 !important;
            }

            .dark #docapp-table td:first-child {
                background: rgba(254, 252, 232, 0.05) !important;
                border-bottom-color: #422006 !important;
            }

            #docapp-table td:first-child:before {
                flex: 0 0 auto !important;
                margin-right: 1rem !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <div id="notification-container"></div>

    @include('partials.sidebar')

    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <div id="main-content" class="transition-all duration-300 lg:ml-64" style="margin-left: 0;">

        <nav id="navbar"
            class="fixed top-0 h-16 bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 z-40 flex items-center justify-between px-6 transition-all duration-300 left-0 right-0 lg:left-64">
            <button id="mobile-menu-btn"
                class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex items-center space-x-4"></div>

            <div class="flex items-center space-x-4">
                {{-- Appointment Manager Link --}}
                <a href="{{ route('appointments.manager') }}" title="{{ __('Appointment Manager') }}"
                    class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </a>


                @can('invoices.index')
                    {{-- POS Link --}}
                    <a href="{{ route('invoices.pos') }}" title="{{ __('file.pos') ?? 'POS' }}" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                                   dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                                   dark:focus:ring-offset-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                @endcan

                <div x-data="{ open: false }" x-init="open = false" class="relative">
                    <button @click="open = !open"
                        class="flex items-center space-x-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
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
                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                {{ __('English') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('language.switch') }}">
                            @csrf
                            <input type="hidden" name="locale" value="es">
                            <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                {{ __('Español') }}
                            </button>
                        </form>
                    </div>
                </div>

                <button id="theme-toggle"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <button id="fullscreen-toggle" aria-label="Toggle fullscreen"
                    class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                    <svg id="enter-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 3H5a2 2 0 00-2 2v3M16 3h3a2 2 0 012 2v3M8 21H5a2 2 0 01-2-2v-3M16 21h3a2 2 0 002-2v-3" />
                    </svg>
                    <svg id="exit-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9H5V5M15 9h4V5M9 15H5v4M15 15h4v4" />
                    </svg>
                </button>

                <div class="relative" x-data="notificationsDropdown" x-init="init" x-cloak>

                    <button @click="open = !open; if (open) fetchNotifications()"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative focus:outline-none focus:ring-2 focus:ring-primary"
                        aria-label="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <!-- Badge – show only if unread > 0 -->
                        <template x-if="unreadCount > 0">
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                                x-text="unreadCount > 99 ? '99+' : unreadCount">
                            </span>
                        </template>
                    </button>

                    <!-- Dropdown panel -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="fixed sm:absolute top-16 sm:top-auto sm:mt-3 left-1/2 sm:left-auto right-auto sm:right-0 -translate-x-1/2 sm:translate-x-0 w-[calc(100vw-2rem)] sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-50 max-h-[80vh] overflow-y-auto">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        </div>

                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-if="notifications.length === 0">
                                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-2 text-sm">No new notifications</p>
                                </div>
                            </template>

                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read_at }">
                                    <div class="flex items-start space-x-4">
                                        <!-- Icon / Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                x-text="notification.data.message || 'New notification'"></p>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                                                x-text="notification.data.patient_name || ''"></p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                                x-text="new Date(notification.created_at).toLocaleString()"></p>
                                        </div>

                                        <!-- Action -->
                                        <button @click="markAsRead(notification.id)"
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap"
                                            x-show="!notification.read_at">
                                            Mark read
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                <div class="relative" x-data="{ open: false }" x-cloak x-init="open = false">
                    <button id="user-menu-button" @click="open = !open"
                        class="flex items-center space-x-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                        <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-300 dark:ring-gray-600"
                            src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name) . '&background=6366f1&color=fff' }}"
                            alt="{{ auth()->user()?->name }}">
                        <span class="hidden sm:block">{{ auth()->user()?->name }}</span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">

                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-20 p-4 sm:p-6 min-h-screen bg-gray-50 dark:bg-gray-900/50">
            @yield('content')
        </main>
    </div>

    <script>
        // Global DataTables Default Configuration
        window.addEventListener('DOMContentLoaded', function () {
            if (typeof $.fn.dataTable !== 'undefined') {
                $.extend(true, $.fn.dataTable.defaults, {
                    responsive: false,
                    drawCallback: function (settings) {
                        const $table = $(this);
                        if ($table.attr('id') === 'docapp-table') {
                            const labels = [];
                            $table.find('thead th').each(function () {
                                // Extract text, ignoring empty tags or icon-only tags
                                let text = $(this).text().trim();
                                // Fallback if no text (e.g. checkbox header or icon header)
                                if (!text && $(this).find('input[type="checkbox"]').length) text = "{{ __('file.select') }}";
                                if (!text && $(this).hasClass('no-export')) text = "{{ __('file.actions') }}";
                                labels.push(text);
                            });

                            $table.find('tbody tr').each(function () {
                                $(this).find('td').each(function (index) {
                                    if (labels[index]) {
                                        $(this).attr('data-label', labels[index]);
                                    }
                                });
                            });
                        }
                    }
                });
            } else {
                // Fallback if DT loads after this script
                $(document).on('init.dt', function (e, settings) {
                    const api = new $.fn.dataTable.Api(settings);
                    if ($(api.table().node()).attr('id') === 'docapp-table') {
                        settings.oInit.responsive = false;
                        api.settings()[0].oInit.responsive = false;
                    }
                });
            }
        });
    </script>

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

        mobileMenuBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        });

        mobileOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });

        function isDesktop() {
            return window.innerWidth >= 1024;
        }

        function closeAllDropdowns() {
            sidebar.querySelectorAll('[x-data]').forEach(el => {
                if (el._x_dataStack && el._x_dataStack[0]) {
                    if (typeof el._x_dataStack[0].open !== 'undefined') {
                        el._x_dataStack[0].open = false;
                    }
                }
                if (window.Alpine && el.__x) {
                    Alpine.evaluate(el, 'open = false');
                }
            });
            setTimeout(() => {
                sidebar.querySelectorAll('[x-show]').forEach(dropdown => {
                    if (dropdown.style) {
                        dropdown.style.display = 'none';
                    }
                });
            }, 50);
        }

        toggleBtn?.addEventListener('click', () => {
            if (!isDesktop()) {
                sidebar.classList.add('-translate-x-full');
                mobileOverlay.classList.add('hidden');
                return;
            }
            const collapsed = sidebar.classList.toggle('sidebar-collapsed');
            const iconExpanded = document.getElementById('icon-expanded');
            const iconCollapsed = document.getElementById('icon-collapsed');

            if (collapsed) {
                closeAllDropdowns();
                sidebar.style.width = '74px';
                mainContent.style.marginLeft = '74px';
                navbar.style.left = '74px';
                iconExpanded?.classList.replace('opacity-100', 'opacity-0');
                iconCollapsed?.classList.replace('opacity-0', 'opacity-100');
            } else {
                sidebar.style.width = '16rem';
                mainContent.style.marginLeft = '16rem';
                navbar.style.left = '16rem';
                iconExpanded?.classList.replace('opacity-0', 'opacity-100');
                iconCollapsed?.classList.replace('opacity-100', 'opacity-0');
            }
            localStorage.setItem('sidebarCollapsed', collapsed);
        });

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
            }, true);
        });

        window.addEventListener('resize', () => {
            if (isDesktop()) {
                mobileOverlay.classList.add('hidden');
                sidebar.classList.remove('-translate-x-full');
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
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.style.width = '16rem';
                mainContent.style.marginLeft = '0';
                navbar.style.left = '0';
                sidebar.classList.add('-translate-x-full');
            }
        });

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
            const promise = elem.requestFullscreen?.() || elem.webkitRequestFullscreen?.() || elem.msRequestFullscreen?.();
            if (promise) {
                promise.then(() => {
                    localStorage.setItem('autoFullscreen', 'true');
                    localStorage.setItem('lastFullscreenTime', Date.now().toString());
                }).catch(() => { });
            }
        }

        function exitFullscreen() {
            const promise = document.exitFullscreen?.() || document.webkitExitFullscreen?.() || document.msExitFullscreen?.();
            if (promise) promise.catch(() => { });
            localStorage.removeItem('autoFullscreen');
            localStorage.removeItem('lastFullscreenTime');
        }

        function updateFullscreenIcon() {
            const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
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
            document.addEventListener(event, updateFullscreenIcon);
        });

        document.addEventListener('DOMContentLoaded', updateFullscreenIcon);

        function showNotification(title, message, type = 'info', duration = 5000) {
            const container = document.getElementById('notification-container');
            if (!container) return;

            const icons = {
                success: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                error: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                warning: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                info: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            };

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.setProperty('--duration', `${duration}ms`);
            notification.innerHTML = `
            <div class="notification-icon">${icons[type] || icons.info}</div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="removeNotification(this)">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            ${duration > 0 ? '<div class="notification-progress"></div>' : ''}
        `;
            container.appendChild(notification);
            if (duration > 0) {
                setTimeout(() => {
                    if (notification && notification.parentNode) {
                        removeNotification(notification.querySelector('.notification-close'));
                    }
                }, duration);
            }
        }

        function removeNotification(button) {
            const notification = button.closest('.notification');
            if (notification) {
                notification.classList.add('removing');
                setTimeout(() => notification.remove(), 300);
            }
        }

        window.showNotification = showNotification;
        window.removeNotification = removeNotification;

        document.addEventListener('DOMContentLoaded', () => {
            if (performance.navigation.type === 2) return;

            @if(session('success'))
                showNotification('{{ __("Success") }}', "{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showNotification('{{ __("Error") }}', "{{ session('error') }}", 'error');
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    showNotification('{{ __("Error") }}', "{{ addslashes($error) }}", 'error');
                @endforeach
            @endif
    });

        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsDropdown', () => ({
                open: false,
                notifications: [],
                unreadCount: 0,

                url: '{{ route("notifications.unread") }}',

                async fetchNotifications() {
                    try {
                        const response = await fetch(this.url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) throw new Error('Failed to fetch notifications');

                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                    } catch (error) {
                        console.error('Notifications fetch error:', error);
                    }
                },

                async markAsRead(id) {
                    try {
                        const readRoutePattern = '{{ route("notifications.read", ":id") }}';
                        const url = readRoutePattern.replace(':id', id);

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            let message = 'Failed to mark as read';
                            try {
                                const json = await response.json();
                                message = json.message || message;
                            } catch { }
                            throw new Error(`${message} (${response.status})`);
                        }

                        const notifIndex = this.notifications.findIndex(n => n.id === id);
                        if (notifIndex !== -1) {
                            this.notifications[notifIndex].read_at = new Date().toISOString();
                            this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                        }

                    } catch (err) {
                        console.error('Mark as read failed:', err);
                        showNotification('Error', 'Could not mark notification as read. Please try again.', 'error');
                    }
                },

                init() {
                    this.fetchNotifications();
                    setInterval(() => this.fetchNotifications(), 60000);
                }
            }));
        });
    </script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>