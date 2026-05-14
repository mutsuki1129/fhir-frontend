<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LesionViewerUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('fhir.lesion_viewer_source', 'mock');
    }

    public function test_lesion_pages_do_not_render_write_or_misleading_clinical_labels(): void
    {
        $this->actingAsUser();

        foreach (['/lesions', '/lesions/lesion-001'] as $path) {
            $response = $this->get($path)->assertOk();

            foreach ($this->forbiddenLabels() as $label) {
                $response->assertDontSee($label, false);
            }
        }

        $this->assertStringContainsString(
            "{{ __('fhir.consent_references') }}",
            file_get_contents(resource_path('views/admin/lesions/show.blade.php')),
        );
    }

    public function test_homepage_renders_localized_readonly_entry_and_controls(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('Open Lesion Viewer')
            ->assertSee('aria-label="Theme"', false)
            ->assertSee('theme-icon-sun', false)
            ->assertSee('theme-icon-moon', false)
            ->assertSee('No lesion CRUD')
            ->assertSee('No formal ingestion')
            ->assertSee('No FHIR persistence')
            ->assertDontSee('fhir.home_', false);

        $this->withSession(['locale' => 'zh_TW'])
            ->get('/')
            ->assertOk()
            ->assertSee('開啟病灶檢視器')
            ->assertSee('aria-label="主題"', false)
            ->assertSee('theme-icon-sun', false)
            ->assertSee('theme-icon-moon', false)
            ->assertSee('不提供病灶 CRUD')
            ->assertSee('不進行正式匯入')
            ->assertSee('不寫入 FHIR')
            ->assertDontSee('fhir.home_', false);
    }

    public function test_sidebar_and_dashboard_include_lesion_viewer_entry(): void
    {
        $this->actingAsUser();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('FHIR Read-only Lesion Viewer')
            ->assertSee('Lesion Viewer')
            ->assertSee('href="' . route('lesions.index') . '"', false);

        $lesionPage = $this->get('/lesions')->assertOk();

        $this->assertStringContainsString('Lesion Viewer', $lesionPage->getContent());
        $this->assertStringContainsString('FHIR Metadata', $lesionPage->getContent());
        $this->assertStringContainsString('Clinical Data Display', $lesionPage->getContent());
    }

    public function test_fhir_metadata_page_and_sidebar_are_localized_for_zh_tw(): void
    {
        $this->withSession(['locale' => 'zh_TW']);
        $this->actingAsUser();

        $this->get('/fhir')
            ->assertOk()
            ->assertSee('臨床證據中繼資料')
            ->assertSee('只讀安全邊界')
            ->assertSee('FHIR 參照詳情')
            ->assertSee('開啟病灶檢視器')
            ->assertSee('支援檢視')
            ->assertSee('臨床資料展示')
            ->assertDontSee('Clinical Evidence Metadata')
            ->assertDontSee('Read-only Safety Boundary')
            ->assertDontSee('FHIR Reference Details')
            ->assertDontSee('Supporting views');
    }

    public function test_fhir_source_empty_state_is_read_only_and_not_misleading(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('search')->once()->with('DiagnosticReport')->andReturn([
                'resourceType' => 'Bundle',
                'type' => 'searchset',
                'entry' => [],
            ]);
        });

        $this->actingAsUser();

        $response = $this->get('/lesions')
            ->assertOk()
            ->assertSee('Displays read-only lesion evidence derived from FHIR DiagnosticReport metadata.')
            ->assertSee('No read-only lesion / clinical evidence data is available yet.')
            ->assertSee('this display remains empty instead of offering manual entry or frontend write workflow');

        foreach ($this->forbiddenLabels() as $label) {
            $response->assertDontSee($label, false);
        }
    }

    public function test_fhir_source_detail_displays_enrichment_sections_without_write_controls(): void
    {
        Config::set('fhir.lesion_viewer_source', 'fhir');

        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('read')->once()->with('DiagnosticReport', 'report-001')->andReturn([
                'resourceType' => 'DiagnosticReport',
                'id' => 'report-001',
                'status' => 'final',
                'code' => ['text' => 'Lesion review diagnostic report'],
                'subject' => [
                    'reference' => 'Patient/patient-001',
                    'display' => 'P-001',
                ],
                'encounter' => ['reference' => 'Encounter/encounter-001'],
                'result' => [
                    ['reference' => 'Observation/obs-001'],
                ],
                'performer' => [
                    ['display' => 'clinician-reviewed workflow'],
                ],
                'conclusion' => 'Evidence summary for review only; no clinical advice is persisted.',
                'issued' => '2026-05-12T00:00:00Z',
            ]);
            $mock->shouldReceive('read')->once()->with('Patient', 'patient-001')->andReturn([
                'resourceType' => 'Patient',
                'id' => 'patient-001',
                'identifier' => [['value' => 'P-001']],
                'gender' => 'female',
                'birthDate' => '1990-01-01',
            ]);
            $mock->shouldReceive('read')->once()->with('Observation', 'obs-001')->andReturn([
                'resourceType' => 'Observation',
                'id' => 'obs-001',
                'status' => 'final',
                'code' => ['text' => 'Body temperature observation'],
                'effectiveDateTime' => '2026-05-12T00:00:00Z',
                'valueQuantity' => [
                    'value' => 36.8,
                    'unit' => 'Cel',
                ],
            ]);
            $mock->shouldReceive('read')->once()->with('Encounter', 'encounter-001')->andReturn([
                'resourceType' => 'Encounter',
                'id' => 'encounter-001',
                'status' => 'finished',
                'period' => [
                    'start' => '2026-05-12T00:00:00Z',
                    'end' => '2026-05-12T00:30:00Z',
                ],
            ]);
        });

        $this->actingAsUser();

        $response = $this->get('/lesions/lesion-report-001')
            ->assertOk()
            ->assertSee('Subject metadata')
            ->assertSee('Observation references')
            ->assertSee('Encounter / Patient context')
            ->assertSee('FHIR resource references')
            ->assertSee('masked')
            ->assertSee('36.8 Cel');

        foreach ($this->forbiddenLabels() as $label) {
            $response->assertDontSee($label, false);
        }
    }

    /**
     * @return array<int, string>
     */
    private function forbiddenLabels(): array
    {
        return [
            'create',
            'edit',
            'delete',
            'upload',
            'diagnose',
            'infer',
            'treatment advice',
            'clinical advice',
            'automatic diagnosis',
            '診斷結果',
            'AI 判讀',
            '治療建議',
        ];
    }

    private function actingAsUser(): User
    {
        $user = (new User())->forceFill([
            'id' => 1,
            'name' => 'Lesion Viewer UI User',
            'email' => 'lesion-ui-user@example.test',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
