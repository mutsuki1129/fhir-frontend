<?php

namespace Tests\Feature\Fhir;

use App\Http\Middleware\EnsureFhirFrontendReadOnly;
use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FrontendReadOnlyRouteGuardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.frontend_read_only', true);
        Config::set('services.fhir.phase1_enabled', true);
        Config::set('services.fhir.smart_enabled', false);
    }

    /**
     * @dataProvider clinicalWriteRouteProvider
     */
    public function test_read_only_mode_rejects_clinical_write_routes(string $method, string $uri): void
    {
        $this->actingAs($this->user());

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();
            $mock->shouldReceive('read')->never();
            $mock->shouldReceive('search')->never();
        });

        $this->json($method, $uri, $this->payloadFor($uri))
            ->assertForbidden()
            ->assertJson([
                'message' => EnsureFhirFrontendReadOnly::MESSAGE,
            ]);
    }

    public function test_read_only_mode_can_be_disabled_by_config_for_guarded_routes(): void
    {
        Config::set('fhir.frontend_read_only', false);
        $this->actingAs($this->user());

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('create')
                ->once()
                ->with('Patient', \Mockery::type('array'))
                ->andReturn(['resourceType' => 'Patient', 'id' => 'patient-created-001']);
        });

        $this->post('/pasiens', [
            'name' => 'Config Open Patient',
            'email' => 'config-open-patient@example.test',
            'phone_number' => '0900000000',
            'birth_date' => '1990-01-01',
            'gender' => 'unknown',
            'address' => 'Read-only guard disabled for this test.',
        ])->assertRedirect('/pasiens');
    }

    public function test_read_only_mode_keeps_get_list_pages_available(): void
    {
        $this->actingAs($this->user());

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')
                ->once()
                ->with('Patient', ['_count' => 200])
                ->andReturn(['entry' => [[
                    'resource' => [
                        'resourceType' => 'Patient',
                        'id' => 'patient-readonly-guard-001',
                        'name' => [['text' => 'Read-only Guard Patient']],
                    ],
                ]]]);
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();
        });

        $this->get('/pasiens')
            ->assertOk()
            ->assertSee('Read-only Guard Patient')
            ->assertDontSee('/pasiens/create', false)
            ->assertDontSee('/edit-pasien/', false);
    }

    public static function clinicalWriteRouteProvider(): array
    {
        return [
            'Patient create' => ['POST', '/pasiens'],
            'Patient update' => ['PATCH', '/edit-pasien/patient-001'],
            'Patient delete' => ['DELETE', '/pasiens/patient-001'],
            'Practitioner create' => ['POST', '/dokters'],
            'Practitioner update' => ['PATCH', '/edit-dokter/practitioner-001'],
            'Practitioner delete' => ['DELETE', '/dokters/practitioner-001'],
            'Observation create' => ['POST', '/rekam'],
            'Observation update' => ['PATCH', '/rekam/obs-001'],
            'Observation delete' => ['DELETE', '/rekam/obs-001'],
            'DocumentReference binary upload' => ['POST', '/document-references/doc-001/binary'],
            'MedicationRequest create' => ['POST', '/medication-requests'],
        ];
    }

    private function payloadFor(string $uri): array
    {
        if (str_contains($uri, 'medication-requests')) {
            return [
                'patient_id' => 'patient-001',
                'encounter_id' => 'encounter-001',
                'medication_display' => 'Read-only metadata',
            ];
        }

        if (str_contains($uri, 'rekam')) {
            return [
                'pasien' => 'patient-001',
                'suhu' => '37.2',
            ];
        }

        return [
            'name' => 'Read-only Guard',
            'email' => 'readonly-guard@example.test',
            'phone_number' => '0900000000',
        ];
    }

    private function user(): User
    {
        return (new User())->forceFill([
            'id' => 1,
            'name' => 'Read-only Guard User',
            'email' => 'readonly-guard-user@example.test',
        ]);
    }
}
