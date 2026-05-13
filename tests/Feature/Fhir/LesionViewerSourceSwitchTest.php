<?php

namespace Tests\Feature\Fhir;

use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LesionViewerSourceSwitchTest extends TestCase
{
    public function test_mock_source_keeps_existing_api_payload(): void
    {
        Config::set('fhir.lesion_viewer_source', 'mock');

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('meta.readOnly', true)
            ->assertJsonPath('meta.source', 'mock-lesion-repository')
            ->assertJsonPath('meta.contract', 'docs/fhir/lesion-viewer-data-contract.md')
            ->assertJsonCount(3, 'data');

        $this->getJson('/api/lesions/lesion-001')
            ->assertOk()
            ->assertJsonPath('data.lesionId', 'lesion-001')
            ->assertJsonPath('meta.source', 'mock-lesion-repository');
    }

    public function test_fhir_source_returns_safe_read_only_aggregation_response(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn([
                'resourceType' => 'Bundle',
                'type' => 'searchset',
                'entry' => [],
            ]);
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', '001')->andReturn([
                'resourceType' => 'OperationOutcome',
            ]);
        });

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertExactJson([
                'data' => [],
                'meta' => [
                    'readOnly' => true,
                    'source' => 'fhir-backed-lesion-repository',
                    'contract' => 'docs/fhir/lesion-viewer-data-contract.md',
                    'aggregation' => 'diagnostic-report-centered',
                    'enrichment' => [
                        'patient' => true,
                        'observations' => true,
                        'encounter' => true,
                        'conditions' => true,
                        'documents' => true,
                        'consents' => true,
                    ],
                    'status' => 'read-only-aggregation-v3',
                ],
            ]);

        $this->getJson('/api/lesions/lesion-001')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Lesion viewer record not found.',
            ]);
    }

    public function test_unknown_source_falls_back_to_mock_api_without_500(): void
    {
        Config::set('fhir.lesion_viewer_source', 'unknown');

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('meta.source', 'mock-lesion-repository')
            ->assertJsonCount(3, 'data');
    }
}
