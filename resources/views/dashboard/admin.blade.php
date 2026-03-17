{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
            {{-- Header --}}
            <div class=" mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back, Dr. Johnson!</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Here's what's happening today.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Dec 20, 2025 - Dec 20, 2025</p>
            </div>

            {{-- Tabs Navigation --}}
            <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div
                    class="sm:hidden p-4 bg-white dark:bg-gray-800 rounded-lg mb-4 border border-gray-200 dark:border-gray-700">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                        <option value="overview">Overview</option>
                        <option value="analytics">Analytics</option>
                        <option value="reports">Reports</option>
                        <option value="notifications">Notifications</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex space-x-8 no-scrollbar  overflow-x-auto"
                    aria-label="Tabs">
                    <button onclick="switchTab('overview')" id="tab-overview"
                        class="tab-btn border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 py-4 px-1 text-sm font-medium whitespace-nowrap">
                        Overview
                    </button>
                    <button onclick="switchTab('analytics')" id="tab-analytics"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                        Analytics
                    </button>
                    <button onclick="switchTab('reports')" id="tab-reports"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                        Reports
                    </button>
                    <button onclick="switchTab('notifications')" id="tab-notifications"
                        class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 py-4 px-1 text-sm font-medium relative whitespace-nowrap">
                        Notifications
                        @if($unreadCount > 0)
                            <span
                                class="absolute top-3 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>

            {{-- Overview Tab --}}
            <div id="content-overview" class="tab-content">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</h3>
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">$45,231.89</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">+20.1% from last month</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Appointments</h3>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">+2,350</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">+10.1% from last month</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Patients</h3>
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">+12,234</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">+19% from last month</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Staff</h3>
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">+573</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">+4 new this month</p>
                    </div>
                </div>

                {{-- Chart and Appointments --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Chart --}}
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Patient visits and revenue for the current
                            period.</p>
                        <div class="h-64 flex items-end justify-between space-x-2">
                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                <div class="flex-1 flex flex-col items-center">
                                    <div class="w-full bg-blue-500 rounded-t" style="height: {{ rand(40, 100) }}%"></div>
                                    <div class="w-full bg-purple-500 rounded-t mt-1" style="height: {{ rand(30, 80) }}%"></div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $month }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-center space-x-6 mt-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Patients</span>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Appointments --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Recent Appointments</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">You have 12 appointments today.</p>

                        <div class="space-y-4">
                            <div class="border-l-4 border-green-500 pl-4 py-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">John Smith</h4>
                                    <span
                                        class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded">Confirmed</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Check-up</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Today, 10:00 AM</span>
                                    <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">More</button>
                                </div>
                            </div>

                            <div class="border-l-4 border-yellow-500 pl-4 py-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Emily Davis</h4>
                                    <span
                                        class="text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 px-2 py-1 rounded">In
                                        Progress</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Consultation</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Today, 11:30 AM</span>
                                    <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">More</button>
                                </div>
                            </div>

                            <div class="border-l-4 border-gray-500 pl-4 py-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Robert Wilson</h4>
                                    <span
                                        class="text-xs bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200 px-2 py-1 rounded">Completed</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Follow-up</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Today, 09:15 AM</span>
                                    <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics Tab --}}
            <div id="content-analytics" class="tab-content hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Detailed Analytics</h2>
                    <p class="text-gray-600 dark:text-gray-400">Insights and trends from your clinic data</p>
                    <div class="flex space-x-4 mt-4">
                        <button
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Filter</button>
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Export</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Patient Demographics --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Patient Demographics</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Age and gender distribution</p>
                        <div class="space-y-4">
                            @foreach([['0-17', 60], ['25-34', 120], ['45-54', 200], ['65+', 150]] as [$age, $value])
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $age }}</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $value }}</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <div class="h-8 bg-blue-500 rounded" style="width: {{ ($value / 280) * 50 }}%"></div>
                                        <div class="h-8 bg-pink-500 rounded" style="width: {{ ($value / 280) * 50 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-center space-x-6 mt-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Male</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-pink-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Female</span>
                            </div>
                        </div>
                    </div>

                    {{-- Appointment Types --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Appointment Types</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Distribution by service category</p>
                        <div class="flex items-center justify-center h-64">
                            <div class="relative w-48 h-48">
                                <svg viewBox="0 0 100 100" class="transform -rotate-90">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="20"
                                        stroke-dasharray="87.96 251.33"></circle>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#8b5cf6" stroke-width="20"
                                        stroke-dasharray="62.83 251.33" stroke-dashoffset="-87.96"></circle>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#ec4899" stroke-width="20"
                                        stroke-dasharray="50.27 251.33" stroke-dashoffset="-150.79"></circle>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="20"
                                        stroke-dasharray="25.13 251.33" stroke-dashoffset="-201.06"></circle>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="20"
                                        stroke-dasharray="12.57 251.33" stroke-dashoffset="-226.19"></circle>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#6b7280" stroke-width="20"
                                        stroke-dasharray="12.57 251.33" stroke-dashoffset="-238.76"></circle>
                                </svg>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>Check-up 35%
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded mr-2"></div>Consultation 25%
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-pink-500 rounded mr-2"></div>Follow-up 20%
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>Procedure 10%
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>Emergency 5%
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded mr-2"></div>Other 5%
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Patient Satisfaction & Staff Performance --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Patient Satisfaction</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Based on feedback surveys</p>
                        <div class="space-y-4">
                            @foreach([['Overall Experience', 87], ['Wait Times', 72], ['Staff Friendliness', 94], ['Treatment Effectiveness', 89]] as [$metric, $score])
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $metric }}</span>
                                        <span class="text-gray-900 dark:text-white font-semibold">{{ $score }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $score }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Staff Performance</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Top performing staff members</p>
                        <div class="space-y-4">
                            @foreach([['S', 'Dr. Sarah Chen', 'Cardiologist', 42, 4.9], ['M', 'Dr. Michael Rodriguez', 'Pediatrician', 38, 4.8], ['E', 'Dr. Emily Johnson', 'Neurologist', 35, 4.7], ['R', 'Nurse Robert Kim', 'Head Nurse', 56, 4.9]] as [$initial, $name, $role, $patients, $rating])
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ $initial }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $role }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $patients }} patients • Rating:
                                            {{ $rating }}/5
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reports Tab --}}
            <div id="content-reports" class="tab-content hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Available Reports</h2>
                    <p class="text-gray-600 dark:text-gray-400">Access and generate detailed reports</p>
                    <button class="mt-4 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">Generate
                        New Report</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    {{-- Financial Reports --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Financial Reports</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Revenue, expenses, and billing</p>
                        <div class="space-y-4">
                            @foreach([['Monthly Revenue Summary', 'Today'], ['Quarterly Financial Analysis', 'Last week'], ['Insurance Claims Report', '2 days ago'], ['Outstanding Payments', 'Yesterday']] as [$report, $updated])
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $report }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Updated: {{ $updated }}</p>
                                </div>
                            @endforeach
                        </div>
                        <button class="mt-6 text-sm text-blue-600 dark:text-blue-400 hover:underline">View all financial
                            reports</button>
                    </div>

                    {{-- Patient Reports --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Patient Reports</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Demographics and visit analytics</p>
                        <div class="space-y-4">
                            @foreach([['New Patient Registrations', 'Today'], ['Patient Demographics', '3 days ago'], ['Visit Frequency Analysis', 'Last week'], ['Treatment Outcomes', 'Yesterday']] as [$report, $updated])
                                <div class="border-l-4 border-purple-500 pl-4 py-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $report }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Updated: {{ $updated }}</p>
                                </div>
                            @endforeach
                        </div>
                        <button class="mt-6 text-sm text-blue-600 dark:text-blue-400 hover:underline">View all patient
                            reports</button>
                    </div>

                    {{-- Operational Reports --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Operational Reports</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Staff, inventory, and efficiency</p>
                        <div class="space-y-4">
                            @foreach([['Staff Performance Metrics', 'Yesterday'], ['Inventory Status', 'Today'], ['Room Utilization', '2 days ago'], ['Wait Time Analysis', 'Last week']] as [$report, $updated])
                                <div class="border-l-4 border-green-500 pl-4 py-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $report }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Updated: {{ $updated }}</p>
                                </div>
                            @endforeach
                        </div>
                        <button class="mt-6 text-sm text-blue-600 dark:text-blue-400 hover:underline">View all operational
                            reports</button>
                    </div>
                </div>

                {{-- Recent Report Activity --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Recent Report Activity</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Reports generated or viewed recently</p>
                    <div class="space-y-4">
                        @foreach([['J', 'Dr. Johnson generated Monthly Revenue Summary', '2 hours ago'], ['S', 'Admin Sarah viewed Staff Performance Metrics', 'Yesterday, 4:30 PM'], ['R', 'Dr. Rodriguez generated Patient Demographics', 'Yesterday, 2:15 PM'], ['K', 'Nurse Kim viewed Inventory Status', '2 days ago'], ['C', 'Dr. Chen generated Treatment Outcomes', '3 days ago']] as [$initial, $activity, $time])
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-700 dark:text-gray-200 font-semibold">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $time }}</p>
                                    </div>
                                </div>
                                <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Notifications Tab --}}
            <div id="content-notifications" class="tab-content hidden">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Notifications</h2>
                        <p class="text-gray-600 dark:text-gray-400">Stay updated with important alerts and messages</p>
                    </div>
                    <div class="flex space-x-4">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Mark All as Read
                            </button>
                        </form>
                        <!-- Optional: Add filter button later -->
                        <!-- <button class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Filter</button> -->
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
                    @if($notifications->isEmpty())
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No notifications yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">We'll let you know when something new
                                arrives.</p>
                        </div>
                    @else
                        @foreach($notifications as $notification)
                                <div
                                    class="p-5 notification-item {{ $notification->read_at ? 'opacity-60 bg-gray-50 dark:bg-gray-900/50' : 'bg-blue-50 dark:bg-blue-900/20' }} hover:bg-gray-100 dark:hover:bg-gray-800/50 transition">
                                    <div class="flex items-start space-x-4">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0">
                                            @php
                                                $color = $notification->data['color'] ?? 'blue';
                                                $iconClass = $notification->data['icon'] ?? 'bell';
                                            @endphp
                                            <div class="w-10 h-10 bg-{{ $color }}-500 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="{{ $iconClass === 'calendar-plus' ?
                            'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' :
                            'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' }}" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $notification->data['message'] ?? 'System notification' }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $notification->data['patient_name'] ?? '' }}
                                                {{ !empty($notification->data['reason']) ? ' - ' . Str::limit($notification->data['reason'], 60) : '' }}
                                            </p>
                                            <div class="mt-2 flex items-center space-x-4 text-xs">
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                                @if(!empty($notification->data['link']))
                                                    <a href="{{ $notification->data['link'] }}"
                                                        class="text-blue-600 dark:text-blue-400 hover:underline"
                                                        onclick="event.preventDefault(); markAsRead('{{ $notification->id }}', this); window.location.href = this.href;">
                                                        View details
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Mark read button (only if unread) -->
                                        @if(!$notification->read_at)
                                            <button onclick="markAsRead('{{ $notification->id }}', this)"
                                                class="text-xs text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white font-medium">
                                                Mark read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) {
                activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');

                // Update mobile select if present
                const mobileSelect = document.getElementById('mobile-tab-select');
                if (mobileSelect) mobileSelect.value = tabName;

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = activeTab.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = activeTab.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }

        function markAsRead(notificationId, element) {
            const notificationRow = element.closest('.notification-item');
            const button = element.tagName === 'BUTTON' ? element : element.parentElement.querySelector('button');

            // Visual feedback immediately
            if (button) {
                button.disabled = true;
                button.textContent = 'Marking...';
            }
            notificationRow.classList.add('opacity-60');

            const readRoutePattern = '{{ route("notifications.read", ":id") }}';

            const url = readRoutePattern.replace(':id', notificationId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to mark as read');
                    return response.json();
                })
                .then(() => {
                    // Success → update UI
                    if (button) button.remove();           // remove button
                    notificationRow.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                    notificationRow.classList.add('opacity-60', 'bg-gray-50', 'dark:bg-gray-900/50');

                    // Update global badge count
                    updateNotificationBadge();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Could not mark as read. Please try again.');
                    if (button) button.textContent = 'Mark read';
                    notificationRow.classList.remove('opacity-60');
                });
        }

        function updateNotificationBadge() {
            const badge = document.querySelector('#tab-notifications span');
            if (badge) {
                let count = parseInt(badge.textContent) || 0;
                count = Math.max(0, count - 1);
                badge.textContent = count > 0 ? count : '';
                if (count <= 0) badge.remove();
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection