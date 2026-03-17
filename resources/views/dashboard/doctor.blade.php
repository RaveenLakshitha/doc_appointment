@extends('layouts.app')

@section('title', __('file.doctor_dashboard') ?? 'Doctor Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="px-4 sm:px-6 lg:px-8 pb-10 pt-20">

            {{-- ── PAGE HEADER ────────────────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('file.welcome_back') ?? 'Welcome back' }}, Dr. {{ $userName }}!
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ $currentDate }}</p>
                </div>
                {{-- Quick stat pills --}}
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium"
                        style="background:{{ $primary_color }}20; color:{{ $primary_color }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $todayAppointments->count() }} {{ __('file.appointments_today') ?? 'Today\'s Appointments' }}
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
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div class="sm:hidden p-4 bg-white dark:bg-gray-800 rounded-lg mb-4 border border-gray-200 dark:border-gray-700">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900"
                        style="focus-ring-color: {{ $primary_color }}">
                        <option value="overview">{{ __('file.overview') ?? 'Overview' }}</option>
                        <option value="notifications">{{ __('file.notifications') ?? 'Notifications' }}</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex space-x-6 no-scrollbar  overflow-x-auto" aria-label="Dashboard Tabs">
                    <button onclick="switchTab('overview')" id="tab-overview"
                        class="tab-btn pb-3 px-1 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap"
                        style="border-color:{{ $primary_color }}; color:{{ $primary_color }}">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                        </svg>
                        {{ __('file.overview') ?? 'Overview' }}
                    </button>
                    <button onclick="switchTab('notifications')" id="tab-notifications"
                        class="tab-btn relative pb-3 px-1 text-sm font-semibold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors whitespace-nowrap">
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

                {{-- ── TODAY'S APPOINTMENTS ────────────────────────────────────────── --}}
                <section class="mb-10">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color:{{ $primary_color }}">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('file.todays_schedule') ?? "Today's Schedule" }}
                        </h2>
                    </div>

                    @if($todayAppointments->isEmpty())
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                                style="background:{{ $primary_color }}15">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="color:{{ $primary_color }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ __('file.no_appointments_today') ?? 'No appointments today' }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('file.calendar_clear') ?? 'Your schedule is clear.' }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach($todayAppointments as $appt)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-all group">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                                                style="background: linear-gradient(135deg,{{ $primary_color }},{{ $primary_color }}cc)">
                                                {{ strtoupper(substr($appt->patient?->first_name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">
                                                    {{ $appt->patient?->first_name ?? 'Patient' }}
                                                    {{ $appt->patient?->last_name ?? '' }}
                                                </h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    {{ ucfirst(str_replace('_', ' ', $appt->appointment_type ?? 'Consultation')) }}
                                                </p>
                                            </div>
                                        </div>
                                        @php
                                            $statusMap = [
                                                'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                            ];
                                            $statusClass = $statusMap[$appt->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </div>

                                    {{-- Time --}}
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium">{{ $appt->scheduled_start->format('h:i A') }}</span>
                                        <span class="text-gray-400">→</span>
                                        <span>{{ $appt->scheduled_end->format('h:i A') }}</span>
                                        <span
                                            class="text-gray-400 text-xs">({{ $appt->scheduled_start->diffInMinutes($appt->scheduled_end) }}min)</span>
                                    </div>

                                    @if($appt->reason_for_visit)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $appt->reason_for_visit }}
                                        </p>
                                    @endif

                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                        <a href="{{ route('appointments.show', $appt->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition"
                                            style="background-color:{{ $primary_color }}">
                                            {{ __('file.view_details') ?? 'View Details' }}
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- ── ATTENDANCE & LEAVE ──────────────────────────────────────────── --}}
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
                b.classList.add('text-gray-500', 'dark:text-gray-400');
                b.classList.remove('text-white');
            });
            document.getElementById('content-' + tabName).classList.remove('hidden');

            const primary = '{{ $primary_color }}';
            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.style.borderColor = primary;
                btn.style.color = primary;
                btn.classList.remove('text-gray-500', 'dark:text-gray-400');

                // Update mobile select if present
                const mobileSelect = document.getElementById('mobile-tab-select');
                if (mobileSelect) mobileSelect.value = tabName;

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = btn.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = btn.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
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

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endsection