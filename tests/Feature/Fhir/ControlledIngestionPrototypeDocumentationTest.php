<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class ControlledIngestionPrototypeDocumentationTest extends TestCase
{
    public function test_phase_10b_documents_describe_dev_only_mock_no_write_boundary(): void
    {
        $content = $this->readDocs([
            'docs/fhir/dev-only-mock-ingestion-prototype.md',
            'docs/fhir/controlled-ingestion-prototype-planning.md',
            'docs/fhir/no-write-fhir-boundary.md',
            'docs/fhir/manual-review-queue-mock-plan.md',
            'docs/fhir/validation-result-mock-plan.md',
            'docs/fhir/candidate-resource-staging-plan.md',
            'docs/fhir/fhir-docs-index.md',
            'docs/README.md',
            'readme.md',
        ]);

        foreach ([
            'Phase 10B 是 dev-only mock prototype',
            'feature flag default disabled',
            'no real PHI',
            'no production FHIR Server',
            'no direct FHIR write',
            'manual review queue mock only',
            'validation result mock only',
            'candidate preview only',
            'not production ingestion',
            'not AI Agent runtime',
            'not CDS runtime',
            'not SMART production',
            'manual review queue mock 不等於 signed-off',
            'validation result mock 不等於 live HAPI `$validate`',
            'candidate preview 不等於 persisted FHIR Resource',
        ] as $required) {
            $this->assertStringContainsString($required, $content);
        }
    }

    public function test_phase_10b_evidence_package_contains_required_files(): void
    {
        $base = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-10b-dev-only-mock-ingestion-prototype-20260512');

        foreach ([
            'README.md',
            'working-tree-snapshot.md',
            'implementation-summary.md',
            'feature-flag-results.md',
            'mock-payload-parser-results.md',
            'validation-result-mock-results.md',
            'manual-review-queue-mock-results.md',
            'candidate-preview-results.md',
            'ui-results.md',
            'api-contract-results.md',
            'route-safety-results.md',
            'runtime-safety-results.md',
            'no-write-results.md',
            'regression-test-results.md',
            'documentation-results.md',
            'deferred-issues.md',
            'safety-boundary.md',
            'next-phase-plan.md',
        ] as $file) {
            $this->assertFileExists($base.'/'.$file);
        }
    }

    /**
     * @param array<int, string> $paths
     */
    private function readDocs(array $paths): string
    {
        return collect($paths)
            ->map(fn (string $path): string => (string) file_get_contents(base_path($path)))
            ->implode("\n");
    }
}
