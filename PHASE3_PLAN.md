# Frontend Phase 3 Plan

## Goal

Build a stable frontend integration layer on top of existing Phase 1/2 behavior, then harden UX, data mapping, and multilingual readiness without breaking backend contracts.

## Scope

1. Data field sync baseline across Patient / Practitioner / Observation / Condition / DocumentReference
2. Condition and DocumentReference flow hardening
3. Practitioner integration stabilization for doctor and medical-record related views
4. i18n baseline and extensible language switching structure

## Non-goals

- No backend contract changes
- No destructive migration of existing Phase 1 behavior
- No broad UI redesign in this phase

## Milestones

### M1 (Completed)

- [x] Field-sync baseline document created: `docs/frontend/phase3-field-sync-baseline.md`
- [x] Mapper-level comments aligned for follow-up development
- [x] Phase 3 task source and TODO synchronized

### M2 (Completed in this batch)

- [x] Minimal i18n skeleton added (`en` / `zh_TW`)
- [x] Locale switch route and middleware hook added
- [x] Navigation + patient/doctor/medical-record common labels migrated to key-based translation
- [x] i18n key guideline document added: `docs/frontend/phase3-i18n-key-guideline.md`

### M3 (Completed in this batch)

- [x] Cross-page linkage hints aligned on medical-record list/grouped views
- [x] Condition/legacy-note/document-reference status display made consistent on list, patient-group, and practitioner-group pages
- [x] Patient-latest inference warning added to reduce timeline misunderstanding
- [x] UI notes documented: `docs/frontend/phase3-linkage-ui-notes.md`

### M4 (In progress - Practitioner migration step 1 completed)

- [x] Doctor controller moved to FHIR Practitioner facade flow (list/create/edit)
- [x] Rekam performer semantics aligned with doctor data source (`Practitioner`)
- [x] Migration notes documented: `docs/frontend/phase3-practitioner-migration-notes.md`
- [ ] Remove legacy doctor-only UI fields not in current Practitioner contract
- [ ] Add explicit OperationOutcome mapping for doctor create/edit form errors
- [ ] Expand key coverage to create/edit forms and validation/error messages
- [ ] Add URL/query based locale persistence strategy evaluation
- [ ] Add smoke checklist for cross-locale UI paths

### M5 (Completed in this batch - pre-freeze regression tightening)

- [x] Regression convergence on medical-record/patient/doctor pages for success/error/empty visibility
- [x] i18n key consistency tightened on touched list pages to reduce hard-string drift
- [x] Added manual smoke/regression checklist: `docs/frontend/phase3-m5-smoke-checklist.md`

### M6 (Completed in this batch - regression acceptance)

- [x] Re-checked M5 checklist coverage on medical-record/patient/doctor paths
- [x] Re-checked i18n key usage consistency on key list flows (`en` / `zh_TW`)
- [x] Re-validated loading/empty/error/success display convergence on touched pages

### M7 (Completed in this batch - release convergence)

- [x] Added release readiness report: `docs/frontend/phase3-release-readiness.md`
- [x] Updated Phase 3 status and deferred-risk ledger for freeze handoff

## Risks

- Existing views mix literal strings and translation calls, so full migration should be incremental
- Locale switching currently uses session + back redirect; deep-link locale behavior is deferred
- Legacy Blade pages may need targeted cleanups before wider i18n rollout
- Condition/DocumentReference currently use patient-latest inference, not strict per-observation linkage

## Acceptance Criteria

1. Switching locale between `en` and `zh_TW` updates core navigation labels
2. Patient and doctor list page common title/search/add labels are translated by keys
3. Medical record landing title is translated by key
4. Existing Phase 1 and Phase 2 workflows remain operational
5. Linkage statuses are displayed consistently in list, patient-group, and practitioner-group views

## Backend Dependencies

- No new backend API required for M2
- Existing Phase 1/2 FHIR routes and payload contracts remain unchanged
