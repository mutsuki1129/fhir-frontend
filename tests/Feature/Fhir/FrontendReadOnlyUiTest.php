<?php

namespace Tests\Feature\Fhir;

use App\Models\User;
use App\Services\Fhir\FhirApiClient;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FrontendReadOnlyUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.fhir.phase1_enabled', true);
        Config::set('services.fhir.smart_enabled', false);
        Config::set('fhir.gateway_report_only', true);
        Config::set('fhir.gateway_enforce_read', false);
        Config::set('fhir.gateway_enforce_write', false);
        Config::set('fhir.gateway_enforce_medication_request_write', false);
        Config::set('fhir.policy_decision_store', 'log');
    }

    public function test_patient_list_renders_read_only_without_create_edit_or_delete_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/pasiens')
            ->assertOk()
            ->assertSee('Read Only Patient')
            ->assertSee('本頁僅供資料展示')
            ->assertDontSee('/pasiens/create', false)
            ->assertDontSee('/edit-pasien/', false)
            ->assertDontSee('action="http://localhost:8080/pasiens/patient-readonly-001"', false)
            ->assertDontSee('ui.patients.add')
            ->assertDontSee('data-toggle-icon="edit"', false)
            ->assertDontSee('data-toggle-icon="delete"', false);
    }

    public function test_rekam_list_renders_read_only_without_create_edit_delete_or_upload_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/rekam')
            ->assertOk()
            ->assertSee('Read Only Patient')
            ->assertSee('Observation/obs-readonly-001')
            ->assertSee('本頁僅供資料展示')
            ->assertDontSee('/rekam/create', false)
            ->assertDontSee('/rekam/obs-readonly-001/edit', false)
            ->assertDontSee('action="http://localhost:8080/rekam/obs-readonly-001"', false)
            ->assertDontSee('data-upload-file', false)
            ->assertDontSee('data-upload-start', false);
    }

    public function test_rekam_patient_grouped_view_renders_read_only_without_create_or_edit_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/rekam/pasien')
            ->assertOk()
            ->assertSee('Read Only Patient')
            ->assertSee('37.2 C')
            ->assertSee('本頁僅供資料展示')
            ->assertDontSee('/rekam/create', false)
            ->assertDontSee('/rekam/obs-readonly-001/edit', false)
            ->assertDontSee('data-upload-start', false);
    }

    public function test_rekam_practitioner_grouped_view_renders_read_only_without_create_or_edit_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/rekam/dokter')
            ->assertOk()
            ->assertSee('Read Only Practitioner')
            ->assertSee('Read Only Patient')
            ->assertSee('本頁僅供資料展示')
            ->assertDontSee('/rekam/create', false)
            ->assertDontSee('/rekam/obs-readonly-001/edit', false)
            ->assertDontSee('data-upload-start', false);
    }

    public function test_practitioner_list_renders_read_only_without_create_edit_or_delete_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/dokters')
            ->assertOk()
            ->assertSee('Read Only Practitioner')
            ->assertSee('本頁僅供資料展示')
            ->assertDontSee('/dokters/create', false)
            ->assertDontSee('/edit-dokter/', false)
            ->assertDontSee('action="http://localhost:8080/dokters/practitioner-readonly-001"', false);
    }

    public function test_medication_request_list_and_detail_remain_read_only_without_create_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/medication-requests')
            ->assertOk()
            ->assertSee('MedicationRequest')
            ->assertSee('medreq-readonly-001')
            ->assertDontSee('/medication-requests" method="POST"', false);

        $this->get('/medication-requests/medreq-readonly-001')
            ->assertOk()
            ->assertSee('MedicationRequest')
            ->assertSee('Read-only medication metadata')
            ->assertDontSee('/medication-requests" method="POST"', false);
    }

    public function test_document_reference_pages_remain_metadata_read_only_without_upload_actions(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/document-references')
            ->assertOk()
            ->assertSee('DocumentReference')
            ->assertSee('docref-readonly-001')
            ->assertDontSee('/document-references/docref-readonly-001/binary', false)
            ->assertDontSee('data-upload-file', false)
            ->assertDontSee('data-upload-start', false);

        $this->get('/document-references/docref-readonly-001')
            ->assertOk()
            ->assertSee('DocumentReference')
            ->assertSee('Read-only document metadata')
            ->assertSee('/document-references/docref-readonly-001/download', false)
            ->assertDontSee('/document-references/docref-readonly-001/binary', false)
            ->assertDontSee('data-upload-file', false)
            ->assertDontSee('data-upload-start', false);
    }

    public function test_encounter_and_diagnostic_report_read_only_pages_still_render(): void
    {
        $this->actingAsUser();
        $this->mockReadOnlyFhirClient();

        $this->get('/encounters')
            ->assertOk()
            ->assertSee('encounter-readonly-001');

        $this->get('/encounters/encounter-readonly-001')
            ->assertOk()
            ->assertSee('Encounter encounter-readonly-001');

        $this->get('/diagnostic-reports')
            ->assertOk()
            ->assertSee('diag-readonly-001');

        $this->get('/diagnostic-reports/diag-readonly-001')
            ->assertOk()
            ->assertSee('DiagnosticReport')
            ->assertSee('diag-readonly-001');
    }

    public function test_read_only_navigation_translations_do_not_expose_crud_positioning(): void
    {
        $zh = require base_path('lang/zh_TW/ui.php');
        $en = require base_path('lang/en/ui.php');

        foreach ([
            'patients.add',
            'patients.edit_title',
            'patients.delete_label',
            'doctors.add',
            'rekam.create_title',
            'rekam.edit_title',
            'rekam.delete_label',
            'rekam.attachment_upload_label',
            'sidebar.rekam_add',
            'sidebar.rekam_edit',
        ] as $key) {
            $this->assertArrayHasKey($key, $zh);
            $this->assertArrayHasKey($key, $en);
        }

        foreach ([
            '新增病患',
            '編輯病患',
            '刪除病患',
            '新增醫師',
            '新增病歷',
            '編輯病歷',
            '刪除病歷',
            '附件上傳',
        ] as $legacyLabel) {
            $this->assertStringNotContainsString($legacyLabel, implode("\n", [
                $zh['patients.add'],
                $zh['patients.edit_title'],
                $zh['patients.delete_label'],
                $zh['doctors.add'],
                $zh['rekam.create_title'],
                $zh['rekam.edit_title'],
                $zh['rekam.delete_label'],
                $zh['rekam.attachment_upload_label'],
                $zh['sidebar.rekam_add'],
                $zh['sidebar.rekam_edit'],
            ]));
        }

        foreach ([
            'Add Patient',
            'Edit Patient',
            'Delete patient',
            'Add Doctor',
            'Add Medical Record',
            'Edit Medical Record',
            'Delete medical record',
            'Attachment Upload',
        ] as $legacyLabel) {
            $this->assertStringNotContainsString($legacyLabel, implode("\n", [
                $en['patients.add'],
                $en['patients.edit_title'],
                $en['patients.delete_label'],
                $en['doctors.add'],
                $en['rekam.create_title'],
                $en['rekam.edit_title'],
                $en['rekam.delete_label'],
                $en['rekam.attachment_upload_label'],
                $en['sidebar.rekam_add'],
                $en['sidebar.rekam_edit'],
            ]));
        }
    }

    private function actingAsUser(): User
    {
        $user = (new User())->forceFill([
            'id' => 1,
            'name' => 'Read-only UI User',
            'email' => 'readonly-ui-user@example.test',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function mockReadOnlyFhirClient(): void
    {
        $this->mock(FhirApiClient::class, function ($mock): void {
            $mock->shouldReceive('create')->never();
            $mock->shouldReceive('update')->never();
            $mock->shouldReceive('delete')->never();

            $mock->shouldReceive('search')
                ->byDefault()
                ->andReturnUsing(function (string $resourceType, array $params = []): array {
                    return match ($resourceType) {
                        'Patient' => $this->patientBundle($params),
                        'Practitioner' => $this->practitionerBundle(),
                        'Observation' => $this->observationBundle($params),
                        'Encounter' => $this->encounterBundle($params),
                        'Condition' => ['entry' => []],
                        'DocumentReference' => $this->documentReferenceBundle($params),
                        'MedicationRequest' => $this->medicationRequestBundle(),
                        'DiagnosticReport' => $this->diagnosticReportBundle(),
                        default => ['entry' => []],
                    };
                });

            $mock->shouldReceive('read')
                ->byDefault()
                ->andReturnUsing(function (string $resourceType, string $id): array {
                    return match ($resourceType) {
                        'Encounter' => $this->encounterResource($id),
                        'DocumentReference' => $this->documentReferenceResource($id),
                        'MedicationRequest' => $this->medicationRequestResource($id),
                        'DiagnosticReport' => $this->diagnosticReportResource($id),
                        'Observation' => $this->observationResource($id),
                        default => ['resourceType' => $resourceType, 'id' => $id],
                    };
                });
        });
    }

    private function patientBundle(array $params = []): array
    {
        return ['entry' => [
            ['resource' => $this->patientResource((string) ($params['_id'] ?? 'patient-readonly-001'))],
        ]];
    }

    private function practitionerBundle(): array
    {
        return ['entry' => [
            ['resource' => $this->practitionerResource()],
        ]];
    }

    private function observationBundle(array $params = []): array
    {
        if (($params['encounter'] ?? null) === 'Encounter/encounter-readonly-001') {
            return ['entry' => [
                ['resource' => $this->observationResource('obs-readonly-001')],
            ]];
        }

        return ['entry' => [
            ['resource' => $this->observationResource('obs-readonly-001')],
            ['resource' => $this->patientResource()],
            ['resource' => $this->practitionerResource()],
        ]];
    }

    private function encounterBundle(array $params = []): array
    {
        $patientId = (string) ($params['patient'] ?? 'patient-readonly-001');

        return ['entry' => [
            ['resource' => $this->encounterResource('encounter-readonly-001', $patientId)],
        ]];
    }

    private function documentReferenceBundle(array $params = []): array
    {
        return ['entry' => [
            ['resource' => $this->documentReferenceResource('docref-readonly-001')],
        ]];
    }

    private function medicationRequestBundle(): array
    {
        return ['entry' => [
            ['resource' => $this->medicationRequestResource('medreq-readonly-001')],
        ]];
    }

    private function diagnosticReportBundle(): array
    {
        return ['entry' => [
            ['resource' => $this->diagnosticReportResource('diag-readonly-001')],
        ]];
    }

    private function patientResource(string $id = 'patient-readonly-001'): array
    {
        return [
            'resourceType' => 'Patient',
            'id' => $id,
            'name' => [['text' => 'Read Only Patient', 'family' => 'Patient', 'given' => ['Read Only']]],
            'telecom' => [
                ['system' => 'email', 'value' => 'readonly-patient@example.test'],
                ['system' => 'phone', 'value' => '0900000000'],
            ],
            'gender' => 'female',
            'birthDate' => '1990-01-01',
        ];
    }

    private function practitionerResource(): array
    {
        return [
            'resourceType' => 'Practitioner',
            'id' => 'practitioner-readonly-001',
            'name' => [['text' => 'Read Only Practitioner', 'family' => 'Practitioner', 'given' => ['Read Only']]],
            'telecom' => [
                ['system' => 'email', 'value' => 'readonly-practitioner@example.test'],
                ['system' => 'phone', 'value' => '0911111111'],
            ],
        ];
    }

    private function observationResource(string $id): array
    {
        return [
            'resourceType' => 'Observation',
            'id' => $id,
            'status' => 'final',
            'code' => [
                'coding' => [[
                    'system' => 'http://loinc.org',
                    'code' => '8310-5',
                    'display' => 'Body temperature',
                ]],
                'text' => 'Body temperature',
            ],
            'subject' => ['reference' => 'Patient/patient-readonly-001', 'display' => 'Read Only Patient'],
            'performer' => [['reference' => 'Practitioner/practitioner-readonly-001', 'display' => 'Read Only Practitioner']],
            'encounter' => ['reference' => 'Encounter/encounter-readonly-001'],
            'effectiveDateTime' => '2026-05-12T09:00:00+08:00',
            'valueQuantity' => [
                'value' => 37.2,
                'unit' => 'C',
                'system' => 'http://unitsofmeasure.org',
                'code' => 'Cel',
            ],
        ];
    }

    private function encounterResource(string $id, string $patientId = 'patient-readonly-001'): array
    {
        return [
            'resourceType' => 'Encounter',
            'id' => $id,
            'status' => 'finished',
            'class' => ['code' => 'AMB', 'display' => 'Ambulatory'],
            'subject' => ['reference' => "Patient/{$patientId}", 'display' => 'Read Only Patient'],
            'period' => ['start' => '2026-05-12T08:30:00+08:00'],
        ];
    }

    private function documentReferenceResource(string $id): array
    {
        return [
            'resourceType' => 'DocumentReference',
            'id' => $id,
            'status' => 'current',
            'subject' => ['reference' => 'Patient/patient-readonly-001'],
            'date' => '2026-05-12T09:10:00+08:00',
            'type' => ['text' => 'Read-only document metadata'],
            'content' => [[
                'attachment' => [
                    'contentType' => 'application/pdf',
                    'title' => 'Read-only document metadata',
                    'url' => 'Binary/binary-readonly-001',
                ],
            ]],
        ];
    }

    private function medicationRequestResource(string $id): array
    {
        return [
            'resourceType' => 'MedicationRequest',
            'id' => $id,
            'status' => 'active',
            'intent' => 'order',
            'subject' => ['reference' => 'Patient/patient-readonly-001'],
            'encounter' => ['reference' => 'Encounter/encounter-readonly-001'],
            'medicationCodeableConcept' => ['text' => 'Read-only medication metadata'],
            'authoredOn' => '2026-05-12T09:15:00+08:00',
            'requester' => ['reference' => 'Practitioner/practitioner-readonly-001'],
            'dosageInstruction' => [['text' => 'Read-only medication metadata']],
            'note' => [['text' => 'Read-only medication metadata']],
        ];
    }

    private function diagnosticReportResource(string $id): array
    {
        return [
            'resourceType' => 'DiagnosticReport',
            'id' => $id,
            'status' => 'final',
            'subject' => ['reference' => 'Patient/patient-readonly-001'],
            'encounter' => ['reference' => 'Encounter/encounter-readonly-001'],
            'code' => ['text' => 'Read-only diagnostic metadata'],
            'effectiveDateTime' => '2026-05-12T09:20:00+08:00',
        ];
    }
}
