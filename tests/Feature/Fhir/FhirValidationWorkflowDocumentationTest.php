<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class FhirValidationWorkflowDocumentationTest extends TestCase
{
    public function test_phase_9b_validation_documents_exist_and_are_indexed(): void
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

        $this->assertStringContainsString('docs/fhir/fhir-validation-workflow-draft.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/contracts/fhir-validation-result.schema.json', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/', $combinedRootIndexes);
    }

    public function test_validation_result_schema_draft_contains_required_sections(): void
    {
        $schemaPath = base_path('docs/fhir/contracts/fhir-validation-result.schema.json');
        $schema = json_decode(file_get_contents($schemaPath), true, 512, JSON_THROW_ON_ERROR);
        $properties = $schema['properties'] ?? [];

        foreach ([
            'schemaVersion',
            'validationId',
            'messageId',
            'correlationId',
            'status',
            'outcome',
            'readOnly',
            'runtime',
            'candidateResources',
            'errors',
            'review',
        ] as $key) {
            $this->assertArrayHasKey($key, $properties);
        }

        $this->assertSame('0.1-draft', $properties['schemaVersion']['const'] ?? null);
        $this->assertSame(true, $properties['readOnly']['const'] ?? null);
        $this->assertContains('not-enabled-in-phase-9b', $properties['runtime']['enum'] ?? []);
    }

    public function test_phase_9b_documents_define_non_runtime_validation_boundaries(): void
    {
        $content = $this->phase9bContent();

        foreach ([
            'Phase 9B',
            'FHIR Validation Workflow Draft',
            'validation runtime',
            'live HAPI `$validate`',
            'FHIR write path',
            'production validation service',
            '不啟用',
            '不執行',
            '不寫入 FHIR Server',
            'validation-passed-candidate',
            'DiagnosticReport.final',
            'Condition.provisional',
            'Consent.active',
            'AI-generated',
            'clinician-reviewed',
            'signed-off',
            '明確簽核證據',
        ] as $needle) {
            $this->assertStringContainsString($needle, $content);
        }
    }

    public function test_phase_9b_documents_avoid_misleading_activation_language(): void
    {
        $content = strtolower($this->phase9bContent());

        foreach ([
            'live hapi $validate is enabled',
            'validation runtime is enabled',
            'fhir write pipeline is enabled',
            'gateway runtime is enabled',
            'ai agent runtime is enabled',
            'cds runtime is enabled',
            'smart production is activated',
            'ai confirmed',
            'ai diagnosis',
            'signed-off without review',
            'production validation service is enabled',
            'production ingestion is enabled',
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
            'docs/fhir/fhir-validation-workflow-draft.md',
            'docs/fhir/fhir-validation-error-taxonomy.md',
            'docs/fhir/fhir-profile-ig-draft-boundary.md',
            'docs/fhir/fhir-validation-result-contract.md',
            'docs/fhir/contracts/fhir-validation-result.schema.json',
            'docs/fhir/manual-review-gate-policy.md',
            'docs/fhir/future-hapi-validate-boundary.md',
            'docs/fhir/evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/README.md',
        ];
    }

    /**
     * @return list<string>
     */
    private function indexNeedles(): array
    {
        return [
            '[fhir-validation-workflow-draft.md](fhir-validation-workflow-draft.md)',
            '[fhir-validation-error-taxonomy.md](fhir-validation-error-taxonomy.md)',
            '[fhir-profile-ig-draft-boundary.md](fhir-profile-ig-draft-boundary.md)',
            '[fhir-validation-result-contract.md](fhir-validation-result-contract.md)',
            '[contracts/fhir-validation-result.schema.json](contracts/fhir-validation-result.schema.json)',
            '[manual-review-gate-policy.md](manual-review-gate-policy.md)',
            '[future-hapi-validate-boundary.md](future-hapi-validate-boundary.md)',
            '[evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/](evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/)',
        ];
    }

    private function phase9bContent(): string
    {
        return collect($this->documentPaths())
            ->map(fn (string $path): string => file_get_contents(base_path($path)))
            ->implode("\n");
    }
}
