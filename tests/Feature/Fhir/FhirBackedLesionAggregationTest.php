<?php

namespace Tests\Feature\Fhir;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\LesionViewer\FhirBackedLesionRepository;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FhirBackedLesionAggregationTest extends TestCase
{
    public function test_diagnostic_report_bundle_maps_to_lesion_view_model(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn($this->diagnosticReportBundle());
            $this->expectEnrichmentReads($mock);
            $this->expectNoWrites($mock);
        });

        $lesions = $this->app->make(FhirBackedLesionRepository::class)->all();

        $this->assertCount(1, $lesions);
        $this->assertSame($this->expectedLesion(), $lesions[0]);
    }

    public function test_find_supports_lesion_id_direct_id_and_diagnostic_report_reference(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->times(3)->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReportResource());
            $this->expectEnrichmentReads($mock, 3);
            $this->expectNoWrites($mock);
        });

        $repository = $this->app->make(FhirBackedLesionRepository::class);

        $this->assertSame('lesion-report-001', $repository->find('lesion-report-001')['lesionId'] ?? null);
        $this->assertSame('lesion-report-001', $repository->find('report-001')['lesionId'] ?? null);
        $this->assertSame('lesion-report-001', $repository->find('DiagnosticReport/report-001')['lesionId'] ?? null);
    }

    public function test_missing_diagnostic_report_returns_api_404(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'missing-report')->andReturn([
                'resourceType' => 'OperationOutcome',
            ]);
            $this->expectNoWrites($mock);
        });

        $this->getJson('/api/lesions/lesion-missing-report')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Lesion viewer record not found.',
            ]);
    }

    public function test_fhir_source_api_returns_aggregated_diagnostic_report_payload(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn($this->diagnosticReportBundle());
            $this->expectEnrichmentReads($mock);
            $this->expectNoWrites($mock);
        });

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('data.0.lesionId', 'lesion-report-001')
            ->assertJsonPath('data.0.title', '病灶相關臨床報告')
            ->assertJsonPath('data.0.subject.patientReference', 'Patient/patient-001')
            ->assertJsonPath('data.0.resources.observations.0', 'Observation/obs-001')
            ->assertJsonPath('data.0.resources.observations.1', 'Observation/obs-002')
            ->assertJsonPath('data.0.resources.encounters.0', 'Encounter/encounter-001')
            ->assertJsonPath('data.0.enrichment.patient.status', 'available')
            ->assertJsonPath('data.0.enrichment.patient.gender', 'female')
            ->assertJsonPath('data.0.enrichment.observations.0.valueSummary', '36.8 Cel')
            ->assertJsonPath('data.0.enrichment.encounter.statusNote', 'available')
            ->assertJsonPath('meta.source', 'fhir-backed-lesion-repository')
            ->assertJsonPath('meta.aggregation', 'diagnostic-report-centered')
            ->assertJsonPath('meta.status', 'read-only-aggregation-v3');
    }

    private function diagnosticReportBundle(): array
    {
        return [
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'entry' => [
                [
                    'resource' => $this->diagnosticReportResource(),
                ],
            ],
        ];
    }

    private function diagnosticReportResource(): array
    {
        return [
            'resourceType' => 'DiagnosticReport',
            'id' => 'report-001',
            'status' => 'final',
            'code' => [
                'text' => '病灶相關臨床報告',
            ],
            'subject' => [
                'reference' => 'Patient/patient-001',
                'display' => 'P-001',
            ],
            'encounter' => [
                'reference' => 'Encounter/encounter-001',
            ],
            'result' => [
                [
                    'reference' => 'Observation/obs-001',
                ],
                [
                    'reference' => 'Observation/obs-002',
                ],
            ],
            'performer' => [
                [
                    'display' => 'clinician-reviewed workflow',
                ],
            ],
            'conclusion' => '展示用摘要，不包含自動診斷或治療建議。',
            'issued' => '2026-05-12T00:00:00Z',
            'meta' => [
                'lastUpdated' => '2026-05-12T00:00:00Z',
            ],
        ];
    }

    private function expectedLesion(): array
    {
        return [
            'lesionId' => 'lesion-report-001',
            'title' => '病灶相關臨床報告',
            'status' => 'final',
            'severity' => 'unknown',
            'source' => 'clinician-reviewed workflow',
            'subject' => [
                'patientReference' => 'Patient/patient-001',
                'displayId' => 'P-001',
                'gender' => 'female',
                'ageRange' => 'not-displayed',
            ],
            'summary' => '展示用摘要，不包含自動診斷或治療建議。',
            'clinicalStatus' => 'unknown',
            'verificationStatus' => 'unknown',
            'review' => [
                'reviewStatus' => 'pending-review',
                'reviewedAt' => '2026-05-12T00:00:00Z',
                'reviewedBy' => 'clinician-reviewed workflow',
            ],
            'resources' => [
                'observations' => ['Observation/obs-001', 'Observation/obs-002'],
                'conditions' => [],
                'diagnosticReports' => ['DiagnosticReport/report-001'],
                'documents' => [],
                'consents' => [],
                'encounters' => ['Encounter/encounter-001'],
            ],
            'lastUpdated' => '2026-05-12T00:00:00Z',
            'enrichment' => [
                'patient' => [
                    'reference' => 'Patient/patient-001',
                    'displayId' => 'P-001',
                    'gender' => 'female',
                    'birthDate' => 'masked',
                    'status' => 'available',
                ],
                'observations' => [
                    [
                        'reference' => 'Observation/obs-001',
                        'title' => '體溫觀察',
                        'status' => 'final',
                        'effective' => '2026-05-12T00:00:00Z',
                        'valueSummary' => '36.8 Cel',
                        'statusNote' => 'available',
                    ],
                    [
                        'reference' => 'Observation/obs-002',
                        'title' => '臨床文字觀察',
                        'status' => 'preliminary',
                        'effective' => '2026-05-12T00:10:00Z',
                        'valueSummary' => '展示用觀察文字',
                        'statusNote' => 'available',
                    ],
                ],
                'encounter' => [
                    'reference' => 'Encounter/encounter-001',
                    'status' => 'finished',
                    'periodStart' => '2026-05-12T00:00:00Z',
                    'periodEnd' => '2026-05-12T00:30:00Z',
                    'class' => 'ambulatory',
                    'statusNote' => 'available',
                ],
                'conditions' => [],
                'documents' => [],
                'consents' => [],
            ],
        ];
    }

    private function patientResource(): array
    {
        return [
            'resourceType' => 'Patient',
            'id' => 'patient-001',
            'identifier' => [
                [
                    'system' => 'urn:app:patient',
                    'value' => 'P-001',
                ],
            ],
            'gender' => 'female',
            'birthDate' => '1990-01-01',
        ];
    }

    private function observationQuantityResource(): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => 'obs-001',
            'status' => 'final',
            'code' => [
                'text' => '體溫觀察',
            ],
            'effectiveDateTime' => '2026-05-12T00:00:00Z',
            'valueQuantity' => [
                'value' => 36.8,
                'unit' => 'Cel',
            ],
        ];
    }

    private function observationStringResource(): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => 'obs-002',
            'status' => 'preliminary',
            'code' => [
                'text' => '臨床文字觀察',
            ],
            'effectiveDateTime' => '2026-05-12T00:10:00Z',
            'valueString' => '展示用觀察文字',
        ];
    }

    private function encounterResource(): array
    {
        return [
            'resourceType' => 'Encounter',
            'id' => 'encounter-001',
            'status' => 'finished',
            'class' => [
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'period' => [
                'start' => '2026-05-12T00:00:00Z',
                'end' => '2026-05-12T00:30:00Z',
            ],
        ];
    }

    private function expectEnrichmentReads($mock, int $times = 1): void
    {
        $mock->shouldReceive('read')->times($times)->with('Patient', 'patient-001')->andReturn($this->patientResource());
        $mock->shouldReceive('read')->times($times)->with('Observation', 'obs-001')->andReturn($this->observationQuantityResource());
        $mock->shouldReceive('read')->times($times)->with('Observation', 'obs-002')->andReturn($this->observationStringResource());
        $mock->shouldReceive('read')->times($times)->with('Encounter', 'encounter-001')->andReturn($this->encounterResource());
    }

    private function expectNoWrites($mock): void
    {
        $mock->shouldReceive('create')->never();
        $mock->shouldReceive('update')->never();
        $mock->shouldReceive('delete')->never();
        $mock->shouldReceive('expungeDeletedResource')->never();
    }
}
