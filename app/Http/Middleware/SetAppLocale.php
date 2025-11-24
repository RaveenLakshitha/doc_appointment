<?php
// app/Http/Middleware/SetAppLocale.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SetAppLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale') ?? config('app.locale', 'en');

        if (in_array($locale, ['en', 'es'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}