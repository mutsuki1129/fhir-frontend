# Untracked Files Review

規則：

- 只有明確屬於 Phase 10B / 10B.1 code / docs / tests / evidence 的 untracked files 可以 stage。
- unknown untracked files 不得 stage。
- unrelated untracked files 不得 stage。

允許 stage 的 untracked files：

- `app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `resources/views/dev/fhir/mock-ingestion/index.blade.php`
- `resources/fhir/mock-ingestion/sample-gateway-payload.json`
- `tests/Feature/Fhir/ControlledIngestionPrototype*.php`
- `docs/fhir/dev-only-mock-ingestion-prototype.md`
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10b-dev-only-mock-ingestion-prototype-20260512/`
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10b1-prototype-stabilization-hardening-20260512/`
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10b2-selective-checkpoint-20260512/`

排除的 untracked files：

- `.e2e_*`
- env backup files
- broad CDS / SMART / terminology / storage / database files
- unrelated views, controllers, models, migrations, seeders, scripts, docs, images, and test files

本階段不清理、不刪除、不 stage unrelated untracked files。
