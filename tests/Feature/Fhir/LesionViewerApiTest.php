<?php

namespace Tests\Feature\Fhir;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LesionViewerApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.lesion_viewer_source', 'mock');
    }

    public function test_api_lesions_returns_expected_json_structure(): void
    {
        $this->getJson('/api/lesions')
            ->assertOk()
            ->assertJsonPath('meta.readOnly', true)
            ->assertJsonPath('meta.source', 'mock-lesion-repository')
            ->assertJsonPath('meta.contract', 'docs/fhir/lesion-viewer-data-contract.md')
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'lesionId',
                    'title',
                    'status',
                    'severity',
                    'source',
                    'subject' => [
                        'patientReference',
                        'displayId',
                        'gender',
                        'ageRange',
                    ],
                    'summary',
                    'clinicalStatus',
                    'verificationStatus',
                    'review' => [
                        'reviewStatus',
                        'reviewedAt',
                        'reviewedBy',
                    ],
                    'resources' => [
                        'observations',
                        'conditions',
                        'diagnosticReports',
                        'documents',
                        'consents',
                        'encounters',
                    ],
                    'lastUpdated',
                ]],
                'meta' => [
                    'readOnly',
                    'source',
                    'contract',
                ],
            ]);
    }

    public function test_api_lesion_detail_returns_expected_json_structure(): void
    {
        $this->getJson('/api/lesions/lesion-001')
            ->assertOk()
            ->assertJsonPath('data.lesionId', 'lesion-001')
            ->assertJsonPath('data.status', 'clinician-reviewed')
            ->assertJsonPath('data.subject.patientReference', 'Patient/patient-001')
            ->assertJsonPath('meta.readOnly', true)
            ->assertJsonStructure([
                'data' => [
                    'lesionId',
                    'title',
                    'status',
                    'severity',
                    'source',
                    'subject' => [
                        'patientReference',
                        'displayId',
                        'gender',
                        'ageRange',
                    ],
                    'summary',
                    'clinicalStatus',
                    'verificationStatus',
                    'review' => [
                        'reviewStatus',
                        'reviewedAt',
                        'reviewedBy',
                    ],
                    'resources' => [
                        'observations',
                        'conditions',
                        'diagnosticReports',
                        'documents',
                        'consents',
                        'encounters',
                    ],
                    'lastUpdated',
                ],
                'meta' => [
                    'readOnly',
                    'source',
                    'contract',
                ],
            ]);
    }

    public function test_api_lesion_detail_returns_404_json_for_missing_lesion(): void
    {
        $this->getJson('/api/lesions/missing-lesion')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Lesion viewer record not found.',
            ]);
    }
}
