<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureControlledIngestionPrototypeEnabled
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(404);
        }

        if (! filter_var(config('fhir.controlled_ingestion_prototype.enabled', false), FILTER_VALIDATE_BOOLEAN)) {
            abort(404);
        }

        if (config('fhir.controlled_ingestion_prototype.mode') !== 'mock') {
            abort(404);
        }

        if (config('fhir.controlled_ingestion_prototype.allow_fhir_write') !== false) {
            abort(404);
        }

        return $next($request);
    }
}
