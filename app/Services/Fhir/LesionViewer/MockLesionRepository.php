<?php

namespace App\Services\Fhir\LesionViewer;

class MockLesionRepository implements LesionRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->records();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $lesionId): ?array
    {
        foreach ($this->all() as $lesion) {
            if ($lesion['lesionId'] === $lesionId) {
                return $lesion;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function meta(): array
    {
        return [
            'readOnly' => true,
            'source' => 'mock-lesion-repository',
            'contract' => 'docs/fhir/lesion-viewer-data-contract.md',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function records(): array
    {
        return [
            [
                'lesionId' => 'lesion-001',
                'title' => '疑似神經相關病灶觀察',
                'status' => 'clinician-reviewed',
                'severity' => 'medium',
                'source' => 'AI Agent + clinician review',
                'subject' => [
                    'patientReference' => 'Patient/patient-001',
                    'displayId' => 'P-001',
                    'gender' => 'unknown',
                    'ageRange' => 'not-displayed',
                ],
                'summary' => '展示用摘要，僅描述已關聯的觀察資料與審核狀態。',
                'clinicalStatus' => 'active',
                'verificationStatus' => 'provisional',
                'review' => [
                    'reviewStatus' => 'clinician-reviewed',
                    'reviewedAt' => '2026-05-12T02:00:00Z',
                    'reviewedBy' => 'Practitioner/practitioner-001',
                ],
                'resources' => [
                    'observations' => ['Observation/obs-001', 'Observation/obs-002'],
                    'conditions' => ['Condition/condition-001'],
                    'diagnosticReports' => ['DiagnosticReport/report-001'],
                    'documents' => ['DocumentReference/doc-001'],
                    'consents' => ['Consent/consent-001'],
                    'encounters' => ['Encounter/encounter-001'],
                ],
                'lastUpdated' => '2026-05-12T02:15:00Z',
            ],
            [
                'lesionId' => 'lesion-002',
                'title' => '影像檢查待覆核病灶資料',
                'status' => 'pending-review',
                'severity' => 'low',
                'source' => 'AI Agent draft + pending clinician review',
                'subject' => [
                    'patientReference' => 'Patient/patient-002',
                    'displayId' => 'P-002',
                    'gender' => 'unknown',
                    'ageRange' => 'not-displayed',
                ],
                'summary' => '展示用摘要，標示資料仍待醫護覆核，僅供 read-only viewer 呈現。',
                'clinicalStatus' => 'inactive',
                'verificationStatus' => 'provisional',
                'review' => [
                    'reviewStatus' => 'pending-review',
                    'reviewedAt' => null,
                    'reviewedBy' => null,
                ],
                'resources' => [
                    'observations' => ['Observation/obs-010'],
                    'conditions' => ['Condition/condition-010'],
                    'diagnosticReports' => ['DiagnosticReport/report-010'],
                    'documents' => ['DocumentReference/doc-010', 'DocumentReference/doc-011'],
                    'consents' => ['Consent/consent-010'],
                    'encounters' => ['Encounter/encounter-010'],
                ],
                'lastUpdated' => '2026-05-12T03:30:00Z',
            ],
            [
                'lesionId' => 'lesion-003',
                'title' => '已簽核病灶資料檢視項目',
                'status' => 'signed-off',
                'severity' => 'high',
                'source' => 'clinician review',
                'subject' => [
                    'patientReference' => 'Patient/patient-003',
                    'displayId' => 'P-003',
                    'gender' => 'unknown',
                    'ageRange' => 'not-displayed',
                ],
                'summary' => '展示用摘要，呈現醫護簽核與 FHIR reference 關聯。',
                'clinicalStatus' => 'active',
                'verificationStatus' => 'confirmed',
                'review' => [
                    'reviewStatus' => 'signed-off',
                    'reviewedAt' => '2026-05-12T04:10:00Z',
                    'reviewedBy' => 'Practitioner/practitioner-003',
                ],
                'resources' => [
                    'observations' => ['Observation/obs-020', 'Observation/obs-021', 'Observation/obs-022'],
                    'conditions' => ['Condition/condition-020'],
                    'diagnosticReports' => ['DiagnosticReport/report-020'],
                    'documents' => ['DocumentReference/doc-020'],
                    'consents' => ['Consent/consent-020', 'Consent/consent-021'],
                    'encounters' => ['Encounter/encounter-020', 'Encounter/encounter-021'],
                ],
                'lastUpdated' => '2026-05-12T04:20:00Z',
            ],
        ];
    }
}
