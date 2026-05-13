<?php

namespace Tests\Feature\Fhir;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ControlledIngestionPrototypeFeatureFlagTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.controlled_ingestion_prototype', [
            'enabled' => false,
            'mode' => 'mock',
            'allow_fhir_write' => false,
        ]);
    }

    public function test_feature_flag_disabled_blocks_get_and_post_routes(): void
    {
        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();
        $this->postJson('/dev/fhir/mock-ingestion/preview', [])->assertNotFound();
    }

    public function test_feature_flag_enabled_in_testing_allows_get_and_post_routes(): void
    {
        $this->enablePrototype();

        $this->get('/dev/fhir/mock-ingestion')
            ->assertOk()
            ->assertSee('Dev-only Mock Ingestion Prototype');

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => $this->samplePayload(),
        ])->assertOk()
            ->assertJsonPath('meta.devOnly', true)
            ->assertJsonPath('meta.mockOnly', true)
            ->assertJsonPath('meta.noFHIRWrite', true);
    }

    public function test_production_like_environment_blocks_route(): void
    {
        $this->enablePrototype();
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();
        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => $this->samplePayload(),
        ])->assertNotFound();
    }

    public function test_non_mock_mode_blocks_route(): void
    {
        $this->enablePrototype(['mode' => 'live']);

        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();
    }

    public function test_allow_fhir_write_cannot_be_enabled_by_config(): void
    {
        $this->assertFalse(config('fhir.controlled_ingestion_prototype.allow_fhir_write'));

        $this->enablePrototype(['allow_fhir_write' => true]);

        $this->get('/dev/fhir/mock-ingestion')->assertNotFound();
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
