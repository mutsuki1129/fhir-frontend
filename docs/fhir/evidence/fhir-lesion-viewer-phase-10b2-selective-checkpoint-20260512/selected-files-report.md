# Selected Files Report

以下檔案明確屬於 Phase 10B / 10B.1 / 10B.2，允許逐檔 staging。風險等級以 checkpoint selection risk 表示。

## Commit 1: Core Prototype

| path | git status | source phase | include reason | risk | human review |
|---|---|---|---|---|---|
| `config/fhir.php` | modified | 10B | feature flag default disabled, mode mock, allow_fhir_write=false | medium | yes |
| `app/Http/Kernel.php` | modified partial | 10B | middleware alias only: `fhir.controlled_ingestion_prototype` | medium | yes |
| `routes/web.php` | modified | 10B | dev-only guarded routes only | medium | yes |
| `app/Http/Controllers/Dev/FhirMockIngestionController.php` | untracked | 10B | dev-only controller for mock preview | medium | yes |
| `app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php` | untracked | 10B | feature flag / environment / mode / no-write guard | medium | yes |
| `app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php` | untracked | 10B/10B.1 | mock parser, validation result, queue item, candidate preview, hardening | medium | yes |
| `resources/views/dev/fhir/mock-ingestion/index.blade.php` | untracked | 10B/10B.1 | dev-only UI wording and preview surface | low | yes |
| `resources/fhir/mock-ingestion/sample-gateway-payload.json` | untracked | 10B | synthetic sample payload | low | yes |

## Commit 2: Tests

| path | git status | source phase | include reason | risk | human review |
|---|---|---|---|---|---|
| `tests/Feature/Fhir/ControlledIngestionPrototypeFeatureFlagTest.php` | untracked | 10B | feature flag and guard tests | low | yes |
| `tests/Feature/Fhir/ControlledIngestionPrototypePreviewTest.php` | untracked | 10B | preview response tests | low | yes |
| `tests/Feature/Fhir/ControlledIngestionPrototypeNoWriteTest.php` | untracked | 10B | no-write tests | low | yes |
| `tests/Feature/Fhir/ControlledIngestionPrototypeUiTest.php` | untracked | 10B | UI wording tests | low | yes |
| `tests/Feature/Fhir/ControlledIngestionPrototypeDocumentationTest.php` | untracked | 10B | documentation boundary tests | low | yes |
| `tests/Feature/Fhir/ControlledIngestionPrototypeHardeningTest.php` | untracked | 10B.1 | invalid JSON, missing field, oversized, binary-like, no raw echo hardening | low | yes |

## Commit 3: Docs / Evidence

| path | git status | source phase | include reason | risk | human review |
|---|---|---|---|---|---|
| `docs/fhir/dev-only-mock-ingestion-prototype.md` | untracked | 10B/10B.1 | prototype and hardening documentation | low | yes |
| `docs/fhir/controlled-ingestion-prototype-planning.md` | modified | 10B/10B.1 | planning and stabilization boundary updates | low | yes |
| `docs/fhir/no-write-fhir-boundary.md` | modified | 10B/10B.1 | no-write boundary updates | low | yes |
| `docs/fhir/manual-review-queue-mock-plan.md` | modified | 10B/10B.1 | manual review queue mock boundary | low | yes |
| `docs/fhir/validation-result-mock-plan.md` | modified | 10B/10B.1 | validation result mock boundary | low | yes |
| `docs/fhir/candidate-resource-staging-plan.md` | modified | 10B/10B.1 | candidate preview boundary | low | yes |
| `docs/fhir/fhir-docs-index.md` | modified | 10B/10B.1 | docs index update | low | yes |
| `docs/README.md` | modified | 10B/10B.1 | docs hub update | low | yes |
| `readme.md` | modified partial | 10B/10B.1 | root README 10B / 10B.1 sections only | low | yes |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10b-dev-only-mock-ingestion-prototype-20260512/` | untracked | 10B | Phase 10B evidence package | low | yes |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10b1-prototype-stabilization-hardening-20260512/` | untracked | 10B.1 | Phase 10B.1 evidence package | low | yes |
| `docs/fhir/evidence/fhir-lesion-viewer-phase-10b2-selective-checkpoint-20260512/` | untracked | 10B.2 | checkpoint execution evidence package | low | yes |
