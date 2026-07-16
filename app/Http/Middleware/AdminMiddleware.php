<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users with the Administrator role. Registered as
 * the 'admin' alias in bootstrap/app.php — see routes/web.php's admin
 * group for usage. Never relies on the frontend hiding a link; every
 * admin route is protected here regardless of how it was reached.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'This area is restricted to administrators.');
        }

        return $next($request);
    }
}
