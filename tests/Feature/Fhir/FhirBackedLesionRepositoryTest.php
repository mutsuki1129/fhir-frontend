<?php

namespace Tests\Feature\Fhir;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\LesionViewer\FhirBackedLesionRepository;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FhirBackedLesionRepositoryTest extends TestCase
{
    public function test_repository_returns_safe_empty_read_only_payload_when_search_is_empty(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn([
                'resourceType' => 'Bundle',
                'type' => 'searchset',
                'entry' => [],
            ]);
        });

        $repository = $this->app->make(FhirBackedLesionRepository::class);

        $this->assertSame([], $repository->all());
        $this->assertSame([
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
        ], $repository->meta());
    }

    public function test_fhir_source_api_does_not_call_fhir_write_methods(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn([
                'resourceType' => 'Bundle',
                'type' => 'searchset',
                'entry' => [],
            ]);
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();
            $mock->shouldReceive('expungeDeletedResource')->never();
        });

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('meta.source', 'fhir-backed-lesion-repository')
            ->assertJsonPath('meta.aggregation', 'diagnostic-report-centered')
            ->assertJsonPath('meta.enrichment.patient', true)
            ->assertJsonPath('meta.enrichment.observations', true)
            ->assertJsonPath('meta.enrichment.encounter', true)
            ->assertJsonPath('meta.enrichment.conditions', true)
            ->assertJsonPath('meta.enrichment.documents', true)
            ->assertJsonPath('meta.enrichment.consents', true)
            ->assertJsonPath('meta.status', 'read-only-aggregation-v3')
            ->assertJsonCount(0, 'data');
    }

    public function test_search_exception_returns_empty_payload_without_500(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andThrow(new \RuntimeException('connection failed with secret token'));
        });

        $repository = $this->app->make(FhirBackedLesionRepository::class);

        $this->assertSame([], $repository->all());
        $this->assertSame(\RuntimeException::class, $repository->meta()['lastError'] ?? null);
    }

    public function test_read_exception_returns_null_without_500(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andThrow(new \RuntimeException('connection failed with secret token'));
        });

        $repository = $this->app->make(FhirBackedLesionRepository::class);

        $this->assertNull($repository->find('lesion-report-001'));
        $this->assertSame(\RuntimeException::class, $repository->meta()['lastError'] ?? null);
    }
}
