@extends('layouts.app')

@section('content')

    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        {{-- ── Header ── --}}
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    Bulk Mark Attendance
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Record attendance for all employees at once
                </p>
            </div>

        </div>

        <form method="POST" action="{{ route('attendances.bulk-mark.store') }}" id="attendanceForm">
            @csrf

            {{-- ── Date & Defaults ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', $today) }}" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none {{ $errors->has('date') ? 'border-red-500' : '' }}">
                    @error('date')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Clock In</label>
                    <input type="time" name="default_clock_in" id="defaultClockIn"
                        value="{{ old('default_clock_in', '09:00') }}"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Clock Out</label>
                    <input type="time" name="default_clock_out" id="defaultClockOut"
                        value="{{ old('default_clock_out', '17:00') }}"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>
            </div>

            {{-- ── Table Card ── --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Toolbar --}}
                <div
                    class="flex flex-col gap-3 px-4 sm:px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">

                    {{-- Row 1: title + search + mark-all --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                        {{-- Left: title & counts --}}
                        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Employees</h2>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                {{ count($employees) }} total
                            </span>
                            <span id="filteredCount"
                                class="hidden inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                                0 shown
                            </span>
                            <div id="liveStats" class="flex items-center gap-1.5 flex-wrap"></div>
                        </div>

                        {{-- Centre: search --}}
                        <div class="relative flex-1 min-w-[180px]">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="M21 21l-4.35-4.35" />
                                </svg>
                            </div>
                            <input type="text" id="employeeSearch"
                                class="w-full pl-9 pr-8 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                autocomplete="off">
                            <button type="button" id="clearSearch"
                                class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M18 6L6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Right: mark-all --}}
                        <div class="flex items-center gap-2 flex-wrap shrink-0">
                            <span class="text-xs text-gray-400 dark:text-gray-500 font-medium hidden sm:inline">Mark
                                all:</span>
                            <button type="button" onclick="markAll('present')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs font-medium text-gray-700 dark:text-gray-200 transition shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>Present
                            </button>
                            <button type="button" onclick="markAll('absent')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs font-medium text-gray-700 dark:text-gray-200 transition shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>Absent
                            </button>
                            <button type="button" onclick="markAll('late')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs font-medium text-gray-700 dark:text-gray-200 transition shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>Late
                            </button>
                            <button type="button" onclick="markAll('leave')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs font-medium text-gray-700 dark:text-gray-200 transition shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0"></span>Leave
                            </button>
                        </div>{{-- /mark-all --}}
                    </div>{{-- /row 1 --}}

                    {{-- No-results message --}}
                    <p id="noResults" class="hidden text-sm text-gray-500 dark:text-gray-400 py-0.5">
                        No employees match "<span id="noResultsTerm"
                            class="font-medium text-gray-700 dark:text-gray-300"></span>"
                    </p>
                </div>{{-- /toolbar --}}

                {{-- Table --}}
                <div class="overflow-x-auto overflow-y-auto max-h-[560px]"
                    style="scrollbar-width:thin;scrollbar-color:#d1d5db transparent;">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                            <tr>
                                <th
                                    class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[160px]">
                                    Employee
                                </th>
                                {{-- Status columns - each a pill selector --}}
                                <th
                                    class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    <span class="hidden md:inline">Present</span><span class="md:hidden">P</span>
                                </th>
                                <th
                                    class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    <span class="hidden md:inline">Absent</span><span class="md:hidden">A</span>
                                </th>
                                <th
                                    class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    <span class="hidden md:inline">Late</span><span class="md:hidden">L</span>
                                </th>
                                <th
                                    class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    <span class="hidden md:inline">Half Day</span><span class="md:hidden">H</span>
                                </th>
                                <th
                                    class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    <span class="hidden md:inline">On Leave</span><span class="md:hidden">OL</span>
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">
                                    Clock In</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">
                                    Clock Out</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[140px]">
                                    Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($employees as $employee)
                                <tr class="hover:bg-gray-800 transition-colors duration-100">

                                    {{-- Employee --}}
                                    <td class="px-4 sm:px-6 py-3">
                                        <div class="emp-name font-medium text-gray-900 dark:text-white leading-tight">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </div>
                                        <div class="emp-code text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                                            {{ $employee->employee_code }}
                                        </div>
                                    </td>

                                    {{-- Present --}}
                                    <td class="px-3 py-3 text-center">
                                        <label
                                            class="status-pill inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full border-2 text-xs font-semibold cursor-pointer transition-all duration-150
                                                                     border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                                                     hover:border-emerald-400 hover:text-emerald-600 dark:hover:border-emerald-500 dark:hover:text-emerald-400"
                                            data-status="present" title="Present">
                                            <input type="radio" name="status[{{ $employee->id }}]" value="present" {{ old("status.{$employee->id}", 'present') === 'present' ? 'checked' : '' }}
                                                onchange="onStatusChange(this)" class="sr-only">
                                            <svg class="pill-icon w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.5" viewBox="0 0 24 24">
                                                <path d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="pill-text hidden sm:inline">Present</span>
                                        </label>
                                    </td>

                                    {{-- Absent --}}
                                    <td class="px-3 py-3 text-center">
                                        <label
                                            class="status-pill inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full border-2 text-xs font-semibold cursor-pointer transition-all duration-150
                                                                     border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                                                     hover:border-red-400 hover:text-red-600 dark:hover:border-red-500 dark:hover:text-red-400"
                                            data-status="absent" title="Absent">
                                            <input type="radio" name="status[{{ $employee->id }}]" value="absent" {{ old("status.{$employee->id}") === 'absent' ? 'checked' : '' }}
                                                onchange="onStatusChange(this)" class="sr-only">
                                            <svg class="pill-icon w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2.5" viewBox="0 0 24 24">
                                                <path d="M18 6L6 18M6 6l12 12" />
                                            </svg>
                                            <span class="pill-text hidden sm:inline">Absent</span>
                                        </label>
                                    </td>

                                    {{-- Late --}}
                                    <td class="px-3 py-3 text-center">
                                        <label
                                            class="status-pill inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full border-2 text-xs font-semibold cursor-pointer transition-all duration-150
                                                                     border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                                                     hover:border-amber-400 hover:text-amber-600 dark:hover:border-amber-400 dark:hover:text-amber-400"
                                            data-status="late" title="Late">
                                            <input type="radio" name="status[{{ $employee->id }}]" value="late" {{ old("status.{$employee->id}") === 'late' ? 'checked' : '' }}
                                                onchange="onStatusChange(this)" class="sr-only">
                                            <svg class="pill-icon w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <span class="pill-text hidden sm:inline">Late</span>
                                        </label>
                                    </td>

                                    {{-- Half Day --}}
                                    <td class="px-3 py-3 text-center">
                                        <label
                                            class="status-pill inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full border-2 text-xs font-semibold cursor-pointer transition-all duration-150
                                                                     border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                                                     hover:border-indigo-400 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-400"
                                            data-status="half_day" title="Half Day">
                                            <input type="radio" name="status[{{ $employee->id }}]" value="half_day" {{ old("status.{$employee->id}") === 'half_day' ? 'checked' : '' }}
                                                onchange="onStatusChange(this)" class="sr-only">
                                            <svg class="pill-icon w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2a10 10 0 0 1 0 20V2z" />
                                            </svg>
                                            <span class="pill-text hidden sm:inline">Half</span>
                                        </label>
                                    </td>

                                    {{-- On Leave --}}
                                    <td class="px-3 py-3 text-center">
                                        <label
                                            class="status-pill inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full border-2 text-xs font-semibold cursor-pointer transition-all duration-150
                                                                     border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                                                     hover:border-orange-400 hover:text-orange-600 dark:hover:border-orange-400 dark:hover:text-orange-400"
                                            data-status="leave" title="On Leave">
                                            <input type="radio" name="status[{{ $employee->id }}]" value="leave" {{ old("status.{$employee->id}") === 'leave' ? 'checked' : '' }}
                                                onchange="onStatusChange(this)" class="sr-only">
                                            <svg class="pill-icon w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                                <path d="M16 2v4M8 2v4M3 10h18" />
                                            </svg>
                                            <span class="pill-text hidden sm:inline">Leave</span>
                                        </label>
                                    </td>

                                    {{-- Clock In --}}
                                    <td class="px-3 py-3">
                                        <input type="time" name="clock_in[{{ $employee->id }}]"
                                            value="{{ old("clock_in.{$employee->id}") }}"
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                                    </td>

                                    {{-- Clock Out --}}
                                    <td class="px-3 py-3">
                                        <input type="time" name="clock_out[{{ $employee->id }}]"
                                            value="{{ old("clock_out.{$employee->id}") }}"
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                                    </td>

                                    {{-- Notes --}}
                                    <td class="px-3 py-3">
                                        <input type="text" name="notes[{{ $employee->id }}]"
                                            value="{{ old("notes.{$employee->id}") }}" placeholder="Optional…"
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- ── Actions ── --}}
            <div class="flex items-center gap-3 mt-6 flex-wrap">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Save Attendance
                </button>
                <a href="{{ route('attendances.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <style>
        /* Scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
    </style>

    <script>
        /* ── Pill active styles per status ── */
        const PILL_ACTIVE = {
            present: { bg: '#dcfce7', border: '#22c55e', text: '#15803d', darkBg: 'rgba(34,197,94,0.15)', darkBorder: '#22c55e', darkText: '#4ade80' },
            absent: { bg: '#fee2e2', border: '#ef4444', text: '#b91c1c', darkBg: 'rgba(239,68,68,0.15)', darkBorder: '#ef4444', darkText: '#f87171' },
            late: { bg: '#fef9c3', border: '#f59e0b', text: '#b45309', darkBg: 'rgba(245,158,11,0.15)', darkBorder: '#f59e0b', darkText: '#fcd34d' },
            half_day: { bg: '#e0e7ff', border: '#6366f1', text: '#4338ca', darkBg: 'rgba(99,102,241,0.15)', darkBorder: '#818cf8', darkText: '#a5b4fc' },
            leave: { bg: '#ffedd5', border: '#f97316', text: '#c2410c', darkBg: 'rgba(249,115,22,0.15)', darkBorder: '#fb923c', darkText: '#fdba74' },
        };

        function applyPillState(label, isChecked) {
            const status = label.dataset.status;
            const isDark = document.documentElement.classList.contains('dark');
            const s = PILL_ACTIVE[status] || {};

            if (isChecked) {
                label.style.backgroundColor = isDark ? s.darkBg : s.bg;
                label.style.borderColor = isDark ? s.darkBorder : s.border;
                label.style.color = isDark ? s.darkText : s.text;
            } else {
                label.style.backgroundColor = '';
                label.style.borderColor = '';
                label.style.color = '';
            }
        }

        function syncRow(input) {
            document.querySelectorAll(`input[name="${input.name}"]`).forEach(r => {
                applyPillState(r.closest('label'), r.checked);
            });
        }

        function onStatusChange(input) {
            syncRow(input);
            updateStats();
        }

        /* ── Mark All ── */
        function markAll(status) {
            document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => {
                r.checked = true;
                syncRow(r);
            });
            updateStats();
        }

        /* ── Live Stats ── */
        const STAT_CONFIGS = [
            { key: 'present', label: 'Present', lightBg: '#dcfce7', lightColor: '#15803d', darkBg: 'rgba(34,197,94,0.15)', darkColor: '#4ade80' },
            { key: 'absent', label: 'Absent', lightBg: '#fee2e2', lightColor: '#b91c1c', darkBg: 'rgba(239,68,68,0.15)', darkColor: '#f87171' },
            { key: 'late', label: 'Late', lightBg: '#fef9c3', lightColor: '#b45309', darkBg: 'rgba(245,158,11,0.15)', darkColor: '#fcd34d' },
            { key: 'half_day', label: 'Half Day', lightBg: '#e0e7ff', lightColor: '#4338ca', darkBg: 'rgba(99,102,241,0.15)', darkColor: '#a5b4fc' },
            { key: 'leave', label: 'Leave', lightBg: '#ffedd5', lightColor: '#c2410c', darkBg: 'rgba(249,115,22,0.15)', darkColor: '#fdba74' },
        ];

        function updateStats() {
            const counts = {};
            STAT_CONFIGS.forEach(c => counts[c.key] = 0);
            document.querySelectorAll('input[type="radio"]:checked').forEach(r => {
                if (counts[r.value] !== undefined) counts[r.value]++;
            });

            const isDark = document.documentElement.classList.contains('dark');
            const el = document.getElementById('liveStats');
            el.innerHTML = '';

            STAT_CONFIGS.forEach(({ key, label, lightBg, lightColor, darkBg, darkColor }) => {
                if (counts[key] > 0) {
                    const chip = document.createElement('span');
                    chip.style.cssText = `
                            display:inline-flex;align-items:center;padding:1px 8px;
                            border-radius:9999px;font-size:11px;font-weight:600;
                            background:${isDark ? darkBg : lightBg};
                            color:${isDark ? darkColor : lightColor};
                        `;
                    chip.textContent = `${counts[key]} ${label}`;
                    el.appendChild(chip);
                }
            });

            /* Re-sync all pill colours in case dark mode just toggled */
            document.querySelectorAll('input[type="radio"]:checked').forEach(r => {
                applyPillState(r.closest('label'), true);
            });
        }

        /* ── Theme ── */
        (function () {
            const saved = localStorage.getItem('att-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = saved === 'dark' || (!saved && prefersDark);
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.getElementById('iconSun').classList.add('hidden');
                document.getElementById('iconMoon').classList.remove('hidden');
            }
        })();

        document.getElementById('themeToggle').addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('att-theme', isDark ? 'dark' : 'light');
            document.getElementById('iconSun').classList.toggle('hidden', isDark);
            document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
            updateStats();
        });

        /* ── Default Times ── */
        document.getElementById('defaultClockIn').addEventListener('change', function () {
            document.querySelectorAll('input[name^="clock_in"]').forEach(i => { if (!i.value) i.value = this.value; });
        });
        document.getElementById('defaultClockOut').addEventListener('change', function () {
            document.querySelectorAll('input[name^="clock_out"]').forEach(i => { if (!i.value) i.value = this.value; });
        });

        /* ── Search Filter ── */
        const searchInput = document.getElementById('employeeSearch');
        const clearBtn = document.getElementById('clearSearch');
        const noResults = document.getElementById('noResults');
        const noResultsTerm = document.getElementById('noResultsTerm');
        const filteredCount = document.getElementById('filteredCount');
        const tableRows = document.querySelectorAll('#attendanceForm tbody tr');

        function filterRows() {
            const q = searchInput.value.trim().toLowerCase();
            clearBtn.classList.toggle('hidden', q === '');

            let visible = 0;
            tableRows.forEach(row => {
                const name = row.querySelector('.emp-name')?.textContent.toLowerCase() || '';
                const code = row.querySelector('.emp-code')?.textContent.toLowerCase() || '';
                const match = !q || name.includes(q) || code.includes(q);
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            const total = tableRows.length;
            if (q) {
                filteredCount.textContent = `${visible} shown`;
                filteredCount.classList.remove('hidden');
                noResultsTerm.textContent = searchInput.value.trim();
                noResults.classList.toggle('hidden', visible > 0);
            } else {
                filteredCount.classList.add('hidden');
                noResults.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', filterRows);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterRows();
            searchInput.focus();
        });

        /* ── Init ── */
        document.querySelectorAll('input[type="radio"]:checked').forEach(r => syncRow(r));
        updateStats();
    </script>

@endsection