@extends('layouts.app')
@section('title', __('file.create_department'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl">
         <!-- Breadcrumbs -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li>
                    <div class="flex items-center">
                        <a href="{{ route('departments.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            Departments
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-900 dark:text-white font-medium">Create</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('file.create_department') }}
            </h1>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-transparent rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf

                <div class="p-6 space-y-5">
                    <!-- Basic Information Section -->
                    <div class="pb-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                            Basic Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Department Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.department_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-gray-50"
                                       placeholder="e.g. Cardiology">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Head of Department -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.head_of_department') }}
                                </label>
                                <select name="head_doctor_id"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                    <option value="">{{ __('file.select_doctor') }}</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('head_doctor_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.status') }}
                                </label>
                                <div class="flex items-center gap-6 h-[34px]">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="1" checked 
                                               class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900 dark:border-gray-600 dark:focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.active') }}</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status" value="0" 
                                               class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900 dark:border-gray-600 dark:focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.inactive') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="pb-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                            Location Details
                        </h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.location') }}
                            </label>
                            <input type="text" name="location"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white"
                                   placeholder="e.g. Building A, Floor 3">
                            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="pb-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                            Contact Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Contact Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.contact_email') }}
                                </label>
                                <input type="email" name="email"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white"
                                       placeholder="department@clinic.com">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.contact_phone') }}
                                </label>
                                <input type="text" name="phone"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white"
                                       placeholder="(555) 123-4567">
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                            Additional Details
                        </h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.description') }}
                            </label>
                            <textarea name="description" rows="3"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white resize-none"
                                      placeholder="{{ __('file.enter_description') }}"></textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 sm:px-8 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <a href="{{ route('departments.index') }}"
                           class="inline-flex justify-center items-center px-6 py-3 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-medium rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>{{ __('file.cancel') }}
                        </a>
                       <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('file.create_department') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection