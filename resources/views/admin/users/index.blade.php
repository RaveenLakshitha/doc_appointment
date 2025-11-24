{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', __('file.user_management'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ __('file.user_management') }}</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-600">
            {{ __('file.add_new_user') }}
        </a>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="{{ __('file.search_placeholder') }}"
               class="border rounded px-3 py-2 w-full sm:w-64">
        <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded ml-2">
            {{ __('file.search') }}
        </button>
    </form>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.phone') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.roles') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('file.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? __('file.active') : __('file.inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                {{ __('file.edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('{{ __('file.soft_delete_confirm') }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    {{ __('file.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">{{ __('file.no_users_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection