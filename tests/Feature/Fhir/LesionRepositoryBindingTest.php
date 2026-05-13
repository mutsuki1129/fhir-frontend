<?php

namespace Tests\Feature\Fhir;

use App\Http\Controllers\LesionViewerController;
use App\Services\Fhir\LesionViewer\FhirBackedLesionRepository;
use App\Services\Fhir\LesionViewer\LesionRepository;
use App\Services\Fhir\LesionViewer\MockLesionRepository;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use Tests\TestCase;

class LesionRepositoryBindingTest extends TestCase
{
    public function test_mock_source_resolves_mock_repository(): void
    {
        Config::set('fhir.lesion_viewer_source', 'mock');

        $this->assertInstanceOf(MockLesionRepository::class, $this->app->make(LesionRepository::class));
    }

    public function test_fhir_source_resolves_fhir_backed_repository(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->assertInstanceOf(FhirBackedLesionRepository::class, $this->app->make(LesionRepository::class));
    }

    public function test_unknown_source_falls_back_to_mock_repository(): void
    {
        Config::set('fhir.lesion_viewer_source', 'unexpected-source');

        $this->assertInstanceOf(MockLesionRepository::class, $this->app->make(LesionRepository::class));
    }

    public function test_controller_depends_on_lesion_repository_interface(): void
    {
        $constructor = (new ReflectionClass(LesionViewerController::class))->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertSame(
            LesionRepository::class,
            $constructor->getParameters()[0]->getType()?->getName(),
        );
    }
}
