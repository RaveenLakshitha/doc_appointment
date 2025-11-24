@extends('layouts.app')
@section('title', __('file.create_department'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            {{ __('file.create_department') }}
        </h1>

        <form action="{{ route('departments.store') }}" method="POST">
            @csrf

            <!-- Department Name -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.department_name') }}
                </label>
                <input type="text" name="name" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white"
                       placeholder="e.g. Cardiology">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Head of Department -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.head_of_department') }}
                </label>
                <select name="head_doctor_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('file.select_doctor') }}</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                    @endforeach
                </select>
                @error('head_doctor_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Location -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.location') }}
                </label>
                <input type="text" name="location"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white"
                       placeholder="e.g. Building A, Floor 3">
                @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Status -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.status') }}
                </label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center">
                        <input type="radio" name="status" value="1" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.active') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="status" value="0" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.inactive') }}</span>
                    </label>
                </div>
            </div>

            <!-- Contact Email -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.contact_email') }}
                </label>
                <input type="email" name="email"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white"
                       placeholder="department@clinic.com">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Contact Phone -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.contact_phone') }}
                </label>
                <input type="text" name="phone"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white"
                       placeholder="(555) 123-4567">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('file.description') }}
                </label>
                <textarea name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white"
                          placeholder="{{ __('file.enter_description') }}"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="px-5 py-2 bg-gray-900 dark:bg-gray-700 text-white font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.create_department') }}
                </button>
                <a href="{{ route('departments.index') }}"
                   class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection