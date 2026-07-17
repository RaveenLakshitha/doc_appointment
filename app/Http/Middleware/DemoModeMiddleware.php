<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not in demo mode, proceed normally without any interference
        if (!config('app.demo_mode')) {
            return $next($request);
        }

        // If in demo mode, prevent modification requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            // Whitelist some essential routes that need to work even in demo mode
            $allowedRoutes = [
                'login',
                'logout',
                'password.email',
                'password.update',
                'password.reset',
            ];

            if (!$request->routeIs($allowedRoutes) && !$request->is($allowedRoutes)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => 'This action is disabled in Demo Mode.',
                        'status' => 'error'
                    ], 403);
                }

                // Many apps use 'error' or 'message' or 'status' for flash messages. We set both 'error' and 'status'.
                return redirect()->back()->with('error', 'This action is disabled in Demo Mode.')
                                         ->with('status', 'This action is disabled in Demo Mode.');
            }
        }

        return $next($request);
    }
}
