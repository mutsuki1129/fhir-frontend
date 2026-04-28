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
- [ ] Add integration tests for Condition create/update + fallback behavior

### Next tasks (Media / DocumentReference)

- [x] Define image/document baseline UX and metadata mapping (URL-based attachment baseline)
- [x] Add `DocumentReference` adapter + API hooks
- [x] Replace disabled legacy `picture` with FHIR-backed flow baseline
- [ ] Add binary upload flow (`Media` or attachment data) and secure fetch policy
- [ ] Define observation/condition/document explicit linkage model (currently patient-latest)

### Next tasks (Practitioner)

- [x] Define `PractitionerVM` and mapper
- [ ] Migrate doctor list/create/edit from legacy model to FHIR `Practitioner`
- [x] Align Observation performer selection with Practitioner data source (rekam flow)
- [ ] Extend Practitioner validation and fallback coverage to full doctor module

## Non-Goals in this batch

- No destructive data migration.
- No large UI redesign.
- No removal of existing Phase 1 temperature Observation workflow.
