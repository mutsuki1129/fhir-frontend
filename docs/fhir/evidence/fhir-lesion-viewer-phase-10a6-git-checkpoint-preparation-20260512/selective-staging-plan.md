# Selective Staging Plan

本文件提供建議 staging 命令，但 Phase 10A.6 不執行 staging。

不要使用：

- `git add .`
- `git add -A`
- wildcard 導致 unrelated files 被 stage

## 建議先建立新 branch，但不要由 Codex 自動執行

```bash
git status --short
git branch --show-current
git checkout -b fhir-readonly-lesion-viewer-baseline-phase-1-to-10a
```

若 working tree 有大量 unrelated dirty files，請先人工確認 checkout 不會造成衝突。Codex 不應自動 checkout。

## Commit 1 Staging: Read-only Lesion Viewer Baseline

原因：read-only UI、routes、controller、repository interface、mock source、read-only guard、language/navigation wording。

```bash
git add app/Services/Fhir/LesionViewer/LesionRepository.php
git add app/Services/Fhir/LesionViewer/MockLesionRepository.php
git add app/Http/Controllers/LesionViewerController.php
git add app/Http/Middleware/EnsureFhirFrontendReadOnly.php
git add resources/views/admin/lesions/index.blade.php
git add resources/views/admin/lesions/show.blade.php
git add routes/web.php
git add routes/api.php
git add config/fhir.php
git add lang/en/fhir.php
git add lang/zh_TW/fhir.php
git add tests/Feature/Fhir/LesionRepositoryBindingTest.php
git add tests/Feature/Fhir/LesionViewerSourceSwitchTest.php
git add tests/Feature/Fhir/LesionViewerApiTest.php
git add tests/Feature/Fhir/LesionViewerRouteTest.php
git add tests/Feature/Fhir/LesionViewerUiTest.php
git add tests/Feature/Fhir/FrontendReadOnlyRouteGuardTest.php
git add tests/Feature/Fhir/FrontendReadOnlyUiTest.php
```

## Commit 2 Staging: FHIR-backed Lesion Aggregation

原因：DiagnosticReport-centered aggregation、Patient / Observation / Encounter enrichment、Condition / DocumentReference / Consent linking、FHIR-backed source switch。

```bash
git add app/Services/Fhir/LesionViewer/FhirBackedLesionRepository.php
git add app/Support/Fhir/DiagnosticReportMapper.php
git add app/Support/Fhir/EncounterMapper.php
git add app/Support/Fhir/PatientMapper.php
git add app/Support/Fhir/ObservationMapper.php
git add app/Support/Fhir/ConditionMapper.php
git add app/Support/Fhir/DocumentReferenceMapper.php
git add tests/Feature/Fhir/FhirBackedLesionRepositoryTest.php
git add tests/Feature/Fhir/FhirBackedLesionAggregationTest.php
git add tests/Feature/Fhir/FhirBackedLesionEnrichmentTest.php
git add tests/Feature/Fhir/FhirBackedLesionLinkingTest.php
```

## Commit 3 Staging: Gateway / Validation / Governance Draft Docs

原因：Phase 9A Gateway / AI Agent Adapter Contract、Phase 9B FHIR Validation Workflow、Phase 9C Governance Review。

```bash
git add docs/fhir/gateway-ai-agent-adapter-contract.md
git add docs/fhir/contracts/gateway-ai-agent-payload.schema.json
git add docs/fhir/ai-agent-data-classification.md
git add docs/fhir/gateway-validation-boundary.md
git add docs/fhir/gateway-to-fhir-mapping-draft.md
git add docs/fhir/gateway-error-handling-draft.md
git add docs/fhir/fhir-validation-workflow-draft.md
git add docs/fhir/fhir-validation-error-taxonomy.md
git add docs/fhir/fhir-profile-ig-draft-boundary.md
git add docs/fhir/fhir-validation-result-contract.md
git add docs/fhir/contracts/fhir-validation-result.schema.json
git add docs/fhir/manual-review-gate-policy.md
git add docs/fhir/future-hapi-validate-boundary.md
git add docs/fhir/governance-review-draft.md
git add docs/fhir/smart-production-activation-policy-draft.md
git add docs/fhir/cds-runtime-governance-policy-draft.md
git add docs/fhir/document-reference-protected-download-policy-draft.md
git add docs/fhir/consent-ecsu-signoff-governance-draft.md
git add docs/fhir/patient-display-policy-draft.md
git add docs/fhir/ai-generated-review-policy-draft.md
git add docs/fhir/fhir-validation-governance-policy-draft.md
git add docs/fhir/gateway-ingestion-governance-policy-draft.md
git add docs/fhir/audit-provenance-logging-policy-draft.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/README.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/README.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/README.md
```

人工確認後可補齊上述 evidence package 的其餘文件，避免 wildcard。

## Commit 4 Staging: Controlled Ingestion Planning and Checkpoint

原因：Phase 10A planning、Phase 10A.5 baseline checkpoint、Phase 10A.6 git checkpoint preparation。

```bash
git add docs/fhir/controlled-ingestion-prototype-planning.md
git add docs/fhir/dev-only-mock-ingestion-scope.md
git add docs/fhir/mock-ingestion-payload-policy.md
git add docs/fhir/no-write-fhir-boundary.md
git add docs/fhir/manual-review-queue-mock-plan.md
git add docs/fhir/validation-result-mock-plan.md
git add docs/fhir/candidate-resource-staging-plan.md
git add docs/fhir/controlled-ingestion-rollback-disablement-plan.md
git add docs/fhir/controlled-ingestion-test-data-policy.md
git add docs/fhir/controlled-ingestion-prototype-exit-criteria.md
git add docs/fhir/phase-10b-readiness-checklist.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/README.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a5-baseline-checkpoint-20260512/README.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/README.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/working-tree-snapshot.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/fhir-mainline-file-inventory.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/unrelated-dirty-files.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/selective-staging-plan.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/commit-split-recommendation.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/branch-recommendation.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/patch-backup-plan.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/pre-commit-verification-checklist.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/post-commit-verification-checklist.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/route-safety-results.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/runtime-safety-results.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/regression-test-results.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/documentation-results.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/deferred-issues.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/safety-boundary.md
git add docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/next-phase-plan.md
git add docs/fhir/fhir-docs-index.md
git add docs/README.md
```

## Commit 5 Staging: Runtime Safety and Documentation Coverage

原因：runtime absence tests、documentation-only validation tests。

```bash
git add tests/Feature/Fhir/GatewayRuntimeSafetyTest.php
git add tests/Feature/Fhir/FhirValidationRuntimeSafetyTest.php
git add tests/Feature/Fhir/GovernanceRuntimeSafetyTest.php
git add tests/Feature/Fhir/ControlledIngestionRuntimeSafetyTest.php
git add tests/Feature/Fhir/GatewayAiAgentContractDocumentationTest.php
git add tests/Feature/Fhir/FhirValidationWorkflowDocumentationTest.php
git add tests/Feature/Fhir/GovernanceReviewDocumentationTest.php
git add tests/Feature/Fhir/ControlledIngestionPlanningDocumentationTest.php
git add tests/Feature/Fhir/LesionViewerDocumentationTest.php
git add tests/Feature/Fhir/FhirGitCheckpointPreparationDocumentationTest.php
```

## Required Manual Review Before Commit

```bash
git diff --cached --name-only
git diff --cached --stat
git diff --cached
```

實際 commit 前需人工確認 staging。不要自動 commit。不要自動 squash unrelated changes。
