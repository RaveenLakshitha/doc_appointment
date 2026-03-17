@extends('layouts.app')

@section('title', __('file.rooms'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.rooms') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_room_records') }}
                </p>
            </div>
            <button onclick="openCreateDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_room') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('rooms.bulkDelete') }}"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.room_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.name') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.room_number') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.floor') }}
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.capacity') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.department') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeProfileDrawer()"></div>

        <div id="drawer-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.room_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.details') }}</label>
                        <div class="grid grid-cols-1 gap-3">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-department"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.room_number') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-room-number"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.floor') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-floor"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.capacity') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-capacity"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.facilities') }}</label>
                        <div class="text-gray-900 dark:text-white" id="drawer-facilities">—</div>
                    </div>

                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.status') }}
                        </h4>
                        <div id="drawer-status"></div>
                    </div>

                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.description') }}
                        </h4>
                        <div class="text-gray-900 dark:text-white" id="drawer-description"></div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="edit-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeEditDrawer()"></div>

        <div id="edit-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_room') }}</p>
                </div>
                <button onclick="closeEditDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="edit-form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }}</label>
                        <select name="department_id" id="edit-department" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_department') }}</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.room_number') }}</label>
                        <input type="text" name="room_number" id="edit-room-number" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.floor') }}</label>
                        <input type="text" name="floor" id="edit-floor"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.capacity') }}</label>
                        <input type="number" name="capacity" id="edit-capacity" min="1"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                            {{ __('file.facilities') }}
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            @php
                                $availableFacilities = [
                                    'wifi' => 'WiFi',
                                    'air_conditioning' => 'Air Conditioning',
                                    'television' => 'Television',
                                    'telephone' => 'Telephone',
                                    'wheelchair_accessible' => 'Wheelchair Accessible',
                                    'attached_bathroom' => 'Attached Bathroom',
                                    'oxygen_supply' => 'Oxygen Supply',
                                    'nurse_call_button' => 'Nurse Call Button',
                                ];
                            @endphp
                            @foreach($availableFacilities as $key => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="facilities[]" value="{{ $key }}"
                                        class="facility-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.status') }}</label>
                        <select name="is_active" id="edit-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="6"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
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

    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="create-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeCreateDrawer()"></div>

        <div id="create-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_room') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_room') }}</p>
                </div>
                <button onclick="closeCreateDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="create-form" class="space-y-5">
                    @csrf

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }}</label>
                        <select name="department_id" id="create-department" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_department') }}</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="create-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.room_number') }}</label>
                        <input type="text" name="room_number" id="create-room-number" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.floor') }}</label>
                        <input type="text" name="floor" id="create-floor"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.capacity') }}</label>
                        <input type="number" name="capacity" id="create-capacity" min="1"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                            {{ __('file.facilities') }}
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            @php
                                $availableFacilities = [
                                    'wifi' => 'WiFi',
                                    'air_conditioning' => 'Air Conditioning',
                                    'television' => 'Television',
                                    'telephone' => 'Telephone',
                                    'wheelchair_accessible' => 'Wheelchair Accessible',
                                    'attached_bathroom' => 'Attached Bathroom',
                                    'oxygen_supply' => 'Oxygen Supply',
                                    'nurse_call_button' => 'Nurse Call Button',
                                ];
                            @endphp
                            @foreach($availableFacilities as $key => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="facilities[]" value="{{ $key }}"
                                        class="create-facility-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.status') }}</label>
                        <select name="is_active" id="create-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.description') }}</label>
                        <textarea name="description" id="create-description" rows="6"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="create-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.create_room') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('rooms.datatable') }}',
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 7] },
                        { searchable: false, targets: [0, 4, 5, 6, 7] }
                    ],
                    columns: [
                        { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
                        { data: 'name', render: data => data || '-' },
                        { data: 'room_number', render: data => data || '—' },
                        { data: 'floor', render: data => data || '—' },
                        { data: 'capacity', className: 'text-center font-medium', render: data => data || '—' },
                        { data: 'department_name', render: data => data || '—' },
                        {
                            data: 'is_active', render: data => data
                                ? `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.active') }}</span>`
                                : `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ __('file.inactive') }}</span>`
                        },
                        {
                            data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                                        <div class="flex items-center justify-end gap-1">
                                            <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            ${row.delete_url ? `
                                                <form method="POST" action="${row.delete_url}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_room') }}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            ` : ''}
                                        </div>`
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection', text: "{{ __('file.Export') }}", className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium', buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                                        { extend: 'excel', text: 'Excel', filename: 'Rooms_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                                        { extend: 'csv', text: 'CSV', filename: 'Rooms_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'Rooms_{{ date("Y-m-d") }}', title: 'Room List', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
                                        { extend: 'print', text: "{{ __('file.print') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } }
                                    ]
                                }
                            ]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_rooms') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_rooms_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });
                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const count = $('.row-checkbox:checked').length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    $('#bulk-ids').val($('.row-checkbox:checked').map(function () { return this.value; }).get().join(','));
                }

                $('#bulk-delete-form form').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_delete_selected") }}')) return;

                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            if (typeof updateBulkDelete === 'function') updateBulkDelete();
                            if (typeof updateBulkDeleteUI === 'function') updateBulkDeleteUI();
                            if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                        },
                        error: function (xhr) {
                            showNotification(xhr.responseJSON?.message || 'Something went wrong', 'error');
                        }
                    });
                });

                const profileDrawer = document.getElementById('profile-drawer');
                const editDrawer = document.getElementById('edit-drawer');
                const createDrawer = document.getElementById('create-drawer');
                let bodyScrollPos = 0;

                window.openProfileDrawer = function (room) {
                    document.getElementById('drawer-name').textContent = room.name;
                    document.getElementById('drawer-department').textContent = room.department_name || '—';
                    document.getElementById('drawer-room-number').textContent = room.room_number || '—';
                    document.getElementById('drawer-floor').textContent = room.floor || '—';
                    document.getElementById('drawer-capacity').textContent = room.capacity || '—';
                    document.getElementById('drawer-facilities').textContent = room.facilities_list || '—';

                    const statusHtml = room.is_active
                        ? `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.active') }}</span>`
                        : `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ __('file.inactive') }}</span>`;
                    document.getElementById('drawer-status').innerHTML = statusHtml;

                    document.getElementById('drawer-description').textContent = room.description || '—';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    profileDrawer.classList.remove('hidden');
                };

                window.closeProfileDrawer = function () {
                    profileDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.openEditDrawer = function (room) {
                    document.getElementById('edit-id').value = room.id;
                    document.getElementById('edit-drawer-name').textContent = room.name || '';
                    document.getElementById('edit-department').value = room.department_id || '';
                    document.getElementById('edit-name').value = room.name || '';
                    document.getElementById('edit-room-number').value = room.room_number || '';
                    document.getElementById('edit-floor').value = room.floor || '';
                    document.getElementById('edit-capacity').value = room.capacity || '';
                    document.getElementById('edit-status').value = room.is_active ? 1 : 0;
                    document.getElementById('edit-description').value = room.description || '';

                    document.querySelectorAll('.facility-checkbox').forEach(cb => cb.checked = false);
                    if (room.facilities && Array.isArray(room.facilities)) {
                        room.facilities.forEach(fac => {
                            const checkbox = document.querySelector(`#edit-form input[name="facilities[]"][value="${fac}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    editDrawer.classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    editDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.openCreateDrawer = function () {
                    document.getElementById('create-form').reset();
                    document.querySelectorAll('.create-facility-checkbox').forEach(cb => cb.checked = false);
                    document.getElementById('create-status').value = 1;
                    document.getElementById('create-department').value = '';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    createDrawer.classList.remove('hidden');
                };

                window.closeCreateDrawer = function () {
                    createDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    fetch(`{{ route('rooms.update', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                            } else {
                                showNotification(data.message || 'Update failed', 'error');
                            }
                        })
                        .catch(() => showNotification('Failed to update room', 'error'));
                });

                document.getElementById('create-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('{{ route('rooms.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeCreateDrawer();
                            } else {
                                showNotification(data.message || 'Create failed', 'error');
                            }
                        })
                        .catch(() => showNotification('Failed to create room', 'error'));
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!profileDrawer.classList.contains('hidden')) closeProfileDrawer();
                        if (!editDrawer.classList.contains('hidden')) closeEditDrawer();
                        if (!createDrawer.classList.contains('hidden')) closeCreateDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection