@extends('layouts.app')

@section('title', __('file.whatsapp_management') ?? 'WhatsApp Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.whatsapp_management') ?? 'WhatsApp Management' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_whatsapp_alerts_history') ?? 'Manage WhatsApp alert templates and view message history.' }}
            </p>
        </div>

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('history')" id="tab-history" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                            <i class="fas fa-history mr-2"></i> {{ __('file.message_history') ?? 'Message History' }}
                        </button>
                        <button onclick="switchTab('settings')" id="tab-settings" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                            <i class="fas fa-cog mr-2"></i> {{ __('file.settings') ?? 'Settings' }}
                        </button>
                    </nav>
                </div>

                <!-- History Section -->
                <div id="section-history" class="tab-section">
                    <div class="overflow-x-auto">
                        <table id="history-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.patient') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.phone') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.type') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.message') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('file.status') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Settings Section -->
                <div id="section-settings" class="tab-section hidden">
                    <form action="{{ route('admin.whatsapp.settings.update') }}" method="POST" class="space-y-8">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Appointment Assigned Alert -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                        {{ __('file.appointment_assigned_alert') ?? 'Appointment Assigned Alert' }}
                                    </h3>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="whatsapp_assign_enabled" value="1" class="sr-only peer" {{ $settings['assign_enabled'] ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('file.template') ?? 'Template' }}
                                        </label>
                                        <textarea name="whatsapp_assign_template" rows="4" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $settings['assign_template'] }}</textarea>
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ __('file.available_placeholders') ?? 'Available placeholders:' }} <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{patient_name}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{doctor_name}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{date}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{time}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{appointment_number}</code>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Appointment Rescheduled Alert -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                        {{ __('file.appointment_rescheduled_alert') ?? 'Appointment Rescheduled Alert' }}
                                    </h3>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="whatsapp_reschedule_enabled" value="1" class="sr-only peer" {{ $settings['reschedule_enabled'] ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('file.template') ?? 'Template' }}
                                        </label>
                                        <textarea name="whatsapp_reschedule_template" rows="4" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $settings['reschedule_template'] }}</textarea>
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ __('file.available_placeholders') ?? 'Available placeholders:' }} <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{patient_name}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{doctor_name}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{date}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{time}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{appointment_number}</code>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 pt-6">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('file.save_changes') ?? 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let historyTable;

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded!');
            return;
        }

        historyTable = $('#history-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.whatsapp.history.datatable') }}",
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'patient', name: 'patient' },
                { data: 'phone', name: 'phone' },
                { data: 'type', name: 'type' },
                { 
                    data: 'content', 
                    name: 'content',
                    render: function(data) {
                        return '<div class="max-w-xs truncate" title="' + data + '">' + data + '</div>';
                    }
                },
                { data: 'status', name: 'status' }
            ],
            order: [[0, 'desc']],
            language: {
                        info: "{{ __('file.showing_entries') ?? 'Showing _START_ to _END_ of _TOTAL_ entries' }}",
                        search: "{{ __('file.search') ?? 'Search:' }}",
                        lengthMenu: "{{ __('file.show_d_rows') ?? 'Show _MENU_ entries' }}",
                        paginate: {
                            first: "{{ __('file.first') ?? 'First' }}",
                            last: "{{ __('file.last') ?? 'Last' }}",
                            next: "{{ __('file.next') ?? 'Next' }}",
                            previous: "{{ __('file.previous') ?? 'Previous' }}"
                        },
                        buttons: {
                            pageLength: {
                                _: "{{ __('file.show_d_rows') }}",
                                '-1': "{{ __('file.show_all_rows') }}"
                            }
                        },
                url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });
    });

    window.switchTab = function(tab) {
        if (typeof $ === 'undefined') return;
        
        $('.tab-section').addClass('hidden');
        $('#section-' + tab).removeClass('hidden');

        // Update tab styles
        $('nav button').removeClass('border-indigo-500 text-indigo-600').addClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
        $('#tab-' + tab).addClass('border-indigo-500 text-indigo-600').removeClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');

        if (tab === 'history' && historyTable) {
            historyTable.ajax.reload();
        }
    };
</script>
@endpush

@endsection
