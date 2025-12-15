{{-- resources/views/partials/navbar.blade.php --}}
<header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

        <div class="flex items-center">
            <button id="open-sidebar"
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 
                           dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 
                           focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                           dark:focus:ring-offset-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="flex items-center space-x-4">

            <div x-data="{ open: false }" x-init="open = false" class="relative">
                <button @click="open = !open"
                        class="flex items-center space-x-1 text-sm font-medium text-gray-700 dark:text-gray-300 
                               hover:text-primary dark:hover:text-primary-400 
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-cloak
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-36 origin-top-right bg-white dark:bg-gray-800 
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('English') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="es">
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Español') }}
                        </button>
                    </form>
                </div>
            </div>

            <button id="theme-toggle-navbar"
                    aria-label="Toggle dark mode"
                    class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 
                        dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 
                        focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                        dark:focus:ring-offset-gray-800 transition">
                <svg id="sun-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m8.485-11.485l-.707.707M5.636 18.364l-.707.707m12.021 0l-.707-.707M6.343 5.636l-.707-.707m12.728 12.728l-.707-.707M6.343 18.364l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                </svg>
                <svg id="moon-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <button id="fullscreen-toggle"
                aria-label="Toggle fullscreen"
                class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 
                    dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 
                    focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                    dark:focus:ring-offset-gray-800 transition">
                <svg id="enter-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                </svg>
                <svg id="exit-fullscreen-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-8v4m0-4h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0-4h-4m4 4l-5-5"/>
                </svg>
            </button>

            <div class="relative" x-data="{ open: false }" x-cloak x-init="open = false">
                <button id="user-menu-button"
                        @click="open = !open"
                        class="flex items-center space-x-3 text-sm font-medium text-gray-700 dark:text-gray-300 
                               hover:text-primary dark:hover:text-primary-400 
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-300 dark:ring-gray-600"
                         src="{{ auth()->user()?->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name) . '&background=6366f1&color=fff' }}"
                         alt="{{ auth()->user()?->name }}">
                    <span class="hidden sm:block">{{ auth()->user()?->name }}</span>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 origin-top-right bg-white dark:bg-gray-800 
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 
                              hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        {{ __('Profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
