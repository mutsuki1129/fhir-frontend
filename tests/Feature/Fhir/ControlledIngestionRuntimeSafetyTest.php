<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ControlledIngestionRuntimeSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.lesion_viewer.source', 'mock');
        Config::set('fhir.frontend_read_only', true);
        Config::set('services.fhir.smart_enabled', false);
    }

    public function test_phase_10a_runtime_post_routes_do_not_exist(): void
    {
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
            'queue',
            'api/queue',
        ] as $uri) {
            $this->assertFalse(
                $this->routeExists('POST', $uri),
                "POST /{$uri} must not exist in Phase 10A."
            );
        }
    }

    public function test_lesion_routes_remain_get_head_only(): void
    {
        $lesionRoutes = collect(Route::getRoutes())
            ->filter(fn ($route): bool => str_contains($route->uri(), 'lesions'))
            ->mapWithKeys(fn ($route): array => [$route->uri() => $route->methods()])
            ->all();

        $this->assertArrayHasKey('lesions', $lesionRoutes);
        $this->assertArrayHasKey('lesions/{lesion}', $lesionRoutes);
        $this->assertArrayHasKey('api/lesions', $lesionRoutes);
        $this->assertArrayHasKey('api/lesions/{lesion}', $lesionRoutes);

        foreach ($lesionRoutes as $uri => $methods) {
            $this->assertSame([], array_values(array_intersect($methods, ['POST', 'PATCH', 'DELETE'])));
            $this->assertContains('GET', $methods);
            $this->assertContains('HEAD', $methods);
        }
    }

    public function test_existing_runtime_safety_tests_remain_available(): void
    {
        $this->assertTrue(class_exists(GovernanceRuntimeSafetyTest::class));
        $this->assertTrue(class_exists(FhirValidationRuntimeSafetyTest::class));
        $this->assertTrue(class_exists(GatewayRuntimeSafetyTest::class));
        $this->assertTrue(class_exists(FrontendReadOnlyRouteGuardTest::class));
    }

    public function test_phase_10a_documents_do_not_call_fhir_write_methods_for_lesion_reads(): void
    {
        $this->actingAs((new User())->forceFill([
            'id' => 1,
            'name' => 'Controlled Ingestion Runtime Safety User',
            'email' => 'controlled-ingestion-runtime-safety@example.test',
        ]));

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();
            $mock->shouldReceive('expungeDeletedResource')->never();
        });

        $this->get('/lesions')->assertOk();
        $this->getJson('/api/lesions')->assertOk();
    }

    public function test_phase_10a_does_not_add_ingestion_controller_job_or_writer_service(): void
    {
        foreach ([
            'app/Http/Controllers/IngestionController.php',
            'app/Http/Controllers/GatewayIngestionController.php',
            'app/Http/Controllers/AiAgentRuntimeController.php',
            'app/Jobs/FhirIngestionJob.php',
            'app/Jobs/GatewayIngestionJob.php',
            'app/Services/Fhir/Ingestion/FhirWriterService.php',
            'app/Services/Fhir/Ingestion/ControlledIngestionWriter.php',
            'app/Services/Fhir/Gateway/GatewayRuntimeWriter.php',
        ] as $path) {
            $this->assertFileDoesNotExist(base_path($path), "{$path} must not exist in Phase 10A.");
        }
    }

    private function routeExists(string $method, string $uri): bool
    {
        return collect(Route::getRoutes())->contains(function ($route) use ($method, $uri): bool {
            return in_array($method, $route->methods(), true) && trim($route->uri(), '/') === trim($uri, '/');
        });
    }
}

