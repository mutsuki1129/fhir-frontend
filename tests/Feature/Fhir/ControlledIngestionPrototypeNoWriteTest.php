<?php

namespace Tests\Feature\Fhir;

use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ControlledIngestionPrototypeNoWriteTest extends TestCase
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

    public function test_preview_does_not_call_fhir_client_write_methods_or_live_validate(): void
    {
        Http::preventStrayRequests();

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();
            $mock->shouldReceive('expungeDeletedResource')->never();
        });

        $this->postJson('/dev/fhir/mock-ingestion/preview', [
            'payload' => $this->samplePayload(),
        ])->assertOk()
            ->assertJsonPath('meta.noFHIRWrite', true)
            ->assertJsonPath('data.candidateResources.0.persisted', false)
            ->assertJsonPath('data.candidateResources.0.fhirReference', null);

        $this->assertFalse(method_exists(FhirApiClient::class, 'patch'));
        $this->assertFalse(method_exists(FhirApiClient::class, 'upload'));
        $this->assertFalse(method_exists(FhirApiClient::class, 'validate'));
    }

    public function test_only_allowed_dev_preview_post_route_is_registered_for_phase_10b(): void
    {
        $this->assertTrue($this->routeExists('POST', 'dev/fhir/mock-ingestion/preview'));

        foreach ([
            'gateway',
            'ingestion',
            'agent',
            'validate',
            'validation',
            'api/gateway',
            'api/ingestion',
            'api/agent',
            'api/validate',
            'api/validation',
            'lesions',
        ] as $uri) {
            $this->assertFalse($this->routeExists('POST', $uri), "POST /{$uri} must not exist.");
        }

        foreach (['lesions', 'api/lesions'] as $uri) {
            $this->assertFalse($this->routeExists('PATCH', $uri), "PATCH /{$uri} must not exist.");
            $this->assertFalse($this->routeExists('DELETE', $uri), "DELETE /{$uri} must not exist.");
        }
    }

    private function routeExists(string $method, string $uri): bool
    {
        return collect(Route::getRoutes())->contains(function ($route) use ($method, $uri): bool {
            return in_array($method, $route->methods(), true) && trim($route->uri(), '/') === trim($uri, '/');
        });
    }

    private function samplePayload(): string
    {
        return (string) file_get_contents(base_path('resources/fhir/mock-ingestion/sample-gateway-payload.json'));
    }
}
