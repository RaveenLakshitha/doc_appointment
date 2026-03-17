{{-- dashboard/partials/attendance-leave.blade.php
Shared by doctor.blade.php and default.blade.php
Requires: $todayAttendance, $leaveTypes, $leaveBalances, $primary_color
--}}
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

    {{-- ── TODAY'S ATTENDANCE ─────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Card header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3"
            style="background: linear-gradient(135deg, {{ $primary_color }}15, {{ $primary_color }}08)">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $primary_color }}20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color:{{ $primary_color }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">
                    {{ __('file.todays_attendance') ?? "Today's Attendance" }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
            </div>
        </div>

        <div class="p-6">
            {{-- Flash messages --}}
            @if(session('success'))
                <div
                    class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm dark:bg-green-900/20 dark:border-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div
                    class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl text-sm dark:bg-yellow-900/20 dark:border-yellow-700 dark:text-yellow-300">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm dark:bg-red-900/20 dark:border-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            @if($todayAttendance ?? false)
                {{-- Status badge row --}}
                <div class="flex items-center justify-between mb-5">
                    @php
                        $attStatusClass = match ($todayAttendance->status) {
                            'present' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                            'late' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                            'absent' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full {{ $attStatusClass }}">
                        <span class="w-2 h-2 rounded-full inline-block"
                            style="background: {{ $todayAttendance->status === 'present' ? '#16a34a' : ($todayAttendance->status === 'late' ? '#ca8a04' : '#dc2626') }}"></span>
                        {{ ucfirst($todayAttendance->status) }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('file.marked') ?? 'Marked' }}</span>
                </div>

                {{-- Times --}}
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="rounded-xl p-3 bg-gray-50 dark:bg-gray-700/50 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.clock_in') ?? 'Clock In' }}</p>
                        <p class="font-bold text-gray-900 dark:text-white text-base">
                            {{ $todayAttendance->clock_in ? $todayAttendance->clock_in->format('h:i A') : '—' }}
                        </p>
                    </div>
                    <div class="rounded-xl p-3 bg-gray-50 dark:bg-gray-700/50 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.clock_out') ?? 'Clock Out' }}
                        </p>
                        <p class="font-bold text-gray-900 dark:text-white text-base">
                            {{ $todayAttendance->clock_out ? $todayAttendance->clock_out->format('h:i A') : '—' }}
                        </p>
                    </div>
                </div>

                @if(!$todayAttendance->clock_out)
                    <form action="{{ route('attendance.self-check-out') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90"
                            style="background-color:{{ $primary_color }}">
                            {{ __('file.check_out_now') ?? 'Check Out Now' }}
                        </button>
                    </form>
                @else
                    <div class="text-center py-2 text-sm text-gray-500 dark:text-gray-400">
                        ✓ {{ __('file.attendance_complete') ?? 'Attendance complete for today' }}
                    </div>
                @endif
            @else
                <div class="text-center py-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
                        style="background:{{ $primary_color }}15">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color:{{ $primary_color }}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-5">
                        {{ __('file.not_marked_attendance') ?? "You haven't marked attendance yet today." }}
                    </p>
                    <form action="{{ route('attendance.self-check-in') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90"
                            style="background-color:{{ $primary_color }}">
                            {{ __('file.check_in_now') ?? 'Check In Now' }}
                        </button>
                    </form>
                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                        {{ __('file.check_in_marks_present') ?? 'This will mark you as present for' }}
                        {{ now()->format('d M Y') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── LEAVE REQUEST ──────────────────────────────────────────────────── --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Card header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3"
            style="background: linear-gradient(135deg, {{ $primary_color }}15, {{ $primary_color }}08)">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $primary_color }}20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color:{{ $primary_color }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">
                    {{ __('file.request_leave') ?? 'Request Leave' }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->year }}
                    {{ __('file.leave_balances') ?? 'Leave Balances' }}
                </p>
            </div>
        </div>

        <div class="p-6">

            @if(session('leave_success'))
                <div
                    class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm dark:bg-green-900/20 dark:border-green-700 dark:text-green-300">
                    {{ session('leave_success') }}
                </div>
            @endif

            <form action="{{ route('leave-requests.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="employee_id" value="{{ Auth::user()->employee?->id ?? '' }}">

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">
                        {{ __('file.leave_type') ?? 'Leave Type' }}
                    </label>
                    <select name="leave_type_id" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:outline-none transition"
                        style="--tw-ring-color:{{ $primary_color }}50">
                        <option value="">{{ __('file.select_leave_type') ?? 'Select leave type' }}</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('leave_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">
                            {{ __('file.start_date') ?? 'Start Date' }}
                        </label>
                        <input type="date" name="start_date" required min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:outline-none">
                        @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">
                            {{ __('file.end_date') ?? 'End Date' }}
                        </label>
                        <input type="date" name="end_date" required min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:outline-none">
                        @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">
                        {{ __('file.reason') ?? 'Reason' }}
                    </label>
                    <textarea name="reason" rows="2"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:outline-none resize-none"></textarea>
                    @error('reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90"
                    style="background-color:{{ $primary_color }}">
                    {{ __('file.submit_leave_request') ?? 'Submit Leave Request' }}
                </button>
            </form>
        </div>
    </div>

</section>