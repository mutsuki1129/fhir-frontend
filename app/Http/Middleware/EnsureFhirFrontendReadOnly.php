<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFhirFrontendReadOnly
{
    public const MESSAGE = 'FHIR frontend is running in read-only mode. Clinical write actions are disabled.';

    public function handle(Request $request, Closure $next): Response
    {
        if (! filter_var(config('fhir.frontend_read_only', true), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => self::MESSAGE,
            ], 403);
        }

        return response(self::MESSAGE, 403);
    }
}
