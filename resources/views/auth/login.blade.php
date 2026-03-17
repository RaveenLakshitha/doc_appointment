<x-guest-layout>

    <!-- Mobile Header (Visible only on small screens since left side is hidden) -->
    <div class="lg:hidden flex flex-col items-center mb-8 text-center pt-8">
        <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center p-3 shadow-md mb-4 text-sky-600">
            @if($clinic_logo ?? false)
                <img src="{{ $clinic_logo }}" alt="{{ $clinic_name ?? __('messages.Medical Center') }} Logo" class="w-12 h-12 object-contain">
            @else
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-slate-800">
            {{ $clinic_name ?? __('messages.Medical Center') }}
        </h1>
    </div>

    <!-- Title & Greeting -->
    <div class="mb-10 lg:text-left text-center">
        <h2 class="text-3xl font-bold text-slate-800 mb-2">{{ __('messages.Welcome Back') }}</h2>
        <p class="text-slate-500 text-base">{{ __('messages.Please enter your credentials to access the system.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 p-4 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-700">{{ __('messages.Email Address') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <!-- Manual input to handle icons easily -->
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="doctor@medicalcenter.com"
                    class="block w-full pl-11 pr-4 py-3.5 text-base text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all duration-200 shadow-sm outline-none" 
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-700">{{ __('messages.Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500 transition-colors">
                        {{ __('messages.Forgot password?') }}
                    </a>
                @endif
            </div>
            
            <div x-data="{ show: false }" class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                
                <input 
                    id="password"
                    :type="show ? 'text' : 'password'"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="********"
                    class="block w-full pl-11 pr-12 py-3.5 text-base text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all duration-200 shadow-sm outline-none"
                />

                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-sky-600 focus:outline-none transition-colors"
                >
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="peer w-5 h-5 border-2 border-slate-300 rounded text-sky-600 focus:ring-sky-500/30 focus:ring-offset-0 transition-all cursor-pointer appearance-none checked:bg-sky-600 checked:border-sky-600" 
                    >
                    <svg class="absolute w-3.5 h-3.5 text-white left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="ml-3 text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">{{ __('messages.Remember me this device') }}</span>
            </label>
        </div>

        <!-- Login Button -->
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 text-base font-semibold text-white rounded-xl bg-sky-600 hover:bg-sky-500 focus:ring-4 focus:ring-sky-500/30 transition-all duration-200 shadow-lg shadow-sky-600/20 active:scale-[0.98]">
                <span>{{ __('messages.Sign in to Dashboard') }}</span>
                <svg class="ml-2 w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
        
    </form>
    <!-- Auto-refresh CSRF token to prevent 419 errors on stale tabs -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Refresh CSRF token every 15 minutes to prevent 419 Page Expired
            setInterval(function() {
                fetch('/sanctum/csrf-cookie').catch(() => {});
            }, 15 * 60 * 1000);
        });
    </script>
</x-guest-layout>
