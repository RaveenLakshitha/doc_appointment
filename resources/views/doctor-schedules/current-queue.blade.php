@extends('layouts.app')

@section('title', __('file.current_queue'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Current Queue • Dr. {{ $doctor->getFullNameAttribute() }}
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                {{ today()->format('l, d F Y') }} • {{ now()->format('h:i A') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Current Serving -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-10 text-center">
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-4 font-medium">
                    Now Serving
                </p>
                <div class="text-9xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">
                    {{ $current->queue_number ?? '—' }}
                </div>
                <p class="mt-6 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $current->patient?->getFullNameAttribute() ?? 'No patient' }}
                </p>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                    {{ $current->reason_for_visit ?? '—' }}
                </p>
            </div>

            <!-- Next in Queue -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                    Next Patients
                </h3>
                <div class="space-y-6">
                    @forelse($next as $appt)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    #{{ $appt->queue_number }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $appt->patient?->getFullNameAttribute() }}
                                </p>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $appt->scheduled_start->format('h:i A') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                            No upcoming patients
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-12 text-center text-sm text-gray-500 dark:text-gray-400">
            Auto-refreshes every 30 seconds • Last update: {{ now()->format('h:i:s A') }}
        </div>
    </div>
</div>

<script>
// Optional: auto refresh
setTimeout(() => location.reload(), 30000);
</script>
@endsection