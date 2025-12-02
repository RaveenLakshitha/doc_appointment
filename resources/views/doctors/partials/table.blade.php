<div id="doctors-container">

    <div class="space-y-4 sm:hidden">
        @forelse($doctors as $doctor)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" value="{{ $doctor->id }}" class="row-checkbox w-5 h-5 rounded text-blue-600">
                        <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                            @if($doctor->profile_photo)
                                <img src="{{ Storage::url($doctor->profile_photo) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $doctor->full_name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $doctor->license_number }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $doctor->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="text-sm space-y-1 mb-3">
                    <div class="flex justify-between"><span class="text-gray-500">Specialty:</span> <span class="font-medium">{{ $doctor->primarySpecialization?->name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Department:</span> <span class="font-medium">{{ $doctor->department?->name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Appointments:</span> <span class="font-medium">{{ $doctor->appointments_count }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Phone:</span> <span class="font-medium">{{ $doctor->phone ?? '—' }}</span></div>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('doctors.show', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('doctors.edit', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this doctor?')" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center py-8 text-gray-500">No doctors found</p>
        @endforelse
    </div>

    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="w-4 h-4 rounded"></th>
                        <th class="px-4 py-3 w-12"></th>
                        <th class="px-4 py-3 text-left">
                            <a href="#" data-sort="name" class="flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Name
                                <span class="sort-icon">
                                    @if(request('sort') === 'name')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <a href="#" data-sort="specialty" class="flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Specialty
                                <span class="sort-icon">
                                    @if(request('sort') === 'specialty')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <a href="#" data-sort="department" class="flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Department
                                <span class="sort-icon">
                                    @if(request('sort') === 'department')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <a href="#" data-sort="is_active" class="flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Status
                                <span class="sort-icon">
                                    @if(request('sort') === 'is_active')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-center">
                            <a href="#" data-sort="appointments_count" class="flex items-center justify-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Appts
                                <span class="sort-icon">
                                    @if(request('sort') === 'appointments_count')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <a href="#" data-sort="phone" class="flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Phone
                                <span class="sort-icon">
                                    @if(request('sort') === 'phone')
                                        @if(request('direction') === 'asc')
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-900 dark:text-white rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($doctors as $doctor)
                        <tr class="h-16 hover:bg-gray-700 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3"><input type="checkbox" value="{{ $doctor->id }}" class="row-checkbox w-4 h-4 rounded"></td>
                            <td class="px-4 py-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800">
                                    @if($doctor->profile_photo)
                                        <img src="{{ Storage::url($doctor->profile_photo) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $doctor->full_name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                    {{ $doctor->primarySpecialization?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $doctor->department?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $doctor->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">{{ $doctor->appointments_count }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $doctor->phone }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this doctor?')" class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg font-medium">No doctors found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $doctors->appends(request()->query())->links() }}
    </div>

</div>