<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
        :root {
            --primary: {{ $primary_color ?? '#1e40af' }};
            --primary-light: {{ adjustBrightness($primary_color ?? '#1e40af', 30) }};
            --primary-dark: {{ adjustBrightness($primary_color ?? '#1e40af', -30) }};
        }

        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        @keyframes gradient {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient {
            background: linear-gradient(-45deg, 
                var(--primary-dark), 
                var(--primary), 
                var(--primary-light), 
                {{ adjustBrightness($primary_color ?? '#1e40af', 60) }}
            );
            background-size: 400% 400%;
            animation: gradient 12s ease infinite;
        }

        .btn-primary {
            background: var(--primary);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 640px) {
            .mobile-card {
                border-radius: 24px 24px 0 0;
                min-height: 75vh;
            }
        }
    </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col justify-end sm:justify-center items-center animated-gradient px-4 sm:px-6 py-8">

        <div class="mb-10 text-center">
            <div class="inline-block bg-white/95 backdrop-blur-lg rounded-3xl p-5 ring-4 ring-white/20 mb-6">
                @if($clinic_logo ?? false)
                    <img src="{{ $clinic_logo }}"
                         alt="{{ $clinic_name ?? 'Clinic' }} Logo"
                         class="w-20 h-20 sm:w-24 sm:h-24 object-contain rounded-xl">
                @else
                    <!-- Fallback: Initials inside gradient circle -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-[var(--primary)] to-[var(--primary-dark)] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-3xl sm:text-4xl font-bold text-white">
                            {{ Str::substr($clinic_name ?? config('app.name'), 0, 2)->upper() }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

            <!-- Card Container -->
            <div class="w-full max-w-md mobile-card">
                <div class="glass-effect shadow-2xl sm:rounded-2xl px-6 py-8 sm:px-8 sm:py-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
        
    @stack('scripts')
    
    @php
        function adjustBrightness($hex, $percent) {
            $hex = ltrim($hex, '#');
            $rgb = array_map('hexdec', str_split($hex, 2));
            foreach ($rgb as &$value) {
                $value = max(0, min(255, $value + ($value * $percent / 100)));
            }
            return '#' . sprintf('%02x%02x%02x', ...$rgb);
        }
    @endphp
    </body>
</html>