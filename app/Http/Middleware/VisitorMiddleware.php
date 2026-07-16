<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to users with the Visitor role — used for actions
 * that only make sense for someone who hasn't upgraded yet, such as
 * self-service Collector upgrade (see RoleUpgradeController). Registered
 * as the 'visitor' alias in bootstrap/app.php.
 */
class VisitorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isVisitor()) {
            abort(403, 'This action is only available to visitor accounts.');
        }

        return $next($request);
    }
}
