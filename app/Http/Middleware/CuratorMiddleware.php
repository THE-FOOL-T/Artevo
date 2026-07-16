<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users with the Curator role. Registered as the
 * 'curator' alias in bootstrap/app.php.
 */
class CuratorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isCurator()) {
            abort(403, 'This area is restricted to curators.');
        }

        return $next($request);
    }
}
