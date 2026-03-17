<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorTestController extends Controller
{
    public function unauthorized()
    {
        abort(401, 'Unauthorized – please log in');
    }

    public function paymentRequired()
    {
        abort(402, 'Payment Required – upgrade your plan');
    }

    public function forbidden()
    {
        abort(403, 'Forbidden – insufficient permissions');
    }

    public function notFound()
    {
        abort(404);
    }

    public function pageExpired()
    {
        // Laravel throws 419 on CSRF mismatch → easiest way is abort()
        abort(419, 'Page Expired – CSRF token mismatch');
    }

    public function tooManyRequests()
    {
        // For demo purposes — real 429 usually comes from throttle middleware
        abort(429, 'Too Many Requests – slow down');
    }

    public function serverError()
    {
        // Real 500 = uncaught exception
        throw new \Exception('Intentional server error for testing');
        // or: 1 / 0;
        // or: abort(500);
    }

    public function maintenance()
    {
        // Simulate maintenance mode
        Artisan::call('down', ['--secret' => 'testing-secret-123']);

        // But since we're already in a request, just abort
        abort(503, 'Service Unavailable – maintenance mode');
    }

    // Bonus: flexible testing
    public function any($code = null)
    {
        $code = (int) ($code ?? 418); // I'm a teapot as default :)

        if ($code === 503) {
            return $this->maintenance();
        }

        if ($code === 500) {
            return $this->serverError();
        }

        if (in_array($code, [401, 402, 403, 404, 419, 429])) {
            abort($code);
        }

        // Fallback for other codes
        abort($code, "Testing HTTP {$code}");
    }
}