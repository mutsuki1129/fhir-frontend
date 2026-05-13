<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class GovernanceReviewDocumentationTest extends TestCase
{
    public function test_phase_9c_governance_documents_exist_and_are_indexed(): void
    {
        foreach ($this->documentPaths() as $path) {
            $this->assertFileExists(base_path($path));
        }

        $fhirIndex = file_get_contents(base_path('docs/fhir/fhir-docs-index.md'));
        $docsIndex = file_get_contents(base_path('docs/README.md'));
        $readme = file_get_contents(base_path('README.md'));
        $combinedRootIndexes = $docsIndex . "\n" . $readme;

        foreach ($this->indexNeedles() as $needle) {
            $this->assertStringContainsString($needle, $fhirIndex);
        }

        $this->assertStringContainsString('docs/fhir/governance-review-draft.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/smart-production-activation-policy-draft.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/', $combinedRootIndexes);
    }

    public function test_phase_9c_documents_define_required_governance_boundaries(): void
    {
        $content = $this->phase9cContent();

        foreach ([
            'Phase 9C',
            'Governance Review Draft',
            'Phase 9C 不啟用 SMART production',
            'Phase 9C 不啟用 CDS runtime',
            'Phase 9C 不新增 Gateway ingestion runtime',
            'AI-generated 不等於 clinician-reviewed',
            'validation-passed-candidate：格式 / 驗證候選，不等於簽核',
            'DiagnosticReport.final 不等於 signed-off',
            'Condition.provisional 不等於 confirmed',
            'Consent.active 不代表醫療結果',
            '不得直接暴露 binary content',
            'Patient 不顯示完整敏感資料',
            'OperationOutcome 不代表醫療結論',
            'Checklist completed 不等於 production approval',
        ] as $needle) {
            $this->assertStringContainsString($needle, $content);
        }
    }

    public function test_phase_9c_documents_avoid_misleading_activation_language(): void
    {
        $content = strtolower($this->phase9cContent());

        foreach ([
            'smart production activated in phase 9c',
            'smart launch is enabled',
            'cds runtime is enabled',
            'cds hooks are enabled',
            'gateway ingestion is enabled',
            'ai agent runtime is enabled',
            'validation runtime is enabled',
            'fhir write pipeline is enabled',
            'production approval granted in phase 9c',
            'ai confirmed as reviewed',
            'ai diagnosis is accepted',
            'signed-off without review is allowed',
            'operationoutcome as clinical conclusion',
            'documentreference binary is exposed',
            'consent is a medical result',
        ] as $forbiddenPhrase) {
            $this->assertStringNotContainsString($forbiddenPhrase, $content);
        }
    }

    /**
     * @return list<string>
     */
    private function documentPaths(): array
    {
        return [
            'docs/fhir/governance-review-draft.md',
            'docs/fhir/smart-production-activation-policy-draft.md',
            'docs/fhir/cds-runtime-governance-policy-draft.md',
            'docs/fhir/document-reference-protected-download-policy-draft.md',
            'docs/fhir/consent-ecsu-signoff-governance-draft.md',
            'docs/fhir/patient-display-policy-draft.md',
            'docs/fhir/ai-generated-review-policy-draft.md',
            'docs/fhir/fhir-validation-governance-policy-draft.md',
            'docs/fhir/gateway-ingestion-governance-policy-draft.md',
            'docs/fhir/audit-provenance-logging-policy-draft.md',
            'docs/fhir/controlled-ingestion-readiness-checklist.md',
            'docs/fhir/evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/README.md',
        ];
    }

    /**
     * @return list<string>
     */
    private function indexNeedles(): array
    {
        return [
            '[governance-review-draft.md](governance-review-draft.md)',
            '[smart-production-activation-policy-draft.md](smart-production-activation-policy-draft.md)',
            '[cds-runtime-governance-policy-draft.md](cds-runtime-governance-policy-draft.md)',
            '[document-reference-protected-download-policy-draft.md](document-reference-protected-download-policy-draft.md)',
            '[consent-ecsu-signoff-governance-draft.md](consent-ecsu-signoff-governance-draft.md)',
            '[patient-display-policy-draft.md](patient-display-policy-draft.md)',
            '[ai-generated-review-policy-draft.md](ai-generated-review-policy-draft.md)',
            '[fhir-validation-governance-policy-draft.md](fhir-validation-governance-policy-draft.md)',
            '[gateway-ingestion-governance-policy-draft.md](gateway-ingestion-governance-policy-draft.md)',
            '[audit-provenance-logging-policy-draft.md](audit-provenance-logging-policy-draft.md)',
            '[controlled-ingestion-readiness-checklist.md](controlled-ingestion-readiness-checklist.md)',
            '[evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/](evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/)',
        ];
    }

    private function phase9cContent(): string
    {
        return collect($this->documentPaths())
            ->map(fn (string $path): string => file_get_contents(base_path($path)))
            ->implode("\n");
    }
}
