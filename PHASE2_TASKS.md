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
- [x] Keep Phase 1 Observation flow stable (temperature create/edit/list still active)
- [x] Add conservative fallback when Condition API is unavailable (Observation + legacy note still works)

### Next tasks (Condition hardening)

- [ ] Add tighter validation policy for condition code system/value formats
- [ ] Add patient-specific Condition fetch optimization (search by subject)
- [ ] Add UI indicator for Condition stale/missing state per card
- [ ] Add integration tests for Condition create/update + fallback behavior

### Next tasks (Media / DocumentReference)

- [ ] Define image upload UX and metadata mapping
- [ ] Add `Media` / `DocumentReference` adapter + API hooks
- [ ] Replace disabled legacy `picture` with FHIR-backed flow

### Next tasks (Practitioner)

- [ ] Define `PractitionerVM` and mapper
- [ ] Migrate doctor list/create/edit from legacy model to FHIR `Practitioner`
- [ ] Align Observation performer selection with Practitioner data source

## Non-Goals in this batch

- No destructive data migration.
- No large UI redesign.
- No removal of existing Phase 1 temperature Observation workflow.
