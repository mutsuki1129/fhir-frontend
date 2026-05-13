<?php

namespace Tests\Feature\Fhir;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\LesionViewer\FhirBackedLesionRepository;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FhirBackedLesionEnrichmentTest extends TestCase
{
    public function test_patient_observation_and_encounter_enrichment_are_read_from_references(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn($this->bundle());
            $mock->shouldReceive('read')->once()->with('Patient', 'patient-001')->andReturn($this->patient());
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-001')->andReturn($this->observationQuantity());
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-002')->andReturn($this->observationString());
            $mock->shouldReceive('read')->once()->with('Encounter', 'encounter-001')->andReturn($this->encounter());
            $this->expectNoWrites($mock);
        });

        $lesion = $this->app->make(FhirBackedLesionRepository::class)->all()[0];

        $this->assertSame('available', $lesion['enrichment']['patient']['status']);
        $this->assertSame('female', $lesion['enrichment']['patient']['gender']);
        $this->assertSame('masked', $lesion['enrichment']['patient']['birthDate']);
        $this->assertSame('36.8 Cel', $lesion['enrichment']['observations'][0]['valueSummary']);
        $this->assertSame('體溫觀察', $lesion['enrichment']['observations'][0]['title']);
        $this->assertSame('展示用觀察文字', $lesion['enrichment']['observations'][1]['valueSummary']);
        $this->assertSame('available', $lesion['enrichment']['encounter']['statusNote']);
        $this->assertSame('2026-05-12T00:00:00Z', $lesion['enrichment']['encounter']['periodStart']);
        $this->assertSame('2026-05-12T00:30:00Z', $lesion['enrichment']['encounter']['periodEnd']);
    }

    public function test_enrichment_read_exceptions_keep_lesion_payload_available(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReport());
            $mock->shouldReceive('read')->once()->with('Patient', 'patient-001')->andThrow(new \RuntimeException('patient unavailable secret-token'));
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-001')->andReturn($this->observationQuantity());
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-002')->andThrow(new \RuntimeException('observation unavailable secret-token'));
            $mock->shouldReceive('read')->once()->with('Encounter', 'encounter-001')->andThrow(new \RuntimeException('encounter unavailable secret-token'));
            $this->expectNoWrites($mock);
        });

        $lesion = $this->app->make(FhirBackedLesionRepository::class)->find('lesion-report-001');

        $this->assertSame('lesion-report-001', $lesion['lesionId']);
        $this->assertSame('unavailable', $lesion['enrichment']['patient']['status']);
        $this->assertSame('available', $lesion['enrichment']['observations'][0]['statusNote']);
        $this->assertSame('unavailable', $lesion['enrichment']['observations'][1]['statusNote']);
        $this->assertSame('Observation/obs-002', $lesion['enrichment']['observations'][1]['reference']);
        $this->assertSame('unavailable', $lesion['enrichment']['encounter']['statusNote']);
        $this->assertSame('Encounter/encounter-001', $lesion['enrichment']['encounter']['reference']);
    }

    public function test_fhir_source_api_detail_includes_enrichment_without_changing_mock_source(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReport());
            $mock->shouldReceive('read')->once()->with('Patient', 'patient-001')->andReturn($this->patient());
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-001')->andReturn($this->observationQuantity());
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-002')->andReturn($this->observationString());
            $mock->shouldReceive('read')->once()->with('Encounter', 'encounter-001')->andReturn($this->encounter());
            $this->expectNoWrites($mock);
        });

        $this->getJson('/api/lesions/lesion-report-001')
            ->assertOk()
            ->assertJsonPath('data.enrichment.patient.status', 'available')
            ->assertJsonPath('data.enrichment.patient.birthDate', 'masked')
            ->assertJsonPath('data.enrichment.observations.0.valueSummary', '36.8 Cel')
            ->assertJsonPath('data.enrichment.encounter.statusNote', 'available')
            ->assertJsonPath('meta.status', 'read-only-aggregation-v3');

        Config::set('fhir.lesion_viewer_source', 'mock');

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('meta.source', 'mock-lesion-repository')
            ->assertJsonCount(3, 'data');
    }

    private function bundle(): array
    {
        return [
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'entry' => [
                ['resource' => $this->diagnosticReport()],
            ],
        ];
    }

    private function diagnosticReport(): array
    {
        return [
            'resourceType' => 'DiagnosticReport',
            'id' => 'report-001',
            'status' => 'final',
            'code' => ['text' => '病灶相關臨床報告'],
            'subject' => [
                'reference' => 'Patient/patient-001',
                'display' => 'P-001',
            ],
            'encounter' => ['reference' => 'Encounter/encounter-001'],
            'result' => [
                ['reference' => 'Observation/obs-001'],
                ['reference' => 'Observation/obs-002'],
            ],
            'performer' => [
                ['display' => 'clinician-reviewed workflow'],
            ],
            'conclusion' => '展示用摘要，不包含自動診斷或治療建議。',
            'issued' => '2026-05-12T00:00:00Z',
            'meta' => ['lastUpdated' => '2026-05-12T00:00:00Z'],
        ];
    }

    private function patient(): array
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

    private function observationQuantity(): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => 'obs-001',
            'status' => 'final',
            'code' => ['text' => '體溫觀察'],
            'effectiveDateTime' => '2026-05-12T00:00:00Z',
            'valueQuantity' => [
                'value' => 36.8,
                'unit' => 'Cel',
            ],
        ];
    }

    private function observationString(): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => 'obs-002',
            'status' => 'preliminary',
            'code' => ['text' => '臨床文字觀察'],
            'effectiveDateTime' => '2026-05-12T00:10:00Z',
            'valueString' => '展示用觀察文字',
        ];
    }

    private function encounter(): array
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

    private function expectNoWrites($mock): void
    {
        $mock->shouldReceive('create')->never();
        $mock->shouldReceive('update')->never();
        $mock->shouldReceive('delete')->never();
        $mock->shouldReceive('expungeDeletedResource')->never();
    }
}
