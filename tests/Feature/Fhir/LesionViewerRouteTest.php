<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LesionViewerRouteTest extends TestCase
{
    public function test_lesion_index_returns_200(): void
    {
        $this->actingAsUser();

        $this->get('/lesions')
            ->assertOk()
            ->assertSee('FHIR Read-only Lesion Viewer')
            ->assertSee('lesion-001');
    }

    public function test_lesion_detail_returns_200_for_existing_mock_lesion(): void
    {
        $this->actingAsUser();

        $this->get('/lesions/lesion-001')
            ->assertOk()
            ->assertSee('FHIR Read-only Lesion Viewer')
            ->assertSee('Lesion ID')
            ->assertSee('lesion-001')
            ->assertSee('Observation/obs-001');
    }

    public function test_lesion_detail_returns_404_for_missing_lesion(): void
    {
        $this->actingAsUser();

        $this->get('/lesions/missing-lesion')
            ->assertNotFound()
            ->assertSee('Lesion record not found')
            ->assertSee('missing-lesion')
            ->assertSee('Back to lesion list')
            ->assertDontSee('Laravel')
            ->assertDontSee('stack trace');
    }

    public function test_no_post_patch_or_delete_lesion_routes_exist(): void
    {
        $writeRoutes = collect(Route::getRoutes())
            ->filter(fn ($route): bool => str_contains($route->uri(), 'lesions'))
            ->filter(fn ($route): bool => count(array_intersect($route->methods(), ['POST', 'PATCH', 'DELETE'])) > 0)
            ->map(fn ($route): string => implode('|', $route->methods()) . ' ' . $route->uri())
            ->values()
            ->all();

        $this->assertSame([], $writeRoutes);
    }

    private function actingAsUser(): User
    {
        $user = (new User())->forceFill([
            'id' => 1,
            'name' => 'Lesion Viewer Route User',
            'email' => 'lesion-route-user@example.test',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
