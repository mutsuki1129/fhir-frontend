<?php

namespace App\Http\Controllers;

use App\Services\Fhir\LesionViewer\LesionRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class LesionViewerController extends Controller
{
    public function __construct(
        private readonly LesionRepository $lesions,
    ) {
    }

    public function index(): View
    {
        return view('admin.lesions.index', [
            'lesions' => $this->lesions->all(),
            'meta' => $this->lesions->meta(),
        ]);
    }

    public function show(string $lesion): View
    {
        $record = $this->lesions->find($lesion);

        abort_if($record === null, 404, 'Lesion viewer record not found.');

        return view('admin.lesions.show', [
            'lesion' => $record,
            'meta' => $this->lesions->meta(),
        ]);
    }

    public function apiIndex(): JsonResponse
    {
        return response()->json([
            'data' => array_values($this->lesions->all()),
            'meta' => $this->lesions->meta(),
        ]);
    }

    public function apiShow(string $lesion): JsonResponse
    {
        $record = $this->lesions->find($lesion);

        if ($record === null) {
            return response()->json([
                'message' => 'Lesion viewer record not found.',
            ], 404);
        }

        return response()->json([
            'data' => $record,
            'meta' => $this->lesions->meta(),
        ]);
    }
}
