@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back, Dr. {{ $userName ?? 'Doctor' }}!</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Here's your schedule and quick actions for today.</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $currentDate }}</p>
        </div>

        <div class="mb-10 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button onclick="switchTab('overview')" id="tab-overview" class="tab-btn border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 py-4 px-1 text-sm font-medium">Overview</button>
                <button onclick="switchTab('notifications')" id="tab-notifications" class="tab-btn relative">
                    Notifications
                    @if($unreadCount > 0)
                        <span class="absolute top-3 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </button>
            </nav>
        </div>

        <div id="content-overview" class="tab-content">

            <!-- Today's Schedule -->
            <section class="mb-12">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Today's Schedule</h2>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                        {{ $todayAppointments->count() }} appointment{{ $todayAppointments->count() !== 1 ? 's' : '' }}
                    </span>
                </div>

                @if($todayAppointments->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No appointments today</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Your calendar is clear for the rest of the day.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($todayAppointments as $appt)
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-all duration-150">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                                            {{ $appt->patient?->first_name ?? 'Patient' }} {{ $appt->patient?->last_name ?? '' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $appt->appointment_type ?? 'consultation')) }}
                                        </p>
                                    </div>
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
                                        {{ $appt->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : '' }}
                                        {{ $appt->status === 'pending'  ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                                        {{ $appt->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                        {{ in_array($appt->status, ['cancelled','rejected']) ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </div>

                                <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $appt->scheduled_start->format('h:i A') }} – {{ $appt->scheduled_end->format('h:i A') }}</span>
                                        <span class="ml-2 text-gray-500 dark:text-gray-400">
                                            ({{ $appt->scheduled_start->diffInMinutes($appt->scheduled_end) }} min)
                                        </span>
                                    </div>

                                    @if($appt->reason_for_visit)
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-400 line-clamp-2">{{ $appt->reason_for_visit }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('appointments.show', $appt->id) }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Upcoming Appointments -->
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">Upcoming Appointments</h2>

                @if($upcomingAppointments->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-gray-500 dark:text-gray-400">No upcoming appointments in the next 7 days.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $appt)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                            {{ $appt->patient?->first_name ?? 'Patient' }} {{ $appt->patient?->last_name ?? '' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $appt->appointment_type ?? 'consultation')) }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $appt->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </div>

                                <div class="mt-3 text-sm text-gray-700 dark:text-gray-300 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $appt->scheduled_start->format('l, M j') }} at {{ $appt->scheduled_start->format('h:i A') }}
                                </div>

                                @if($appt->reason_for_visit)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-1">
                                        {{ Str::limit($appt->reason_for_visit, 80) }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('appointments.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            View full calendar →
                        </a>
                    </div>
                @endif
            </section>

            <!-- Attendance & Leave Request -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">

                <!-- Today's Attendance (self check-in / check-out) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Today's Attendance</h3>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($todayAttendance ?? false)
                        <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="font-medium">Status:</span>
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
                                    {{ $todayAttendance->status === 'present' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : '' }}
                                    {{ $todayAttendance->status === 'late' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : '' }}
                                    {{ $todayAttendance->status === 'absent' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : '' }}">
                                    {{ ucfirst($todayAttendance->status) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="font-medium">Clock In:</span>
                                <span>{{ $todayAttendance->clock_in ? $todayAttendance->clock_in->format('h:i A') : '—' }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="font-medium">Clock Out:</span>
                                <span>{{ $todayAttendance->clock_out ? $todayAttendance->clock_out->format('h:i A') : '—' }}</span>
                            </div>

                            @if(!$todayAttendance->clock_out)
                                <form action="{{ route('attendance.self-check-out') }}" method="POST" class="mt-6">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                        Check Out Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't marked attendance yet today.</p>
                            <form action="{{ route('attendance.self-check-in') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                    Check In Now
                                </button>
                            </form>
                            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                Checking in will mark you as present for {{ now()->format('d M Y') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Leave Request Form (same as previous) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5">Request Leave</h3>

                    @if(session('leave_success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('leave_success') }}
                        </div>
                    @endif

                    <form action="{{ route('leave-requests.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ Auth::user()->employee?->id ?? '' }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Leave Type</label>
                            <select name="leave_type_id" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select leave type</option>
                                @foreach($leaveTypes ?? [] as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('leave_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date</label>
                                <input type="date" name="start_date" required min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Date</label>
                                <input type="date" name="end_date" required min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Reason</label>
                            <textarea name="reason" rows="3" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end pt-3">
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                Submit Leave Request
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </div>


        <div id="content-notifications" class="tab-content hidden">
            <!-- Your existing notifications code -->
        </div>

    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-500','text-blue-600','dark:text-blue-400');
        b.classList.add('border-transparent','text-gray-500','hover:text-gray-700','hover:border-gray-300','dark:text-gray-400','dark:hover:text-gray-300');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tabName);
    if (btn) {
        btn.classList.remove('border-transparent','text-gray-500','hover:text-gray-700','hover:border-gray-300','dark:text-gray-400','dark:hover:text-gray-300');
        btn.classList.add('border-blue-500','text-blue-600','dark:text-blue-400');
    }
}
</script>
@endsection