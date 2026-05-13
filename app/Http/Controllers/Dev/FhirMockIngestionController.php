<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Services\Fhir\ControlledIngestion\MockIngestionPreviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FhirMockIngestionController extends Controller
{
    public function index(): View
    {
        $samplePath = base_path('resources/fhir/mock-ingestion/sample-gateway-payload.json');
        $samplePayload = is_file($samplePath) ? (string) file_get_contents($samplePath) : "{}\n";

        return view('dev.fhir.mock-ingestion.index', [
            'samplePayload' => $samplePayload,
            'prototypeConfig' => config('fhir.controlled_ingestion_prototype'),
        ]);
    }

    public function preview(Request $request, MockIngestionPreviewService $service): JsonResponse
    {
        $payload = $request->input('payload');

        if ($payload === null && $request->isJson()) {
            $payload = $request->all();
        }

        return response()->json($service->preview($payload));
    }
}
