<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $clinic_name }}</title>

        
        <link rel="icon" 
      href="{{ $clinic_logo ?? asset('images/default-logo.png') }}" 
      type="image/png">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
        :root {
            --primary: {{ $primary_color ?? '#1e40af' }};
            --primary-light: {{ adjustBrightness($primary_color ?? '#1e40af', 30) }};
            --primary-dark: {{ adjustBrightness($primary_color ?? '#1e40af', -30) }};
        }

        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        @keyframes gradient-zoom {
            0% { background-size: 300% 300%; }
            50% { background-size: 500% 500%; }
            100% { background-size: 300% 300%; }
        }


        .animated-gradient {
            background: linear-gradient(-45deg, 
                #4b7d93, 
                #6b9ccd, 
                #4fa2a1, 
                {{ adjustBrightness('#2E3447'?? '#1e40af', 60) }}
            );
            background-size: 400% 400%;
            animation: gradient-zoom 4s ease-in-out infinite;
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