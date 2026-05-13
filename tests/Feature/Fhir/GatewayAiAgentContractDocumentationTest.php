<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class GatewayAiAgentContractDocumentationTest extends TestCase
{
    public function test_phase_9a_contract_documents_exist_and_are_indexed(): void
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

        $this->assertStringContainsString('docs/fhir/gateway-ai-agent-adapter-contract.md', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/contracts/gateway-ai-agent-payload.schema.json', $combinedRootIndexes);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/', $combinedRootIndexes);
    }

    public function test_schema_draft_contains_required_contract_sections(): void
    {
        $schemaPath = base_path('docs/fhir/contracts/gateway-ai-agent-payload.schema.json');
        $schema = json_decode(file_get_contents($schemaPath), true, 512, JSON_THROW_ON_ERROR);
        $properties = $schema['properties'] ?? [];

        foreach (['schemaVersion', 'sourceSystem', 'trust', 'payload'] as $key) {
            $this->assertArrayHasKey($key, $properties);
        }

        $this->assertSame('0.1-draft', $properties['schemaVersion']['const'] ?? null);
        $this->assertSame(
            ['ai-generated', 'ai-suggested', 'clinician-observed', 'clinician-reviewed', 'signed-off'],
            $properties['trust']['properties']['dataOrigin']['enum'] ?? []
        );
    }

    public function test_phase_9a_documents_define_non_runtime_safety_boundaries(): void
    {
        $content = $this->phase9aContent();

        $this->assertStringContainsString('Phase 9A 是 Gateway / AI Agent Adapter Contract Draft', $content);
        $this->assertStringContainsString('這不是 Gateway runtime', $content);
        $this->assertStringContainsString('這不是 AI Agent runtime', $content);
        $this->assertStringContainsString('這不是 FHIR write pipeline', $content);
        $this->assertStringContainsString('不寫入 FHIR Server', $content);
        $this->assertStringContainsString('不啟用 runtime', $content);
        $this->assertStringContainsString('ai-generated 不等於 clinician-reviewed', $content);
        $this->assertStringContainsString('ai-suggested 不等於 signed-off', $content);
        $this->assertStringContainsString('signed-off 需要明確簽核證據', $content);
        $this->assertStringContainsString('DiagnosticReport 不等於自動診斷', $content);
        $this->assertStringContainsString('Consent.active 不等於醫療結果成立', $content);
        $this->assertStringContainsString('DocumentReference metadata / reference display 不暴露 binary content', $content);
    }

    public function test_phase_9a_documents_avoid_misleading_activation_language(): void
    {
        $content = strtolower($this->phase9aContent());

        foreach ([
            'gateway runtime enabled',
            'ai agent runtime enabled',
            'fhir write pipeline enabled',
            'cds runtime enabled',
            'smart production activated',
            'ai confirmed',
            'ai diagnosis',
            'signed-off without review',
            'production ingestion enabled',
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
            'docs/fhir/gateway-ai-agent-adapter-contract.md',
            'docs/fhir/contracts/gateway-ai-agent-payload.schema.json',
            'docs/fhir/ai-agent-data-classification.md',
            'docs/fhir/gateway-validation-boundary.md',
            'docs/fhir/gateway-to-fhir-mapping-draft.md',
            'docs/fhir/gateway-error-handling-draft.md',
            'docs/fhir/evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/README.md',
        ];
    }

    /**
     * @return list<string>
     */
    private function indexNeedles(): array
    {
        return [
            '[gateway-ai-agent-adapter-contract.md](gateway-ai-agent-adapter-contract.md)',
            '[contracts/gateway-ai-agent-payload.schema.json](contracts/gateway-ai-agent-payload.schema.json)',
            '[ai-agent-data-classification.md](ai-agent-data-classification.md)',
            '[gateway-validation-boundary.md](gateway-validation-boundary.md)',
            '[gateway-to-fhir-mapping-draft.md](gateway-to-fhir-mapping-draft.md)',
            '[gateway-error-handling-draft.md](gateway-error-handling-draft.md)',
            '[evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/](evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/)',
        ];
    }

    private function phase9aContent(): string
    {
        return collect($this->documentPaths())
            ->map(fn (string $path): string => file_get_contents(base_path($path)))
            ->implode("\n");
    }
}
