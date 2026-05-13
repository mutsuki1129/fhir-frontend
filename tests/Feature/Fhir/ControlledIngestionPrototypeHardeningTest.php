<?php

namespace Tests\Feature\Fhir;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ControlledIngestionPrototypeHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->enablePrototype();
    }

    public function test_guard_blocks_disabled_non_mock_production_and_write_enabled_states(): void
    {
        Config::set('fhir.controlled_ingestion_prototype.enabled', false);
        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();

        $this->enablePrototype(['mode' => 'live']);
        $this->postJson('/dev/fhir/mock-ingestion/preview', [])->assertNotFound();

        $this->enablePrototype();
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();

        $this->app->detectEnvironment(fn (): string => 'testing');
        $this->enablePrototype(['allow_fhir_write' => true]);
        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();
    }

    public function test_invalid_and_missing_payloads_return_safe_validation_result_without_500(): void
    {
        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => '{not-json',
        ])->assertOk()
            ->assertJsonPath('data.validationResult.status', 'hold-for-review')
            ->assertJsonPath('data.validationResult.outcome', 'manual-review-required')
            ->assertJsonPath('data.validationResult.errors.0.code', 'payload.invalid_or_missing_required_fields')
            ->assertJsonPath('data.validationResult.errors.0.safeDetails', 'No sensitive payload included.')
            ->assertJsonMissingPath('data.payload');

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => json_encode(['schemaVersion' => '0.1-draft']),
        ])->assertOk()
            ->assertJsonPath('data.validationResult.status', 'hold-for-review')
            ->assertJsonPath('data.validationResult.outcome', 'manual-review-required')
            ->assertJsonFragment(['code' => 'envelope.missing_message_id'])
            ->assertJsonMissingPath('data.payload');
    }

    public function test_large_binary_and_real_phi_like_payloads_are_safely_rejected_without_raw_echo(): void
    {
        $payload = json_decode($this->samplePayload(), true);
        $payload['subject']['name'] = 'Jane Realperson';
        $payload['subject']['birthDate'] = '1970-01-01';
        $payload['subject']['nationalId'] = 'A123456789';
        $payload['subject']['address'] = '1 Real Street';
        $payload['subject']['phone'] = '+886-900-000-000';
        $payload['payload']['documents'][] = [
            'title' => 'unsafe binary',
            'base64' => str_repeat('A', 256),
        ];

        $response = $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => json_encode($payload),
        ])->assertOk()
            ->assertJsonFragment(['code' => 'privacy.real_phi_not_allowed'])
            ->assertJsonFragment(['code' => 'privacy.binary_content_not_allowed'])
            ->assertJsonPath('data.candidateResources.0.persisted', false)
            ->assertJsonPath('data.candidateResources.0.fhirReference', null)
            ->assertJsonPath('data.manualReviewQueueItem.signedOff', false)
            ->assertJsonPath('data.validationResult.review.signedOff', false)
            ->assertJsonPath('data.validationResult.readOnly', true)
            ->assertJsonPath('data.validationResult.runtime', 'dev-mock-only')
            ->assertJsonMissingPath('data.payload')
            ->json();

        $encoded = json_encode($response);

        $this->assertIsString($encoded);
        $this->assertStringNotContainsString('Jane Realperson', $encoded);
        $this->assertStringNotContainsString('A123456789', $encoded);
        $this->assertStringNotContainsString('+886-900-000-000', $encoded);
        $this->assertStringNotContainsString(str_repeat('A', 128), $encoded);
        $this->assertStringNotContainsString('signed-off', $encoded);
        $this->assertStringNotContainsString('written-to-fhir', $encoded);
    }

    public function test_payload_size_limit_returns_safe_error_without_echoing_payload(): void
    {
        $largePayload = str_repeat('x', 33000);

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => $largePayload,
        ])->assertOk()
            ->assertJsonPath('data.validationResult.errors.0.code', 'payload.invalid_or_missing_required_fields')
            ->assertJsonPath('data.validationResult.errors.0.safeDetails', 'No sensitive payload included.')
            ->assertJsonMissingPath('data.payload');
    }

    public function test_ui_contains_required_safety_wording_and_avoids_forbidden_wording(): void
    {
        $response = $this->get('/dev/fhir/mock-ingestion')
            ->assertOk()
            ->assertSee('Dev-only')
            ->assertSee('Mock-only')
            ->assertSee('Feature flag guarded')
            ->assertSee('No real PHI')
            ->assertSee('No production FHIR Server')
            ->assertSee('No direct FHIR write')
            ->assertSee('No AI Agent runtime')
            ->assertSee('No CDS runtime')
            ->assertSee('No SMART production')
            ->assertSee('Candidate preview only');

        $content = $response->getContent();

        foreach ([
            'production ingestion enabled',
            'FHIR write enabled',
            'AI diagnosis',
            'clinical advice',
            'automatic diagnosis',
            'treatment recommendation',
            'signed-off',
            'clinician-confirmed',
            'written-to-fhir',
            'production-approved',
        ] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $content);
        }
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function enablePrototype(array $overrides = []): void
    {
        Config::set('fhir.controlled_ingestion_prototype', array_merge([
            'enabled' => true,
            'mode' => 'mock',
            'allow_fhir_write' => false,
        ], $overrides));
    }

    private function samplePayload(): string
    {
        return (string) file_get_contents(base_path('resources/fhir/mock-ingestion/sample-gateway-payload.json'));
    }
}
