<?php

namespace Tests\Feature\Fhir;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ControlledIngestionPrototypePreviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.controlled_ingestion_prototype', [
            'enabled' => true,
            'mode' => 'mock',
            'allow_fhir_write' => false,
        ]);
    }

    public function test_valid_synthetic_payload_returns_validation_queue_and_candidates(): void
    {
        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => $this->samplePayload(),
        ])->assertOk()
            ->assertJsonPath('data.messageId', 'msg-001')
            ->assertJsonPath('data.correlationId', 'case-001')
            ->assertJsonPath('data.validationResult.status', 'hold-for-review')
            ->assertJsonPath('data.validationResult.outcome', 'manual-review-required')
            ->assertJsonPath('data.validationResult.review.signedOff', false)
            ->assertJsonPath('data.manualReviewQueueItem.status', 'pending-review')
            ->assertJsonPath('data.manualReviewQueueItem.signedOff', false)
            ->assertJsonPath('data.candidateResources.0.persisted', false)
            ->assertJsonPath('data.candidateResources.0.fhirReference', null)
            ->assertJsonStructure([
                'data' => [
                    'validationResult',
                    'manualReviewQueueItem',
                    'candidateResources' => [[
                        'candidateId',
                        'resourceType',
                        'mappingStatus',
                        'validationStatus',
                        'reviewStatus',
                        'persisted',
                        'fhirReference',
                        'preview',
                    ]],
                ],
                'meta' => [
                    'readOnly',
                    'devOnly',
                    'mockOnly',
                    'noFHIRWrite',
                    'featureFlag',
                    'source',
                ],
            ]);
    }

    public function test_missing_message_id_returns_safe_hold_for_review_error(): void
    {
        $payload = json_decode($this->samplePayload(), true);
        unset($payload['messageId']);

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => json_encode($payload),
        ])->assertOk()
            ->assertJsonPath('data.validationResult.status', 'hold-for-review')
            ->assertJsonPath('data.validationResult.outcome', 'manual-review-required')
            ->assertJsonPath('data.validationResult.errors.0.code', 'envelope.missing_message_id')
            ->assertJsonPath('data.validationResult.errors.0.safeDetails', 'No sensitive payload included.')
            ->assertJsonMissingPath('data.payload');
    }

    public function test_real_phi_like_subject_and_binary_payload_are_rejected_safely(): void
    {
        $payload = json_decode($this->samplePayload(), true);
        $payload['subject']['patientReference'] = 'Patient/real-patient-123';
        $payload['subject']['displayId'] = 'REAL-PERSON';
        $payload['payload']['documents'][] = ['title' => 'unsafe', 'base64' => 'abc123'];

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => json_encode($payload),
        ])->assertOk()
            ->assertJsonFragment(['code' => 'privacy.real_phi_not_allowed'])
            ->assertJsonFragment(['code' => 'privacy.binary_content_not_allowed'])
            ->assertJsonPath('data.candidateResources.0.safeSubject.reference', 'Patient/mock-patient-001')
            ->assertJsonPath('data.candidateResources.0.safeSubject.display', 'MOCK-P-001');
    }

    private function samplePayload(): string
    {
        return (string) file_get_contents(base_path('resources/fhir/mock-ingestion/sample-gateway-payload.json'));
    }
}
