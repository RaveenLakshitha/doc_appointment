@extends('layouts.app')

@section('title', __('file.prescription_details') . ' #' . $prescription->id)

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-6 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center mb-3">
            <button onclick="window.history.back()" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('file.back') }}
            </button>
        </div>

        <div class="flex items-center justify-between flex-wrap gap-4">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.prescription_for') }} {{ $prescription->patient?->getFullNameAttribute() ?? 'Unknown Patient' }}
            </h1>

            <span class="inline-block px-4 py-1.5 text-sm font-medium rounded-full
                {{ $prescription->type === 'Emergency' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200' : '' }}
                {{ $prescription->type === 'Chronic'   ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200' : '' }}
                {{ $prescription->type === 'Follow-up' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200' : '' }}
                {{ $prescription->type === 'Standard'  ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : '' }}
                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                {{ $prescription->type ?? 'Standard' }}
            </span>
        </div>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $prescription->prescription_date?->format('F d, Y') ?? 'N/A' }}
            • {{ __('file.prescribed_by') }} {{ $prescription->doctor?->getFullNameAttribute() ?? 'Unknown Doctor' }}
        </p>
    </div>

    <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <!-- Mobile Tab Selector (Visible only on mobile) -->
            <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                <select id="mobile-tab-select" onchange="switchTab(this.value)"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900 dark:focus:ring-gray-500">
                    <option value="overview">{{ __('file.overview') }}</option>
                    <option value="medications">{{ __('file.medications') }} ({{ $prescription->medications->count() }})</option>
                </select>
            </div>

            <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
            <nav class="hidden sm:flex overflow-x-auto no-scrollbar " aria-label="Tabs">
                <button type="button" onclick="switchTab('overview')" id="tab-overview"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                    {{ __('file.overview') }}
                </button>
                <button type="button" onclick="switchTab('medications')" id="tab-medications"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                    {{ __('file.medications') }} ({{ $prescription->medications->count() }})
                </button>
            </nav>
        </div>

        <div class="p-6 space-y-8">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('file.patient_information') }}
                            </h3>
                            <dl class="space-y-4 text-sm">
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.name') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $prescription->patient?->getFullNameAttribute() ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.mrn') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $prescription->patient?->medical_record_number ?? 'N/A' }}</dd>
                                </div>
                                @if($prescription->patient?->date_of_birth)
                                    <div class="flex justify-between">
                                        <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.date_of_birth') }}</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $prescription->patient->date_of_birth->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('file.prescription_details') }}
                            </h3>
                            <dl class="space-y-4 text-sm">
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.date') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $prescription->prescription_date?->format('M d, Y') ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.type') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $prescription->type ?? 'Standard' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.doctor') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $prescription->doctor?->getFullNameAttribute() ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @if($prescription->diagnosis)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('file.diagnosis_reason') }}
                                </h3>
                                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $prescription->diagnosis }}</p>
                                </div>
                            </div>
                        @endif

                        @if($prescription->notes)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('file.additional_notes') }}
                                </h3>
                                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $prescription->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if(!$prescription->diagnosis && !$prescription->notes)
                            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                {{ __('file.no_additional_information_provided') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Medications Tab -->
            <div id="content-medications" class="tab-content hidden">
                @if($prescription->medications->count() > 0)
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($prescription->medications as $medication)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $medication->name }}
                                        </h4>
                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.dosage') }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->dosage ?? '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.route') }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->route ?? '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.frequency') }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->frequency ?? '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.duration_days') }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->duration_days ?? '—' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.per_day') }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->per_day ?? '—' }}</span>
                                            </div>
                                            @if($medication->instructions)
                                                <div class="sm:col-span-2 lg:col-span-4 mt-3">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.instructions') }}:</span>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-200">{{ $medication->instructions }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <p class="text-gray-500 dark:text-gray-400 text-lg">
                            {{ __('file.no_medications_prescribed') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('prescriptions.print', $prescription) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            {{ __('file.print') }}
        </a>

        @can('prescriptions.edit')
            <a href="{{ route('prescriptions.edit', $prescription) }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('file.edit_prescription') }}
            </a>
        @endcan

        <a href="{{ route('prescriptions.index') }}"
           class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition">
            {{ __('file.back_to_list') }}
        </a>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
        b.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700','dark:hover:text-gray-300','hover:bg-gray-50','dark:hover:bg-gray-700/30');
    });

    // Update mobile select if present
    const mobileSelect = document.getElementById('mobile-tab-select');
    if (mobileSelect) mobileSelect.value = tabName;

    document.getElementById('content-' + tabName).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tabName);
    if (btn) {
        btn.classList.add('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
        btn.classList.remove('text-gray-500','dark:text-gray-400');

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

// Open overview by default
switchTab('overview');
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection