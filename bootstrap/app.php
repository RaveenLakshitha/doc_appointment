<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetAppLocale;
use App\Services\AppointmentService; // ← add this

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetAppLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect 419 (CSRF token mismatch) back to login with a friendly message
        $exceptions->render(function (Illuminate\Session\TokenMismatchException $e, Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh and try again.'], 419);
            }
            return redirect()->route('login')
                ->with('status', __('file.session_expired_login_again') ?? 'Your session expired. Please log in again.');
        });

        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, Illuminate\Http\Request $request) {
            $statusCode = $e->getStatusCode();

            // Admin-specific error pages
            if ($request->is('admin*') && view()->exists("errors.admin.{$statusCode}")) {
                return response()->view("errors.admin.{$statusCode}", ['exception' => $e], $statusCode);
            }

            // General custom error pages (fallback)
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", ['exception' => $e], $statusCode);
            }
        });
    })

    // ────────────────────────────────────────────────
    // Add this section - recommended for services
    ->withSingletons([
        AppointmentService::class => AppointmentService::class,
        WhatsAppService::class => WhatsAppService::class,
    ])

    ->create();