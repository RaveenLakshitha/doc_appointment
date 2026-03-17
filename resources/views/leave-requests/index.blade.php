@extends('layouts.app')

@section('title', __('file.leave_requests'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.leave_requests') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_leave_requests') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <div class="relative">
                    <button type="button" id="filter-toggle"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('file.filters') }}
                        <span id="filter-count"
                            class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                @can('leave-requests.create')
                    <button onclick="openCreateDrawer()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">{{ __('file.submit_leave_request') }}</span>
                        <span class="sm:hidden">Add</span>
                    </button>
                @endcan
            </div>
        </div>

        <div id="filter-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="filter-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.filters') }}</h3>
                <button type="button" id="close-drawer"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(85vh-140px)] md:max-h-[calc(100vh-140px)]">
                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee') }}</label>
                        <select id="filter-employee"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_employees') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select id="filter-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_statuses') }}</option>
                            <option value="pending">{{ __('file.pending') }}</option>
                            <option value="approved">{{ __('file.approved') }}</option>
                            <option value="rejected">{{ __('file.rejected') }}</option>
                            <option value="cancelled">{{ __('file.cancelled') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div
                class="bottom-0 left-0 right-0 flex gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="clear-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ __('file.clear') }}
                </button>
                <button type="button" id="apply-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    {{ __('file.apply') }}
                </button>
            </div>
        </div>

        <!-- Bulk Delete Form -->
        <div id="bulk-delete-form" class="hidden mb-6">
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.leave_requests_selected') }}
                </span>
                <button type="button" onclick="bulkDelete()"
                    class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </div>
        </div>

        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.employee') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.leave_type') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.dates') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.days') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop no-export">
                                {{ __('file.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeCreateDrawer()"></div>
        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.submit_leave_request') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_leave_request') }}</p>
                </div>
                <button onclick="closeCreateDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-6 text-sm">
                <form id="create-form" class="space-y-6">
                    @csrf
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee') }}</label>
                        <select name="employee_id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_employee') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.leave_type') }}</label>
                        <select name="leave_type_id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_leave_type') }}</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.start_date') }}</label>
                            <input type="date" name="start_date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.end_date') }}</label>
                            <input type="date" name="end_date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reason') }}</label>
                        <textarea name="reason" rows="4" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="create-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.submit_request') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeEditDrawer()"></div>
        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_leave_request') }}</p>
                </div>
                <button onclick="closeEditDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-6 text-sm">
                <form id="edit-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee') }}</label>
                        <select name="employee_id" id="edit-employee-id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_employee') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.leave_type') }}</label>
                        <select name="leave_type_id" id="edit-leave-type-id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_leave_type') }}</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.start_date') }}</label>
                            <input type="date" name="start_date" id="edit-start-date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.end_date') }}</label>
                            <input type="date" name="end_date" id="edit-end-date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reason') }}</label>
                        <textarea name="reason" id="edit-reason" rows="4" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="status" id="edit-status" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="pending">{{ __('file.pending') }}</option>
                            <option value="approved">{{ __('file.approved') }}</option>
                            <option value="rejected">{{ __('file.rejected') }}</option>
                            <option value="cancelled">{{ __('file.cancelled') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="edit-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="view-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeViewDrawer()"></div>
        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="view-drawer-title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.leave_request_details') }}</p>
                </div>
                <button onclick="closeViewDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-6 text-sm">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee') }}</label>
                            <div class="mt-1 text-gray-900 dark:text-white" id="view-employee"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.leave_type') }}</label>
                            <div class="mt-1 text-gray-900 dark:text-white" id="view-leave-type"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.dates') }}</label>
                            <div class="mt-1 text-gray-900 dark:text-white" id="view-dates"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.days') }}</label>
                            <div class="mt-1 text-gray-900 dark:text-white" id="view-days"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                            <div class="mt-1" id="view-status"></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reason') }}</h4>
                        <div class="mt-1 whitespace-pre-wrap text-gray-900 dark:text-white" id="view-reason"></div>
                    </div>
                </div>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeViewDrawer()"
                    class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterToggle = document.getElementById('filter-toggle');
                const filterDrawer = document.getElementById('filter-drawer');
                const filterBackdrop = document.getElementById('filter-backdrop');
                const closeDrawer = document.getElementById('close-drawer');
                const filterCount = document.getElementById('filter-count');
                const filterEmployee = document.getElementById('filter-employee');
                const filterStatus = document.getElementById('filter-status');

                function openFilterDrawer() {
                    filterBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        filterBackdrop.classList.add('opacity-100');
                        filterBackdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) filterDrawer.classList.remove('md:translate-x-full');
                        else filterDrawer.classList.remove('translate-y-full');
                    }, 10);
                }

                function closeFilterDrawer() {
                    filterBackdrop.classList.remove('opacity-100');
                    filterBackdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) filterDrawer.classList.add('md:translate-x-full');
                    else filterDrawer.classList.add('translate-y-full');
                    setTimeout(() => {
                        filterBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                filterToggle.addEventListener('click', (e) => { e.stopPropagation(); openFilterDrawer(); });
                closeDrawer.addEventListener('click', closeFilterDrawer);
                filterBackdrop.addEventListener('click', closeFilterDrawer);
                filterDrawer.addEventListener('click', (e) => e.stopPropagation());
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !filterBackdrop.classList.contains('hidden')) closeFilterDrawer(); });

                function updateFilterCount() {
                    const count = [filterEmployee.value, filterStatus.value].filter(Boolean).length;
                    if (count > 0) {
                        filterCount.textContent = count;
                        filterCount.classList.remove('hidden');
                    } else {
                        filterCount.classList.add('hidden');
                    }
                }

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("leave-requests.datatable") }}',
                        data: function (d) {
                            d.employee_id = filterEmployee.value;
                            d.status = filterStatus.value;
                        }
                    },
                    order: [[1, 'desc']],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center',
                            orderable: false
                        },
                        { data: 'employee_name', className: 'text-left' },
                        { data: 'leave_type', className: 'text-left' },
                        { data: 'dates', className: 'text-left' },
                        { data: 'days', className: 'text-center' },
                        { data: 'status_html', className: 'text-center' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            width: '150px',
                            render: (data, type, row) => `
                            <div class="flex items-center justify-end gap-2">
                                <button onclick='openViewDrawer(${JSON.stringify(row)})'
                                    class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                    title="{{ __('file.view') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button onclick='openEditDrawer(${JSON.stringify(row)})'
                                    class="p-1.5 sm:p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors"
                                    title="{{ __('file.edit') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                ${row.can_approve ? `
                                <button onclick="approveRequest(${row.id})"
                                    class="p-1.5 sm:p-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors"
                                    title="{{ __('file.approve') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>` : ''}
                                ${row.can_reject ? `
                                <button onclick="rejectRequest(${row.id})"
                                    class="p-1.5 sm:p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                                    title="{{ __('file.reject') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>` : ''}
                                <button onclick="deleteRequest(${row.id})"
                                    class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                    title="{{ __('file.delete') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        `
                        }
                    ],
                    layout: {
                        topStart: { buttons: ['pageLength', { extend: 'collection', text: '{{ __('file.Export') }}', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] }] },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_leave_requests') }}",
                        emptyTable: "{{ __('file.no_leave_requests_found') }}"
                    }
                });

                document.getElementById('apply-filters').addEventListener('click', () => { table.draw(); updateFilterCount(); closeFilterDrawer(); });
                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterEmployee.value = '';
                    filterStatus.value = '';
                    table.draw(); updateFilterCount();
                });

                [filterEmployee, filterStatus].forEach(el => el.addEventListener('change', updateFilterCount));
                updateFilterCount();

                let bodyScrollPos = 0;

                function lockBody() {
                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';
                }

                function unlockBody() {
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                }

                window.openCreateDrawer = function () {
                    document.getElementById('create-form').reset();
                    lockBody();
                    document.getElementById('create-drawer').classList.remove('hidden');
                };

                window.closeCreateDrawer = function () {
                    document.getElementById('create-drawer').classList.add('hidden');
                    unlockBody();
                };

                window.openEditDrawer = function (row) {
                    document.getElementById('edit-id').value = row.id;
                    document.getElementById('edit-employee-id').value = row.employee_id;
                    document.getElementById('edit-leave-type-id').value = row.leave_type_id;
                    document.getElementById('edit-start-date').value = row.start_date;
                    document.getElementById('edit-end-date').value = row.end_date;
                    document.getElementById('edit-reason').value = row.reason;
                    document.getElementById('edit-status').value = row.status || 'pending';
                    document.getElementById('edit-drawer-title').textContent = row.employee_name + ' - ' + row.leave_type;
                    lockBody();
                    document.getElementById('edit-drawer').classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    document.getElementById('edit-drawer').classList.add('hidden');
                    unlockBody();
                };

                window.openViewDrawer = function (row) {
                    document.getElementById('view-drawer-title').textContent = row.employee_name + ' - ' + row.leave_type;
                    document.getElementById('view-employee').textContent = row.employee_name;
                    document.getElementById('view-leave-type').textContent = row.leave_type;
                    document.getElementById('view-dates').textContent = row.dates;
                    document.getElementById('view-days').textContent = row.days;
                    document.getElementById('view-status').innerHTML = row.status_html;
                    document.getElementById('view-reason').textContent = row.reason || '—';
                    lockBody();
                    document.getElementById('view-drawer').classList.remove('hidden');
                };

                window.closeViewDrawer = function () {
                    document.getElementById('view-drawer').classList.add('hidden');
                    unlockBody();
                };

                document.getElementById('create-form').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    try {
                        const response = await fetch('{{ route("leave-requests.store") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            table.draw(false);
                            closeCreateDrawer();
                            showNotification('{{ __('file.request_submitted') }}', 'success');
                        } else {
                            showNotification(data.message || '{{ __('file.failed_to_submit') }}', 'error');
                        }
                    } catch (err) {
                        showNotification('Error submitting request', 'error');
                    }
                });

                document.getElementById('edit-form').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    try {
                        const response = await fetch(`{{ url('leave-requests') }}/${id}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            table.draw(false);
                            closeEditDrawer();
                            showNotification('{{ __('file.request_updated') }}', 'success');
                        } else {
                            showNotification(data.message || '{{ __('file.failed_to_update') }}', 'error');
                        }
                    } catch (err) {
                        showNotification('Error updating request', 'error');
                    }
                });

                window.approveRequest = async function (id) {
                    if (!confirm('{{ __("file.confirm_approve_request") }}')) return;
                    try {
                        const response = await fetch(`{{ url('leave-requests') }}/${id}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server Error:', errorText);
                            throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                        }

                        const data = await response.json();
                        if (data.success) {
                            table.draw(false);
                            showNotification('{{ __("file.request_approved") }}', 'success');
                        } else {
                            showNotification(data.message || '{{ __("file.failed_to_approve") }}', 'error');
                        }
                    } catch (err) {
                        console.error('Approve Error:', err);
                        showNotification('{{ __("file.error") }}: ' + err.message, 'error');
                    }
                };

                window.rejectRequest = async function (id) {
                    const reason = prompt('{{ __("file.enter_rejection_reason") }}');
                    if (reason === null) return;
                    try {
                        const response = await fetch(`{{ url('leave-requests') }}/${id}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ reason })
                        });
                        const data = await response.json();
                        if (data.success) {
                            table.draw(false);
                            showNotification('{{ __("file.request_rejected") }}', 'success');
                        } else {
                            showNotification(data.message || '{{ __("file.failed_to_reject") }}', 'error');
                        }
                    } catch (err) {
                        showNotification('{{ __("file.error_deleting") }}', 'error');
                    }
                };

                // Bulk delete logic
                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const count = $('.row-checkbox:checked').length;
                    document.getElementById('bulk-delete-form').classList.toggle('hidden', count === 0);
                    document.getElementById('selected-count').textContent = count;
                    document.getElementById('bulk-ids').value = $('.row-checkbox:checked').map(function () { return this.value; }).get().join(',');
                }

                window.deleteRequest = async function (id) {
                    if (!confirm('{{ __("file.confirm_delete") }}')) return;
                    try {
                        const response = await fetch(`{{ url('leave-requests') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            table.draw(false);
                            showNotification('{{ __("file.record_deleted") }}', 'success');
                        } else {
                            showNotification(data.message || '{{ __("file.failed_to_delete") }}', 'error');
                        }
                    } catch (err) {
                        showNotification('{{ __("file.error_deleting") }}', 'error');
                    }
                };

                window.bulkDelete = async function () {
                    const ids = document.getElementById('bulk-ids').value;
                    if (!ids || !confirm('{{ __("file.confirm_bulk_delete") }}')) return;

                    try {
                        const response = await fetch('{{ route("leave-requests.bulkDelete") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ ids })
                        });
                        const data = await response.json();
                        if (data.success) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            if (typeof updateBulkDelete === 'function') updateBulkDelete();
                            if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                        } else {
                            showNotification(data.message || '{{ __("file.failed_to_delete") }}', 'error');
                        }
                    } catch (err) {
                        showNotification('{{ __("file.error_deleting") }}', 'error');
                    }
                };

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!document.getElementById('view-drawer').classList.contains('hidden')) closeViewDrawer();
                        if (!document.getElementById('create-drawer').classList.contains('hidden')) closeCreateDrawer();
                        if (!document.getElementById('edit-drawer').classList.contains('hidden')) closeEditDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection