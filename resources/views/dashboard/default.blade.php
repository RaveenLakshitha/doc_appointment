@extends('layouts.app')

@section('title', __('file.dashboard') ?? 'Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="px-4 sm:px-6 lg:px-8 pb-10 pt-20">

            {{-- ── PAGE HEADER ────────────────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('file.welcome_back') ?? 'Welcome back' }}, {{ $userName }}!
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ $currentDate }}</p>
                </div>
                {{-- Role + Notification pills --}}
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium"
                        style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                    </span>
                    @if($unreadCount > 0)
                        <span
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            {{ $unreadCount }} {{ __('file.unread') ?? 'Unread' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- ── TAB BAR ─────────────────────────────────────────────────────────── --}}
            <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-6" aria-label="Dashboard Tabs">
                    <button onclick="switchTab('overview')" id="tab-overview"
                        class="tab-btn pb-3 px-1 text-sm font-semibold border-b-2 transition-colors"
                        style="border-color:{{ $primary_color }}; color:{{ $primary_color }}">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('file.overview') ?? 'Overview' }}
                    </button>
                    <button onclick="switchTab('notifications')" id="tab-notifications"
                        class="tab-btn relative pb-3 px-1 text-sm font-semibold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        {{ __('file.notifications') ?? 'Notifications' }}
                        @if($unreadCount > 0)
                            <span
                                class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>

            {{-- ════════════════════════════════════════════════════════════════════════
            OVERVIEW TAB
            ═════════════════════════════════════════════════════════════════════════ --}}
            <div id="content-overview" class="tab-content">

                {{-- ── QUICK ACCESS CARDS (based on permissions) ───────────────────── --}}
                @php
                    $hasAnyCardAccess = auth()->user()->canAny([
                        'patients.index',
                        'appointments.index',
                        'invoices.index',
                        'employees.index',
                        'inventory.index',
                        'reports.index',
                        'users.index',
                        'attendance.index'
                    ]);
                @endphp

                @if($hasAnyCardAccess)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
                        @can('patients.index')
                            <a href="{{ route('patients.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3 transition-all"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.patients') }}</span>
                            </a>
                        @endcan

                        @can('appointments.index')
                            <a href="{{ route('appointments.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.appointments') }}</span>
                            </a>
                        @endcan

                        @can('invoices.index')
                            <a href="{{ route('invoices.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.billing') }}</span>
                            </a>
                        @endcan

                        @can('employees.index')
                            <a href="{{ route('employees.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.employees') }}</span>
                            </a>
                        @endcan

                        @can('inventory.index')
                            <a href="{{ route('inventory.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.inventory') }}</span>
                            </a>
                        @endcan

                        @can('reports.index')
                            <a href="{{ route('reports.appointments') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.reports') }}</span>
                            </a>
                        @endcan

                        @can('users.index')
                            <a href="{{ route('users.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.administration') }}</span>
                            </a>
                        @endcan

                        {{-- Attendance quick link for all with attendance permission --}}
                        @can('attendance.index')
                            <a href="{{ route('attendances.index') }}"
                                class="group flex flex-col items-center p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all text-center">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3"
                                    style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('file.attendance') }}</span>
                            </a>
                        @endcan
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-10 text-center border border-gray-200 dark:border-gray-700 shadow-sm mb-10">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                            style="background:{{ $primary_color }}15; color:{{ $primary_color }}">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('file.welcome_to_dashboard') ?? 'Welcome to your Dashboard' }}
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            {{ __('file.no_dashboard_widgets_desc') ?? 'You currently do not have access to any quick widgets. If you believe this is an error, please contact your administrator.' }}
                        </p>
                    </div>
                @endif

                {{-- ── ATTENDANCE & LEAVE (only if user has an employee record) ──────── --}}
                @if($hasEmployee)
                    @include('dashboard.partials.attendance-leave')
                @endif

            </div>{{-- /content-overview --}}

            {{-- ════════════════════════════════════════════════════════════════════════
            NOTIFICATIONS TAB
            ═════════════════════════════════════════════════════════════════════════ --}}
            <div id="content-notifications" class="tab-content hidden">
                @include('dashboard.partials.notifications')
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.borderColor = 'transparent';
                b.style.color = '';
                b.classList.add('text-gray-500');
            });
            document.getElementById('content-' + tabName).classList.remove('hidden');
            const primary = '{{ $primary_color }}';
            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.style.borderColor = primary;
                btn.style.color = primary;
                btn.classList.remove('text-gray-500');
            }
        }

        // Auto-switch tab if tab parameter is present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'notifications') {
                switchTab('notifications');
            }
        });
    </script>
@endsection