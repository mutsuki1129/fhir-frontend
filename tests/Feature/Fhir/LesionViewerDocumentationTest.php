<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

class LesionViewerDocumentationTest extends TestCase
{
    public function test_lesion_viewer_documents_exist_and_are_indexed(): void
    {
        $contractPath = base_path('docs/fhir/lesion-viewer-data-contract.md');
        $mappingPath = base_path('docs/fhir/lesion-fhir-resource-mapping.md');
        $aggregationPath = base_path('docs/fhir/lesion-fhir-aggregation-strategy.md');
        $phase3EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/README.md');
        $phase4EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/README.md');
        $phase5EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/README.md');
        $phase6EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/README.md');
        $phase7EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/README.md');
        $handoffPath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/README.md');
        $phase8EvidencePath = base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/README.md');
        $fhirIndex = file_get_contents(base_path('docs/fhir/fhir-docs-index.md'));
        $docsIndex = file_get_contents(base_path('docs/README.md'));
        $readme = file_get_contents(base_path('README.md'));

        $this->assertFileExists($contractPath);
        $this->assertFileExists($mappingPath);
        $this->assertFileExists($aggregationPath);
        $this->assertFileExists($phase3EvidencePath);
        $this->assertFileExists($phase4EvidencePath);
        $this->assertFileExists($phase5EvidencePath);
        $this->assertFileExists($phase6EvidencePath);
        $this->assertFileExists($phase7EvidencePath);
        $this->assertFileExists($handoffPath);
        $this->assertFileExists($phase8EvidencePath);
        $this->assertStringContainsString('[lesion-viewer-data-contract.md](lesion-viewer-data-contract.md)', $fhirIndex);
        $this->assertStringContainsString('[lesion-fhir-resource-mapping.md](lesion-fhir-resource-mapping.md)', $fhirIndex);
        $this->assertStringContainsString('[lesion-fhir-aggregation-strategy.md](lesion-fhir-aggregation-strategy.md)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/](evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/](evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/](evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/](evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/](evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/](evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/)', $fhirIndex);
        $this->assertStringContainsString('[evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/](evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/)', $fhirIndex);
        $this->assertStringContainsString('docs/fhir/lesion-viewer-data-contract.md', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/lesion-fhir-resource-mapping.md', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/lesion-fhir-aggregation-strategy.md', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/', $docsIndex . $readme);
        $this->assertStringContainsString('docs/fhir/evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/', $docsIndex . $readme);
    }

    public function test_lesion_viewer_documents_define_read_only_positioning_and_boundaries(): void
    {
        $content = $this->lesionDocsContent();
        $contractContent = file_get_contents(base_path('docs/fhir/lesion-viewer-data-contract.md'));

        $this->assertStringContainsString('FHIR Read-only Lesion Viewer', $content);
        $this->assertStringContainsString('Lesion is not a new FHIR Resource', $content);
        $this->assertStringContainsString('LesionRepository', $content);
        $this->assertStringContainsString('FHIR_LESION_VIEWER_SOURCE', $content);
        $this->assertStringContainsString('fhir-backed-lesion-repository', $content);
        $this->assertStringContainsString('DiagnosticReport-centered aggregation', $content);
        $this->assertStringContainsString('DiagnosticReport does not equal automatic diagnosis', $content);
        $this->assertStringContainsString('AI-generated content is not clinician-confirmed content', $content);
        $this->assertStringContainsString('lesion-report-001', $content);
        $this->assertStringContainsString('read-only-aggregation-v3', $contractContent);
        $this->assertStringNotContainsString('read-only-aggregation-v1', $contractContent);
        $this->assertStringNotContainsString('read-only-aggregation-v2', $contractContent);
        $this->assertStringContainsString('Patient enrichment', $content);
        $this->assertStringContainsString('Observation values are displayed only', $content);
        $this->assertStringContainsString('Patient enrichment must not display full name', $content);
        $this->assertStringContainsString('Condition linking does not equal frontend automatic diagnosis', $content);
        $this->assertStringContainsString('DocumentReference metadata / reference display does not expose sensitive file content directly', $content);
        $this->assertStringContainsString('Consent linking does not represent a medical result', $content);
        $this->assertStringContainsString('GET /api/lesions', $content);
        $this->assertStringContainsString('mock-lesion-repository', $content);
        $this->assertStringContainsString('clinician-reviewed', $content);
        $this->assertStringContainsString('pending-review', $content);
        $this->assertStringContainsString('signed-off', $content);
        $this->assertStringContainsString('superseded', $content);
        foreach (['Patient', 'Observation', 'Condition', 'DiagnosticReport', 'DocumentReference', 'Consent', 'Encounter'] as $resourceType) {
            $this->assertStringContainsString($resourceType, $content);
        }
        $this->assertStringContainsString('live FHIR write/update/delete', $content);
        $this->assertStringContainsString('POST/PATCH/DELETE lesion route', $content);
        $this->assertStringContainsString('Gateway ingestion', $contractContent);
        $this->assertStringContainsString('No clinical advice', $contractContent);
        $this->assertStringContainsString('No live FHIR write/update/delete', $contractContent);
    }

    public function test_lesion_viewer_documents_avoid_misleading_activation_language(): void
    {
        $content = strtolower($this->lesionDocsContent());

        foreach ([
            'smart production activation enabled',
            'clinical advice enabled',
            'automatic diagnosis enabled',
            'cds runtime enabled',
            'treatment recommendation enabled',
        ] as $forbiddenPhrase) {
            $this->assertStringNotContainsString($forbiddenPhrase, $content);
        }
    }

    private function lesionDocsContent(): string
    {
        return file_get_contents(base_path('docs/fhir/lesion-viewer-data-contract.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/lesion-fhir-resource-mapping.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/lesion-fhir-aggregation-strategy.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/README.md'))
            . "\n"
            . file_get_contents(base_path('docs/fhir/evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/README.md'));
    }
}
