<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class ControlledIngestionPlanningDocumentationTest extends TestCase
{
    public function test_phase_10a_planning_documents_exist_and_are_indexed(): void
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

        $this->assertStringContainsString('docs/fhir/controlled-ingestion-prototype-planning.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/phase-10b-readiness-checklist.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/', $combinedRootIndexes);
    }

    public function test_phase_10a_documents_define_required_planning_boundaries(): void
    {
        $content = $this->phase10aContent();

        foreach ([
            'Phase 10A is planning',
            'Phase 10A is not runtime',
            'mock-only',
            'Dev-only',
            'No real PHI',
            'No production FHIR Server',
            'No direct FHIR write',
            'Manual Review Queue Mock 不等於 clinician sign-off',
            'Validation result mock 不等於 live HAPI `$validate`',
            'Candidate preview 不等於 persisted FHIR Resource',
            'Checklist completed 不等於 production approval',
        ] as $needle) {
            $this->assertStringContainsString($needle, $content);
        }
    }

    public function test_phase_10a_documents_avoid_misleading_activation_language(): void
    {
        $content = strtolower($this->phase10aContent());

        foreach ([
            'gateway ingestion enabled',
            'ai agent runtime enabled',
            'validation runtime enabled',
            'fhir write pipeline enabled',
            'production approval granted',
            'smart production activated',
            'cds runtime enabled',
            'live hapi $validate enabled',
            'ai confirmed',
            'ai diagnosis',
            'signed-off without review',
            'candidate persisted to fhir',
            'real phi accepted',
            'documentreference binary exposed',
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
            'docs/fhir/controlled-ingestion-prototype-planning.md',
            'docs/fhir/dev-only-mock-ingestion-scope.md',
            'docs/fhir/mock-ingestion-payload-policy.md',
            'docs/fhir/no-write-fhir-boundary.md',
            'docs/fhir/manual-review-queue-mock-plan.md',
            'docs/fhir/validation-result-mock-plan.md',
            'docs/fhir/candidate-resource-staging-plan.md',
            'docs/fhir/controlled-ingestion-rollback-disablement-plan.md',
            'docs/fhir/controlled-ingestion-test-data-policy.md',
            'docs/fhir/controlled-ingestion-prototype-exit-criteria.md',
            'docs/fhir/phase-10b-readiness-checklist.md',
            'docs/fhir/evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/README.md',
        ];
    }

    /**
     * @return list<string>
     */
    private function indexNeedles(): array
    {
        return [
            '[controlled-ingestion-prototype-planning.md](controlled-ingestion-prototype-planning.md)',
            '[dev-only-mock-ingestion-scope.md](dev-only-mock-ingestion-scope.md)',
            '[mock-ingestion-payload-policy.md](mock-ingestion-payload-policy.md)',
            '[no-write-fhir-boundary.md](no-write-fhir-boundary.md)',
            '[manual-review-queue-mock-plan.md](manual-review-queue-mock-plan.md)',
            '[validation-result-mock-plan.md](validation-result-mock-plan.md)',
            '[candidate-resource-staging-plan.md](candidate-resource-staging-plan.md)',
            '[controlled-ingestion-rollback-disablement-plan.md](controlled-ingestion-rollback-disablement-plan.md)',
            '[controlled-ingestion-test-data-policy.md](controlled-ingestion-test-data-policy.md)',
            '[controlled-ingestion-prototype-exit-criteria.md](controlled-ingestion-prototype-exit-criteria.md)',
            '[phase-10b-readiness-checklist.md](phase-10b-readiness-checklist.md)',
            '[evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/](evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/)',
        ];
    }

    private function phase10aContent(): string
    {
        return collect($this->documentPaths())
            ->map(fn (string $path): string => file_get_contents(base_path($path)))
            ->implode("\n");
    }
}

