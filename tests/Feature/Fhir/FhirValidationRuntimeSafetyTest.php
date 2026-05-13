<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FhirValidationRuntimeSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.lesion_viewer.source', 'mock');
        Config::set('fhir.frontend_read_only', true);
        Config::set('services.fhir.smart_enabled', false);
    }

    public function test_validation_runtime_post_routes_do_not_exist(): void
    {
        foreach ([
            'validate',
            'validation',
            'fhir/validate',
            'fhir/validation',
            'api/validate',
            'api/validation',
            'gateway/validate',
            'api/gateway/validate',
        ] as $uri) {
            $this->assertFalse(
                $this->routeExists('POST', $uri),
                "POST /{$uri} must not exist in Phase 9B."
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
            $this->assertSame(
                [],
                array_values(array_intersect($methods, ['POST', 'PATCH', 'DELETE'])),
                "{$uri} must not expose POST/PATCH/DELETE."
            );
            $this->assertContains('GET', $methods);
            $this->assertContains('HEAD', $methods);
        }
    }

    public function test_phase_9b_documentation_does_not_call_fhir_write_methods_for_lesion_reads(): void
    {
        $this->actingAs((new User())->forceFill([
            'id' => 1,
            'name' => 'Validation Runtime Safety User',
            'email' => 'validation-runtime-safety@example.test',
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

    public function test_existing_runtime_safety_test_classes_remain_available(): void
    {
        $this->assertTrue(class_exists(GatewayRuntimeSafetyTest::class));
        $this->assertTrue(class_exists(FrontendReadOnlyRouteGuardTest::class));
    }

    private function routeExists(string $method, string $uri): bool
    {
        return collect(Route::getRoutes())->contains(function ($route) use ($method, $uri): bool {
            return in_array($method, $route->methods(), true) && trim($route->uri(), '/') === trim($uri, '/');
        });
    }
}
