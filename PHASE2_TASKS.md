# Phase 2 Frontend Tasks

## Scope Baseline

This task list follows:

- `PHASE2_SCOPE.md`
- `PHASE1_KNOWN_LIMITATIONS.md`
- `FRONTEND_FHIR_USAGE.md`
- `FRONTEND_PHASE1_PLAN.md`

Phase 2 frontend priorities:

1. `Condition` (replace legacy `kondisi` as primary clinical flow)
2. `Media` / `DocumentReference` (replace legacy `picture`)
3. `Practitioner` module alignment

## Current Status

### Completed in this batch

- [x] Add Phase 2 planning docs (`PHASE2_TASKS.md`, `PHASE2_CONDITION_PLAN.md`)
- [x] Add Condition frontend adapter layer:
  - `app/ViewModels/ConditionVM.php`
  - `app/Support/Fhir/ConditionMapper.php`
- [x] Integrate Condition flow in medical record pages:
  - create: accept condition text/code
  - edit: load/edit condition text/code
  - list: show condition summary
- [x] Add Practitioner baseline adapter and rekam performer integration:
  - `app/ViewModels/PractitionerVM.php`
  - `app/Support/Fhir/PractitionerMapper.php`
  - rekam create/edit performer source switched to Practitioner
- [x] Add DocumentReference baseline adapter and rekam attachment integration:
  - `app/ViewModels/DocumentReferenceVM.php`
  - `app/Support/Fhir/DocumentReferenceMapper.php`
  - rekam create/edit/list supports URL-based document reference flow
- [x] Keep Phase 1 Observation flow stable (temperature create/edit/list still active)
- [x] Add conservative fallback when Condition API is unavailable (Observation + legacy note still works)

### Next tasks (Condition hardening)

- [x] Add tighter validation policy for condition code system/value formats
- [x] Add patient-specific Condition fetch optimization (search by subject)
- [x] Add UI indicator for Condition stale/missing state per card
- [x] Add integration tests for Condition create/update success behavior (`scripts/phase2-condition-smoke.ps1`)
- [ ] Deferred: Add deterministic fallback integration test for Condition API failures
  - reason: requires controllable backend fault injection contract (mock endpoint or switchable failure mode) that does not exist in current environment
  - risk: fallback path may regress silently when backend/network behavior changes
  - next: coordinate with backend to provide test-only failure toggle, then add automated fallback assertion in CI smoke script

### Next tasks (Media / DocumentReference)

- [x] Define image/document baseline UX and metadata mapping (URL-based attachment baseline)
- [x] Add `DocumentReference` adapter + API hooks
- [x] Replace disabled legacy `picture` with FHIR-backed flow baseline
- [ ] Deferred: Add binary upload flow (`Media` or attachment data) and secure fetch policy
  - reason: no finalized backend upload contract (multipart/content limits/storage/authz headers)
  - risk: implementing now may break compatibility or create insecure file handling assumptions
  - next: wait for backend upload contract and security policy, then implement upload UI + validation + download authorization checks
- [ ] Deferred: Define observation/condition/document explicit linkage model (currently patient-latest)
  - reason: linkage key is not defined in current API contract (observation-level relation field missing)
  - risk: patient-latest heuristic can mismatch attachment/condition when patient has multiple records
  - next: align on explicit linkage field(s) (e.g. basedOn/related/identifier strategy), then migrate list rendering to deterministic relation

### Next tasks (Practitioner)

- [x] Define `PractitionerVM` and mapper
- [ ] Deferred: Migrate doctor list/create/edit from legacy model to FHIR `Practitioner`
  - reason: route-level doctor module migration touches broad legacy CRUD/auth assumptions outside current safe-change window
  - risk: partial migration can break existing doctor management and role-dependent pages
  - next: execute dedicated migration batch for doctor module with page-by-page parity checklist and rollback plan
- [x] Align Observation performer selection with Practitioner data source (rekam flow)
- [ ] Deferred: Extend Practitioner validation and fallback coverage to full doctor module
  - reason: depends on full doctor module migration and agreed Practitioner profile constraints
  - risk: inconsistent validation between rekam performer path and doctor module
  - next: once module migration starts, centralize practitioner validation rules and fallback messages in shared adapter/service

## Non-Goals in this batch

- No destructive data migration.
- No large UI redesign.
- No removal of existing Phase 1 temperature Observation workflow.
