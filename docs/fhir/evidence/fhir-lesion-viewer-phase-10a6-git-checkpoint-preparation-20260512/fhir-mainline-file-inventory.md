# FHIR Mainline File Inventory

本盤點整理 Phase 1～10A.5 相關檔案群組，並標示建議處理。實際 staging 前仍需人工確認。

Legend:

- `include-in-mainline-checkpoint`：明確屬於 FHIR Read-only Lesion Viewer / Phase 1～10A.5 主線。
- `review-before-include`：可能屬於主線，但 diff 需要人工看過。
- `exclude-unrelated`：不建議放入此 checkpoint。
- `unknown-needs-human-review`：來源或範圍不明，需人工判斷。

## 1. Core Application Code

| File | Recommendation | Reason |
| --- | --- | --- |
| `app/Services/Fhir/FhirApiClient.php` | review-before-include | FHIR client hardening may support Phase 1～10A, but broad shared surface needs review. |
| `app/Services/Fhir/FhirApiException.php` | review-before-include | Error contract likely related; review for unrelated changes. |
| `app/Support/Fhir/PatientMapper.php` | review-before-include | Patient mapping is Phase 1 / read-only support. |
| `app/Support/Fhir/ObservationMapper.php` | review-before-include | Observation enrichment support. |
| `app/Support/Fhir/ConditionMapper.php` | review-before-include | Condition linking support. |
| `app/Support/Fhir/DocumentReferenceMapper.php` | review-before-include | DocumentReference metadata/linking support. |
| `app/Support/Fhir/DiagnosticReportMapper.php` | include-in-mainline-checkpoint | DiagnosticReport-centered lesion aggregation support. |
| `app/Support/Fhir/EncounterMapper.php` | include-in-mainline-checkpoint | Encounter enrichment support. |
| `app/Support/Text/DisplayStringSanitizer.php` | review-before-include | Phase 1 hardening support; verify scope. |
| `app/ViewModels/PatientVM.php` | review-before-include | Patient display support. |
| `app/ViewModels/ConditionVM.php` | review-before-include | Condition display support. |
| `app/ViewModels/TemperatureObservationVM.php` | review-before-include | Observation display support. |

## 2. Routes / Middleware / Config

| File | Recommendation | Reason |
| --- | --- | --- |
| `routes/web.php` | include-in-mainline-checkpoint | Contains Lesion Viewer GET routes and FHIR documentation/portal display routes. |
| `routes/api.php` | include-in-mainline-checkpoint | Contains read-only `api/lesions` GET routes. |
| `config/fhir.php` | include-in-mainline-checkpoint | Lesion source switch and FHIR config. |
| `app/Http/Middleware/EnsureFhirFrontendReadOnly.php` | include-in-mainline-checkpoint | Read-only route guard. |
| `app/Http/Kernel.php` | review-before-include | Middleware registration; shared file requires review. |
| `config/services.php` | review-before-include | SMART/FHIR service config may be related; verify no unrelated changes. |
| `.env.docker` | review-before-include | Environment-only connectivity change; should not be staged blindly. |

## 3. Lesion Viewer Repository Layer

| File | Recommendation | Reason |
| --- | --- | --- |
| `app/Services/Fhir/LesionViewer/LesionRepository.php` | include-in-mainline-checkpoint | Repository interface. |
| `app/Services/Fhir/LesionViewer/MockLesionRepository.php` | include-in-mainline-checkpoint | Mock-default source. |
| `app/Services/Fhir/LesionViewer/FhirBackedLesionRepository.php` | include-in-mainline-checkpoint | Read-only FHIR-backed aggregation. |

## 4. Lesion Viewer Controller

| File | Recommendation | Reason |
| --- | --- | --- |
| `app/Http/Controllers/LesionViewerController.php` | include-in-mainline-checkpoint | Lesion list/detail/API controller. |

## 5. Lesion Viewer Blade UI

| File | Recommendation | Reason |
| --- | --- | --- |
| `resources/views/admin/lesions/index.blade.php` | include-in-mainline-checkpoint | Read-only Lesion list UI. |
| `resources/views/admin/lesions/show.blade.php` | include-in-mainline-checkpoint | Read-only Lesion detail UI. |

## 6. Language / Navigation / Dashboard Wording

| File | Recommendation | Reason |
| --- | --- | --- |
| `lang/en/fhir.php` | include-in-mainline-checkpoint | FHIR / Lesion wording. |
| `lang/zh_TW/fhir.php` | include-in-mainline-checkpoint | FHIR / Lesion wording. |
| `lang/en/ui.php` | review-before-include | Existing UI language file; review broader edits. |
| `lang/zh_TW/ui.php` | review-before-include | Existing UI language file; review broader edits. |
| `resources/views/layouts/navigation.blade.php` | review-before-include | Navigation wording. |
| `resources/views/layouts/sidebar.blade.php` | review-before-include | Sidebar wording. |
| `resources/views/dashboard/admin.blade.php` | review-before-include | Dashboard entry point. |

## 7. Tests

| File | Recommendation | Reason |
| --- | --- | --- |
| `tests/Feature/Fhir/LesionRepositoryBindingTest.php` | include-in-mainline-checkpoint | Repository binding safety. |
| `tests/Feature/Fhir/LesionViewerSourceSwitchTest.php` | include-in-mainline-checkpoint | Source switch behavior. |
| `tests/Feature/Fhir/LesionViewerApiTest.php` | include-in-mainline-checkpoint | Read-only API contract. |
| `tests/Feature/Fhir/LesionViewerRouteTest.php` | include-in-mainline-checkpoint | Route behavior. |
| `tests/Feature/Fhir/LesionViewerUiTest.php` | include-in-mainline-checkpoint | UI read-only baseline. |
| `tests/Feature/Fhir/LesionViewerDocumentationTest.php` | include-in-mainline-checkpoint | Documentation coverage. |
| `tests/Feature/Fhir/FhirBackedLesionRepositoryTest.php` | include-in-mainline-checkpoint | FHIR-backed repository. |
| `tests/Feature/Fhir/FhirBackedLesionAggregationTest.php` | include-in-mainline-checkpoint | DiagnosticReport aggregation. |
| `tests/Feature/Fhir/FhirBackedLesionEnrichmentTest.php` | include-in-mainline-checkpoint | Patient/Observation/Encounter enrichment. |
| `tests/Feature/Fhir/FhirBackedLesionLinkingTest.php` | include-in-mainline-checkpoint | Condition/DocumentReference/Consent linking. |
| `tests/Feature/Fhir/FrontendReadOnlyRouteGuardTest.php` | include-in-mainline-checkpoint | Read-only route guard. |
| `tests/Feature/Fhir/FrontendReadOnlyUiTest.php` | include-in-mainline-checkpoint | Read-only UI controls. |
| `tests/Feature/Fhir/GatewayRuntimeSafetyTest.php` | include-in-mainline-checkpoint | Gateway runtime absence. |
| `tests/Feature/Fhir/FhirValidationRuntimeSafetyTest.php` | include-in-mainline-checkpoint | Validation runtime absence. |
| `tests/Feature/Fhir/GovernanceRuntimeSafetyTest.php` | include-in-mainline-checkpoint | Governance runtime safety. |
| `tests/Feature/Fhir/ControlledIngestionRuntimeSafetyTest.php` | include-in-mainline-checkpoint | Controlled ingestion runtime absence. |
| `tests/Feature/Fhir/ControlledIngestionPlanningDocumentationTest.php` | include-in-mainline-checkpoint | Phase 10A docs. |
| `tests/Feature/Fhir/FhirGitCheckpointPreparationDocumentationTest.php` | include-in-mainline-checkpoint | Phase 10A.6 docs-only test. |
| `tests/Feature/TestingRegisterTest.php` | unknown-needs-human-review | Non-FHIR test file changed; do not stage blindly. |

## 8. FHIR Lesion Viewer Docs

| File | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/lesion-viewer-data-contract.md` | include-in-mainline-checkpoint | Lesion ViewModel contract. |
| `docs/fhir/lesion-fhir-resource-mapping.md` | include-in-mainline-checkpoint | Resource mapping. |
| `docs/fhir/lesion-fhir-aggregation-strategy.md` | include-in-mainline-checkpoint | Aggregation strategy. |

## 9. Gateway / AI Agent Contract Docs

| File | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/gateway-ai-agent-adapter-contract.md` | include-in-mainline-checkpoint | Phase 9A contract draft. |
| `docs/fhir/contracts/gateway-ai-agent-payload.schema.json` | include-in-mainline-checkpoint | Phase 9A payload schema draft. |
| `docs/fhir/ai-agent-data-classification.md` | include-in-mainline-checkpoint | AI data classification boundary. |
| `docs/fhir/gateway-validation-boundary.md` | include-in-mainline-checkpoint | Validation boundary. |
| `docs/fhir/gateway-to-fhir-mapping-draft.md` | include-in-mainline-checkpoint | Candidate mapping draft. |
| `docs/fhir/gateway-error-handling-draft.md` | include-in-mainline-checkpoint | Error handling draft. |

## 10. FHIR Validation Workflow Docs

| File | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/fhir-validation-workflow-draft.md` | include-in-mainline-checkpoint | Phase 9B workflow. |
| `docs/fhir/fhir-validation-error-taxonomy.md` | include-in-mainline-checkpoint | Error taxonomy. |
| `docs/fhir/fhir-profile-ig-draft-boundary.md` | include-in-mainline-checkpoint | IG/live validation boundary. |
| `docs/fhir/fhir-validation-result-contract.md` | include-in-mainline-checkpoint | Validation result contract. |
| `docs/fhir/contracts/fhir-validation-result.schema.json` | include-in-mainline-checkpoint | Validation result schema. |
| `docs/fhir/manual-review-gate-policy.md` | include-in-mainline-checkpoint | Manual review boundary. |
| `docs/fhir/future-hapi-validate-boundary.md` | include-in-mainline-checkpoint | Future `$validate` boundary. |

## 11. Governance Review Docs

| File | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/governance-review-draft.md` | include-in-mainline-checkpoint | Phase 9C governance draft. |
| `docs/fhir/smart-production-activation-policy-draft.md` | include-in-mainline-checkpoint | SMART production boundary. |
| `docs/fhir/cds-runtime-governance-policy-draft.md` | include-in-mainline-checkpoint | CDS runtime governance boundary. |
| `docs/fhir/document-reference-protected-download-policy-draft.md` | include-in-mainline-checkpoint | DocumentReference binary boundary. |
| `docs/fhir/consent-ecsu-signoff-governance-draft.md` | include-in-mainline-checkpoint | Consent/sign-off boundary. |
| `docs/fhir/patient-display-policy-draft.md` | include-in-mainline-checkpoint | Patient display boundary. |
| `docs/fhir/ai-generated-review-policy-draft.md` | include-in-mainline-checkpoint | AI-generated review boundary. |
| `docs/fhir/fhir-validation-governance-policy-draft.md` | include-in-mainline-checkpoint | Validation governance. |
| `docs/fhir/gateway-ingestion-governance-policy-draft.md` | include-in-mainline-checkpoint | Gateway ingestion governance. |
| `docs/fhir/audit-provenance-logging-policy-draft.md` | include-in-mainline-checkpoint | Audit/provenance policy draft. |

## 12. Controlled Ingestion Planning Docs

| File | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/controlled-ingestion-prototype-planning.md` | include-in-mainline-checkpoint | Phase 10A planning. |
| `docs/fhir/dev-only-mock-ingestion-scope.md` | include-in-mainline-checkpoint | Future Phase 10B scope. |
| `docs/fhir/mock-ingestion-payload-policy.md` | include-in-mainline-checkpoint | Mock payload policy. |
| `docs/fhir/no-write-fhir-boundary.md` | include-in-mainline-checkpoint | No-write boundary. |
| `docs/fhir/manual-review-queue-mock-plan.md` | include-in-mainline-checkpoint | Mock manual review queue plan. |
| `docs/fhir/validation-result-mock-plan.md` | include-in-mainline-checkpoint | Mock validation result plan. |
| `docs/fhir/candidate-resource-staging-plan.md` | include-in-mainline-checkpoint | Candidate preview/staging. |
| `docs/fhir/controlled-ingestion-rollback-disablement-plan.md` | include-in-mainline-checkpoint | Rollback/disablement plan. |
| `docs/fhir/controlled-ingestion-test-data-policy.md` | include-in-mainline-checkpoint | Synthetic test data policy. |
| `docs/fhir/controlled-ingestion-prototype-exit-criteria.md` | include-in-mainline-checkpoint | Exit criteria. |
| `docs/fhir/phase-10b-readiness-checklist.md` | include-in-mainline-checkpoint | Pre-Phase 10B checklist. |

## 13. Phase 1～10A.5 Evidence Packages

| Path | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-3-mock-api-ui-20260512/` | include-in-mainline-checkpoint | Lesion Viewer mock API/UI baseline. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/` | include-in-mainline-checkpoint | Repository/skeleton baseline. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/` | include-in-mainline-checkpoint | FHIR-backed aggregation. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/` | include-in-mainline-checkpoint | Enrichment package. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/` | include-in-mainline-checkpoint | Linking package. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/` | include-in-mainline-checkpoint | Stabilization handoff. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/` | include-in-mainline-checkpoint | Gateway / AI Agent contract evidence. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/` | include-in-mainline-checkpoint | Validation workflow evidence. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/` | include-in-mainline-checkpoint | Governance review evidence. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/` | include-in-mainline-checkpoint | Controlled ingestion planning evidence. |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10a5-baseline-checkpoint-20260512/` | include-in-mainline-checkpoint | Baseline checkpoint / readiness review. |

## 14. Phase 10A.6 Checkpoint Files

| Path | Recommendation | Reason |
| --- | --- | --- |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/` | include-in-mainline-checkpoint | This selective commit plan evidence package. |
| `tests/Feature/Fhir/FhirGitCheckpointPreparationDocumentationTest.php` | include-in-mainline-checkpoint | Documentation-only package coverage. |
| `docs/fhir/fhir-docs-index.md` | include-in-mainline-checkpoint | FHIR docs index update. |
| `docs/README.md` | include-in-mainline-checkpoint | Project docs index update. |

## Explicit Exclusions

`.e2e_*.html`, `.e2e_cookies.txt`, `.env.*.bak`, `.env.backup-*`, root deleted legacy docs, logos, and broad SMART/CDS/terminology/document storage files are not automatically included here. Some may be valid for another checkpoint, but Phase 10A.6 should not stage them without human review.
