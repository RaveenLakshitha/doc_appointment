@extends('layouts.app')

@section('title', 'Pending Appointments - Review')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pending Appointments</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Review appointment requests → click "View Details" to approve, reject or assign
            </p>
        </div>

        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                {{ $appointments->total() }} Pending
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $appointment->patient?->getFullNameAttribute() ?? '—' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $appointment->patient?->phone ?? 'No phone' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $appointment->appointment_type === $TYPE_SPECIFIC 
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200' 
                                        : 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200' }}">
                                    {{ str_replace('_', ' ', ucfirst($appointment->appointment_type ?? '')) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $appointment->created_at->format('M d, Y · h:i A') }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-md truncate">
                                {{ Str::limit($appointment->reason_for_visit ?? '—', 70) }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('appointments.show', $appointment) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="text-xl font-medium mb-2">No pending appointments</div>
                                <p>All requests have been processed.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection