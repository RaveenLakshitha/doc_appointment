{{-- resources/views/patients/show.blade.php --}}
@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('patients.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.patients') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($patient->full_name, 30) }}</span>
        </div>
    </div>

    <!-- Main Layout: Profile Card (Left) + Content (Right) -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- LEFT SIDE: Profile Card -->
        <div class="lg:w-80 xl:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 sticky top-6">
                <!-- Profile Header -->
                <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $patient->full_name }}</h2>

                    <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium">
                            MRN: {{ $patient->medical_record_number ?? '—' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $patient->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                            {{ $patient->is_active ? __('file.active') : __('file.inactive') }}
                        </span>
                        @if($patient->allergies && count($patient->allergies))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                {{ __('file.allergies') }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('patients.edit', $patient) }}"
                       class="inline-flex items-center justify-center w-full px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('file.edit_patient') }}
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.total_appointments') }}</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $patient->appointments_count ?? $patient->appointments->count() }}</div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.blood_type') }}</div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $patient->blood_type ?? '—' }}</div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.date_of_birth') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.gender') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($patient->gender ?? '—') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.phone') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.email') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->email ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Tabbed Content -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ activeTab: 'overview' }">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-4 px-4 overflow-x-auto">
                        <button @click="activeTab = 'overview'" 
                                :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.overview') }}
                        </button>
                        <button @click="activeTab = 'appointments'" 
                                :class="activeTab === 'appointments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.appointments') }}
                        </button>
                        <button @click="activeTab = 'prescriptions'" 
                                :class="activeTab === 'prescriptions' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.prescriptions') }}
                        </button>
                        <button @click="activeTab = 'billings'" 
                                :class="activeTab === 'billings' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.billings') }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.address') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->address ? "$patient->address, $patient->city, $patient->state $patient->zip_code" : '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.height_weight') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ($patient->height_cm ?? '—') . ' cm / ' . ($patient->weight_kg ?? '—') . ' kg' }}</p>
                            </div>
                        </div>

                        @if($patient->allergies && count($patient->allergies))
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('file.allergies') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($patient->allergies as $allergy)
                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 text-xs font-medium rounded-full">
                                            {{ $allergy }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('file.emergency_contact') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.name') }}</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ $patient->emergency_contact_name ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.relationship') }}</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ $patient->emergency_contact_relationship ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.phone') }}</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ $patient->emergency_contact_phone ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.email') }}</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ $patient->emergency_contact_email ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Tab -->
                    <div x-show="activeTab === 'appointments'" x-cloak>
                        @if($patient->appointments->isNotEmpty())
                            <!-- Your appointments list here -->
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_appointments_found') }}</p>
                        @endif
                    </div>

                    <!-- Prescriptions Tab -->
                    <div x-show="activeTab === 'prescriptions'" x-cloak>
                        @if($patient->prescriptions && $patient->prescriptions->isNotEmpty())
                            <!-- Your prescriptions list here -->
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_prescriptions_found') }}</p>
                        @endif
                    </div>

                    <!-- Billings Tab -->
                    <div x-show="activeTab === 'billings'" x-cloak>
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.invoice_number') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.invoice_date') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.total') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.paid') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.balance_due') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.status') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#docapp-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('patients.billing.datatable', $patient) }}',
        order: [[1, 'desc']],
        lengthChange: false,
        pageLength: 10, 
        columnDefs: [
            { orderable: false, targets: [0, 1, 2, 3, 4, 5, 6] },
            { searchable: false, targets: [2, 3, 4, 5, 6] }
        ],
        columns: [
            { data: 'invoice_number' },
            { data: 'invoice_date' },
            { data: 'total', className: 'text-right font-medium' },
            { data: 'paid_amount', className: 'text-right font-medium' },
            { data: 'balance_due', className: 'text-right font-medium' },
            { data: 'status_html' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-right whitespace-nowrap' }
        ],
        language: {
            search: "",
            searchPlaceholder: "{{ __('file.search_invoices') }}",
            emptyTable: "{{ __('file.no_billings_found') }}",
            processing: "{{ __('file.processing') }}",
            zeroRecords: "{{ __('file.no_billings_found') }}"
        }
    });
});
</script>
@endpush
@endsection