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

### M3 (Planned)

- [ ] Expand key coverage to create/edit forms and validation/error messages
- [ ] Add URL/query based locale persistence strategy evaluation
- [ ] Add smoke checklist for cross-locale UI paths

## Risks

- Existing views mix literal strings and translation calls, so full migration should be incremental
- Locale switching currently uses session + back redirect; deep-link locale behavior is deferred
- Legacy Blade pages may need targeted cleanups before wider i18n rollout

## Acceptance Criteria

1. Switching locale between `en` and `zh_TW` updates core navigation labels
2. Patient and doctor list page common title/search/add labels are translated by keys
3. Medical record landing title is translated by key
4. Existing Phase 1 and Phase 2 workflows remain operational

## Backend Dependencies

- No new backend API required for M2
- Existing Phase 1/2 FHIR routes and payload contracts remain unchanged
