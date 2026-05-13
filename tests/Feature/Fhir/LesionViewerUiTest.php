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
            '同意與簽核參照（僅供檢視）',
            file_get_contents(resource_path('views/admin/lesions/show.blade.php')),
        );
    }

    public function test_sidebar_and_dashboard_include_lesion_viewer_entry(): void
    {
        $this->actingAsUser();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('FHIR Read-only Lesion Viewer')
            ->assertSee('病灶資料總覽')
            ->assertSee('href="' . route('lesions.index') . '"', false);

        $lesionPage = $this->get('/lesions')->assertOk();

        $this->assertStringContainsString('病灶資料總覽', $lesionPage->getContent());
        $this->assertStringContainsString('FHIR legacy read-only view', $lesionPage->getContent());
        $this->assertStringContainsString('輔助資料 / Rekam', $lesionPage->getContent());
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
            ->assertSee('目前顯示的是 FHIR DiagnosticReport 聚合後的只讀病灶展示資料。')
            ->assertSee('目前沒有可顯示的 read-only lesion / clinical evidence data。')
            ->assertSee('此頁只展示 server / FHIR 提供的資料；沒有資料時不會在此頁產生新資料、檔案匯入或臨床寫入流程。');

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
                'code' => ['text' => '病灶相關臨床報告'],
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
                'conclusion' => '展示用摘要，不包含自動診斷或治療建議。',
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
                'code' => ['text' => '體溫觀察'],
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
            ->assertSee('Subject 摘要')
            ->assertSee('觀察資料摘要')
            ->assertSee('互動 / 觀察事件')
            ->assertSee('FHIR 來源參照')
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
            'AI 判定',
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
