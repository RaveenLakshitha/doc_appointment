<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-100">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <h1 class="text-9xl font-bold text-red-600">404</h1>
                <p class="mt-4 text-3xl font-semibold text-gray-800">{{ __('file.error_404_title') }}</p>
                <p class="mt-2 text-lg text-gray-600">
                    {{ __('file.error_404_message') }}
                </p>
            </div>
            <div>
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    {{ auth()->check() ? __('file.go_to_dashboard') : __('file.go_to_login') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>