<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\LesionViewer\FhirBackedLesionRepository;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FhirBackedLesionLinkingTest extends TestCase
{
    public function test_condition_document_reference_and_consent_linking_read_successfully(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReport());
            $this->expectBaseReads($mock);
            $this->expectLinkingReads($mock);
            $this->expectNoWrites($mock);
        });

        $lesion = $this->app->make(FhirBackedLesionRepository::class)->find('lesion-report-001');

        $this->assertSame(['Condition/condition-001'], $lesion['resources']['conditions']);
        $this->assertSame(['DocumentReference/doc-001'], $lesion['resources']['documents']);
        $this->assertSame(['Consent/consent-001'], $lesion['resources']['consents']);

        $this->assertSame('available', $lesion['enrichment']['conditions'][0]['statusNote']);
        $this->assertSame('病灶相關狀態紀錄', $lesion['enrichment']['conditions'][0]['title']);
        $this->assertSame('active', $lesion['enrichment']['conditions'][0]['clinicalStatus']);
        $this->assertSame('provisional', $lesion['enrichment']['conditions'][0]['verificationStatus']);
        $this->assertNotSame('confirmed', $lesion['enrichment']['conditions'][0]['verificationStatus']);

        $this->assertSame('available', $lesion['enrichment']['documents'][0]['statusNote']);
        $this->assertSame('病灶相關報告文件', $lesion['enrichment']['documents'][0]['title']);
        $this->assertSame('current', $lesion['enrichment']['documents'][0]['status']);
        $this->assertSame('clinical-note', $lesion['enrichment']['documents'][0]['type']);
        $this->assertSame('application/pdf', $lesion['enrichment']['documents'][0]['contentType']);
        $this->assertArrayNotHasKey('data', $lesion['enrichment']['documents'][0]);
        $this->assertArrayNotHasKey('url', $lesion['enrichment']['documents'][0]);

        $this->assertSame('available', $lesion['enrichment']['consents'][0]['statusNote']);
        $this->assertSame('active', $lesion['enrichment']['consents'][0]['status']);
        $this->assertSame('patient-privacy', $lesion['enrichment']['consents'][0]['scope']);
        $this->assertSame('2026-05-12T00:00:00Z', $lesion['enrichment']['consents'][0]['periodStart']);
        $this->assertNull($lesion['enrichment']['consents'][0]['periodEnd']);
    }

    public function test_linking_read_exceptions_keep_references_unavailable_without_failing_lesion(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReport());
            $this->expectBaseReads($mock);
            $mock->shouldReceive('read')->once()->with('Condition', 'condition-001')->andThrow(new \RuntimeException('condition secret'));
            $mock->shouldReceive('read')->once()->with('DocumentReference', 'doc-001')->andThrow(new \RuntimeException('document secret'));
            $mock->shouldReceive('read')->once()->with('Consent', 'consent-001')->andThrow(new \RuntimeException('consent secret'));
            $this->expectNoWrites($mock);
        });

        $lesion = $this->app->make(FhirBackedLesionRepository::class)->find('DiagnosticReport/report-001');

        $this->assertSame('lesion-report-001', $lesion['lesionId']);
        $this->assertSame([
            'reference' => 'Condition/condition-001',
            'statusNote' => 'unavailable',
        ], $lesion['enrichment']['conditions'][0]);
        $this->assertSame([
            'reference' => 'DocumentReference/doc-001',
            'statusNote' => 'unavailable',
        ], $lesion['enrichment']['documents'][0]);
        $this->assertSame([
            'reference' => 'Consent/consent-001',
            'statusNote' => 'unavailable',
        ], $lesion['enrichment']['consents'][0]);
    }

    public function test_extension_references_are_collected_conservatively(): void
    {
        $report = $this->diagnosticReport();
        unset($report['supportingInfo']);
        $report['extension'] = [
            ['valueReference' => ['reference' => 'Condition/condition-001']],
            ['valueReference' => ['reference' => 'DocumentReference/doc-001']],
            ['valueReference' => ['reference' => 'Consent/consent-001']],
            ['valueReference' => ['reference' => 'Binary/not-linked']],
        ];

        $this->mock(FhirApiClient::class, function ($mock) use ($report): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($report);
            $this->expectBaseReads($mock);
            $this->expectLinkingReads($mock);
            $this->expectNoWrites($mock);
        });

        $lesion = $this->app->make(FhirBackedLesionRepository::class)->find('report-001');

        $this->assertSame(['Condition/condition-001'], $lesion['resources']['conditions']);
        $this->assertSame(['DocumentReference/doc-001'], $lesion['resources']['documents']);
        $this->assertSame(['Consent/consent-001'], $lesion['resources']['consents']);
    }

    public function test_fhir_source_api_and_ui_include_phase_7_linking_without_write_controls(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn([
                'resourceType' => 'Bundle',
                'type' => 'searchset',
                'entry' => [
                    ['resource' => $this->diagnosticReport()],
                ],
            ]);
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn($this->diagnosticReport());
            $this->expectBaseReads($mock, 2);
            $this->expectLinkingReads($mock, 2);
            $this->expectNoWrites($mock);
        });

        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('data.0.enrichment.conditions.0.statusNote', 'available')
            ->assertJsonPath('data.0.enrichment.documents.0.statusNote', 'available')
            ->assertJsonPath('data.0.enrichment.consents.0.statusNote', 'available')
            ->assertJsonPath('meta.status', 'read-only-aggregation-v3');

        $this->actingAs((new User())->forceFill([
            'id' => 77,
            'name' => 'Lesion Linking UI User',
            'email' => 'lesion-linking-ui@example.test',
        ]));

        $response = $this->get('/lesions/lesion-report-001')
            ->assertOk()
            ->assertSee('病灶狀態紀錄')
            ->assertSee('文件 / 報告參照')
            ->assertSee('同意 / 簽核參照');

        foreach (['create', 'edit', 'delete', 'upload', 'automatic diagnosis', 'clinical advice', 'treatment recommendation', '診斷結果', '治療建議', '醫療結論', '療效成立'] as $forbiddenLabel) {
            $response->assertDontSee($forbiddenLabel, false);
        }
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
            ],
            'supportingInfo' => [
                ['reference' => 'Condition/condition-001'],
                ['reference' => 'DocumentReference/doc-001'],
                ['reference' => 'Consent/consent-001'],
            ],
            'conclusion' => '展示用摘要，僅供檢視。',
            'issued' => '2026-05-12T00:00:00Z',
            'meta' => [
                'lastUpdated' => '2026-05-12T00:00:00Z',
            ],
        ];
    }

    private function patient(): array
    {
        return [
            'resourceType' => 'Patient',
            'id' => 'patient-001',
            'identifier' => [['value' => 'P-001']],
            'gender' => 'female',
            'birthDate' => '1990-01-01',
        ];
    }

    private function observation(): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => 'obs-001',
            'status' => 'final',
            'code' => ['text' => '病灶觀察值'],
            'effectiveDateTime' => '2026-05-12T00:00:00Z',
            'valueString' => '展示用觀察值',
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

    private function condition(): array
    {
        return [
            'resourceType' => 'Condition',
            'id' => 'condition-001',
            'clinicalStatus' => [
                'coding' => [
                    ['code' => 'active', 'display' => 'Active'],
                ],
            ],
            'verificationStatus' => [
                'coding' => [
                    ['code' => 'provisional', 'display' => 'Provisional'],
                ],
            ],
            'code' => ['text' => '病灶相關狀態紀錄'],
            'recordedDate' => '2026-05-12T00:00:00Z',
        ];
    }

    private function documentReference(): array
    {
        return [
            'resourceType' => 'DocumentReference',
            'id' => 'doc-001',
            'status' => 'current',
            'type' => ['text' => 'clinical-note'],
            'description' => '病灶相關報告文件',
            'date' => '2026-05-12T00:00:00Z',
            'content' => [
                [
                    'attachment' => [
                        'contentType' => 'application/pdf',
                        'title' => '病灶相關報告 PDF',
                        'data' => 'sensitive-binary-content',
                        'url' => 'Binary/binary-001',
                    ],
                ],
            ],
        ];
    }

    private function consent(): array
    {
        return [
            'resourceType' => 'Consent',
            'id' => 'consent-001',
            'status' => 'active',
            'scope' => [
                'coding' => [
                    ['code' => 'patient-privacy', 'display' => 'Patient Privacy'],
                ],
            ],
            'provision' => [
                'period' => [
                    'start' => '2026-05-12T00:00:00Z',
                    'end' => null,
                ],
            ],
        ];
    }

    private function expectBaseReads($mock, int $times = 1): void
    {
        $mock->shouldReceive('read')->times($times)->with('Patient', 'patient-001')->andReturn($this->patient());
        $mock->shouldReceive('read')->times($times)->with('Observation', 'obs-001')->andReturn($this->observation());
        $mock->shouldReceive('read')->times($times)->with('Encounter', 'encounter-001')->andReturn($this->encounter());
    }

    private function expectLinkingReads($mock, int $times = 1): void
    {
        $mock->shouldReceive('read')->times($times)->with('Condition', 'condition-001')->andReturn($this->condition());
        $mock->shouldReceive('read')->times($times)->with('DocumentReference', 'doc-001')->andReturn($this->documentReference());
        $mock->shouldReceive('read')->times($times)->with('Consent', 'consent-001')->andReturn($this->consent());
    }

    private function expectNoWrites($mock): void
    {
        $mock->shouldReceive('create')->never();
        $mock->shouldReceive('update')->never();
        $mock->shouldReceive('delete')->never();
        $mock->shouldReceive('expungeDeletedResource')->never();
    }
}
