<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessModule
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (! $user || ! $user->company_id || ! $user->canAccessModule($module)) {
            abort(403, 'Vous n’êtes pas autorisé à accéder à ce module.');
        }

        return $next($request);
    }
}
