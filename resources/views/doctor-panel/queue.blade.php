@extends('layouts.app')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ __('file.my_queue') ?? 'My Queue' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $date->format('D, M d, Y') }}
                </p>
            </div>

            <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                <button type="submit"
                    class="h-11 px-5 text-sm font-medium bg-gray-900 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 whitespace-nowrap transition-colors">
                    {{ __('file.filter') ?? 'Filter' }}
                </button>
            </form>
        </div>

        @if($queues->isEmpty())
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center text-gray-500 dark:text-gray-400">
                {{ __('file.no_approved_appointments_queued') ?? 'No approved appointments in queue.' }}
            </div>
        @else
            @foreach($queues as $session)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mb-6 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ $session['session_key'] }}
                                </h2>
                            </div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                {{ $session['patients']->count() }}
                                {{ Str::plural(__('file.patient') ?? 'Patient', $session['patients']->count()) }}
                            </span>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($session['patients'] as $patient)
                            <div
                                class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                                <div class="flex items-center gap-6 flex-1">
                                    <div class="text-4xl font-bold text-gray-900 dark:text-white w-16 shrink-0">
                                        #{{ $patient['queue_number'] }}
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $patient['patient_name'] }}
                                        </p>
                                        <p class="text-base text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $patient['time'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <form action="{{ route('queues.update-queue', $patient['id']) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="queue_number" value="{{ $patient['queue_number'] }}" min="1"
                                            class="h-11 w-20 px-3 text-base text-center font-semibold border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                                        <button type="submit"
                                            class="h-11 px-5 text-base font-medium bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            {{ __('file.update') ?? 'Update' }}
                                        </button>
                                    </form>
                                    @if(!isset($patient['status']) || in_array($patient['status'], [\App\Models\Appointment::STATUS_APPROVED, \App\Models\Appointment::STATUS_PAID]))
                                        <form action="{{ route('queues.start', $patient['id']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                {{ $hasRunning ? 'disabled' : '' }}
                                                class="inline-flex items-center px-3 py-1 bg-amber-600 text-white rounded text-xs transition-colors hover:bg-amber-700 h-11 {{ $hasRunning ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                <i class="fas fa-bullhorn mr-1.5"></i> {{ __('file.call_in') }}
                                            </button>
                                        </form>
                                    @elseif($patient['status'] === \App\Models\Appointment::STATUS_RUNNING)
                                        <span
                                            class="inline-flex items-center h-11 px-3 text-xs font-bold text-amber-600 uppercase bg-amber-50 rounded border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 animate-pulse"></span>
                                            {{ __('file.currently_serving') }}
                                        </span>
                                    @endif

                                    <a href="{{ route('appointments.show', $patient['id']) }}"
                                        class="h-11 px-4 text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white flex items-center transition-colors">
                                        {{ __('file.view_details') ?? 'View Details' }} &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection