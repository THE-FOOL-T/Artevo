<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users with the Collector role. Registered as the
 * 'collector' alias in bootstrap/app.php.
 */
class CollectorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isCollector()) {
            abort(403, 'This area is restricted to collectors.');
        }

        return $next($request);
    }
}
