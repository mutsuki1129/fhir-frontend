# Phase 10E — Documentation-only Dirty Tree Cleanup Plan

## 1. Purpose

This phase defines a dirty tree cleanup and selective commit plan based on the Phase 10D preflight results.

- Do not clean files during this phase.
- Do not modify runtime behavior.
- Do not touch unrelated files.
- Do not introduce CRUD, FHIR write, formal ingestion, approval persistence, or signoff persistence.
- Prepare the next phase to narrow scope safely before any selective work.

## 2. Current Baseline

- Latest checkpoint: `38b9ee3a3a739b794c710bc6021cbaf0a2da0a4f`
- Latest checkpoint message: `docs(fhir): add phase 10c readonly lesion viewer qa evidence`
- Current direction: FHIR Read-only Lesion / Clinical Evidence Viewer.
- Phase 10C completed and passed post-commit verification.
- Phase 10D preflight completed without file modification, staging, or commit.
- Phase 10D dirty tree count:
  - Tracked modified: `65`
  - Tracked deleted: `8`
  - Untracked: `457`
  - Total dirty entries: `530`

## 3. Dirty Tree Cleanup Principles

- Never use broad staging.
- Never use destructive cleanup.
- Use selective path review only.
- Commit only one logical group at a time.
- Keep read-only viewer work separate from SMART, CDS, Gateway, env, and docker work.
- Keep documentation-only work separate from runtime work.
- Keep UI visual QA separate from FHIR read integration.
- Keep mock preview wording separate from formal ingestion.
- Preserve unrelated dirty and untracked files.

## 4. Proposed Commit Groups

### Group A — Read-only Lesion Viewer Docs

Potential scope:

- Lesion viewer evidence docs.
- Lesion mapping and aggregation strategy docs.
- Documentation index updates.

Exclusions:

- No runtime code.
- No route/controller/service behavior changes.
- No FHIR write or clinical mutation behavior.

### Group B — Read-only Lesion Viewer UI QA

Potential scope:

- `resources/views/admin/lesions/index.blade.php`
- `resources/views/admin/lesions/show.blade.php`
- Loading, empty, and error display only.

Exclusions:

- No create, edit, delete, approve, signoff, or persistence forms.
- No mutation buttons, mutation URLs, or writable schemas.
- No formal ingestion controls.

### Group C — Mock Ingestion Preview Documentation / Safety Wording

Potential scope:

- Dev-only mock ingestion docs.
- Preview-only wording.
- Evidence for `noFHIRWrite`, `persisted=false`, `signedOff=false`, `devOnly=true`, and `mockOnly=true`.

Exclusions:

- Must not become formal ingestion.
- Must not persist clinical data.
- Must not write FHIR resources.
- Must not create, update, delete, approve, sign off, upload, purge, or trigger clinical mutation.

### Group D — FHIR Read Integration Verification

Potential scope:

- FHIR-backed lesion repository tests.
- Read-only aggregation tests.
- Patient, Observation, DiagnosticReport, DocumentReference, Condition, Encounter, and Consent display verification.

Exclusions:

- No FHIR create, update, delete, patch, upload, purge, approval persistence, or signoff persistence.
- No lesion CRUD.
- No formal ingestion.

### Group E — SMART / Gateway / CDS Separate Track

This group is a separate future track only.

It must not be handled inside lesion viewer dirty tree cleanup unless separately authorized with a specific scope.

Potential future scope:

- SMART docs or verification.
- Gateway read-only policy documentation.
- CDS governance or report-only documentation.

Exclusions for lesion viewer cleanup:

- No SMART production config.
- No Gateway runtime activation.
- No CDS runtime activation.
- No approval/signoff persistence.
- No FHIR write paths.

### Group F — Unrelated / Do-not-touch

Do not include these in lesion viewer cleanup commits:

- Env backups.
- `.e2e_*` artifacts.
- Legacy docs.
- Public assets.
- Patient CRUD.
- Rekam CRUD.
- Practitioner CRUD.
- Observation CRUD.
- DocumentReference upload/delete/purge.
- CDS runtime activation.
- SMART production config.
- HAPI server config.
- Docker config.
- Unrelated untracked files.

## 5. Recommended Next Authorized Phase

Recommended next phase: **Option 10F-A — Commit Phase 10E documentation-only cleanup plan**.

Reasons:

- The working tree is currently too dirty for safe broad work.
- The cleanup plan should be committed as evidence before additional selective work.
- A committed plan gives the next phase a stable safety boundary.
- This reduces the risk of mixing UI, integration, SMART, CDS, Gateway, env, or docker changes.

Do not start UI QA, loading/empty/error stabilization, FHIR read integration verification, or mock ingestion wording hardening until a single next scope is authorized.

## 6. No-touch List

The following remain out of scope for the next phase unless explicitly authorized:

- `.env`
- `.env.docker`
- `docker-compose.yml`
- HAPI server config
- SMART production config
- CDS runtime
- Gateway runtime activation
- FHIR write
- Lesion create/edit/update/delete
- Formal ingestion
- Approval/signoff persistence
- Patient CRUD
- Observation CRUD
- Rekam CRUD
- Practitioner CRUD
- DocumentReference upload/delete/purge
- `FhirApiClient::expungeDeletedResource`
- Unrelated untracked files
- `.e2e_*` artifacts
- Env backups
- Public logo/favicon assets

## 7. Suggested Verification Commands

Suggested read-only verification commands for the next authorized phase:

```powershell
git status --short
git diff --cached --stat
php artisan route:list --path=lesions
php artisan route:list --path=mock-ingestion
php artisan test --filter LesionViewerDocumentationTest
php artisan test --filter FhirDocumentationIndexTest
```

Do not run destructive commands. Do not use broad staging.

## 8. Final Safety Statement

- This phase is documentation-only.
- No runtime files were modified.
- No route/controller/service behavior was changed.
- No env/docker/HAPI/SMART/CDS/Gateway config was changed.
- No CRUD/write/ingestion/approval persistence was introduced.
- Dirty and untracked unrelated files remain untouched.
