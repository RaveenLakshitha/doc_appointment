@extends('layouts.app')

@section('title', __('file.edit_user') ?? 'Edit User')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <!-- Breadcrumb & Page Header -->
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('users.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.users') ?? 'Users' }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">
                    {{ __('file.edit_user') ?? 'Edit User' }}
                </span>
            </div>

            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.edit_user_details') ?? 'Edit User Details' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.update_user_record') ?? 'Update user account information and role' }}
            </p>
        </div>

        <!-- Main Form -->
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <div class="p-6 sm:p-8 space-y-8">

                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">
                            {{ __('file.basic_information') ?? 'Basic Information' }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.full_name') ?? 'Full Name' }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all"
                                    placeholder="John Doe">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.email') ?? 'Email' }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all"
                                    placeholder="user@example.com">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.phone') ?? 'Phone Number' }} <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                                    minlength="7" maxlength="15"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all"
                                    placeholder="+1712345678">
                                @error('phone')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Credentials -->
                    <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white pb-3">
                            {{ __('file.account_credentials') ?? 'Account Credentials' }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            {{ __('file.password_optional_note') ?? 'Leave password fields blank if you do not want to change the password.' }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.new_password') ?? 'New Password' }}
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" minlength="8"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all pr-10"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password', 'eyePassword')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                                        <svg id="eyePassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.confirm_new_password') ?? 'Confirm New Password' }}
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all pr-10"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePassword('password_confirmation', 'eyeConfirm')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                                        <svg id="eyeConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white pb-3">
                            {{ __('file.user_role') ?? 'User Role' }}
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($roles as $role)
                                <label
                                    class="relative flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <input type="radio" name="role" value="{{ $role->name }}" required
                                        class="h-5 w-5 text-gray-900 focus:ring-gray-900 border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                                        {{ ($currentRole ?? old('role')) === $role->name ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                    </span>
                                    <span
                                        class="absolute inset-0 rounded-lg pointer-events-none border-2 border-transparent group-has-[:checked]:border-gray-900 dark:group-has-[:checked]:border-gray-300 transition-all"></span>
                                </label>
                            @endforeach
                        </div>

                        @error('role')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status Toggle -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <div class="relative inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ? 1 : null) ? 'checked' : '' }} class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer 
                                                        peer-focus:outline-none peer-focus:ring-4 
                                                        peer-focus:ring-gray-300 dark:peer-focus:ring-gray-700 
                                                        dark:bg-gray-700 
                                                        peer-checked:bg-gray-900 
                                                        peer-checked:after:translate-x-5 
                                                        rtl:peer-checked:after:-translate-x-5 
                                                        after:content-[''] after:absolute after:top-[2px] after:start-[2px] 
                                                        after:bg-white after:border-gray-300 after:border 
                                                        after:rounded-full after:h-5 after:w-5 
                                                        after:transition-all dark:after:border-gray-600 
                                                        dark:peer-checked:after:bg-white"></div>
                            </div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ __('file.active') ?? 'Active' }}
                            </span>
                        </label>
                        @error('is_active')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('file.update_user') ?? 'Update User' }}
                </button>

                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') ?? 'Cancel' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Password visibility toggle script (same as create) -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                        `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        `;
            }
        }
    </script>
@endsection