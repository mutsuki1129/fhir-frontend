# Phase X UI Baseline (X0)

## Scope

This baseline captures current information architecture and constraints for:

- Medical records pages (`/rekam`, grouped variants)
- Patients page (`/pasiens`)
- Doctors page (`/dokters`)

Phase X X0 is documentation-only; no functional contract change is included here.

## Current Information Architecture

## 1) Medical Records

- Entry routes:
  - `/rekam` (card list)
  - `/rekam/pasien` (grouped by patient)
  - `/rekam/dokter` (grouped by performer)
- Core data blocks:
  - Observation temperature (`value`, `effective`, note)
  - Condition status (available / fallback note / missing)
  - DocumentReference status (available / missing)
- Relationship semantics:
  - Patient and performer are displayed directly in card context
  - Condition and DocumentReference use patient-latest inference

## 2) Patients

- Entry route: `/pasiens`
- Core data blocks:
  - Name / email / phone list
  - Search + sort + pagination
  - Edit action
- Data source semantics:
  - FHIR Patient facade flow

## 3) Doctors

- Entry route: `/dokters`
- Core data blocks:
  - Name / email / phone list
  - Search + sort + pagination
  - Edit + delete actions
- Data source semantics:
  - FHIR Practitioner facade flow (M4 step-1)
  - Legacy columns kept as placeholders for compatibility

## Shared UI State Patterns

- Success banner (`session('status')`) for post-action feedback
- FHIR error banner (`$errors->has('fhir')`) for non-blocking error display
- Empty and error components in key list views
- i18n key usage on touched list headers and search controls

## Pain Points

1. Mixed translation strategy:
   - Key-based and hardcoded strings coexist in many create/edit forms.
2. Doctor legacy-field mismatch:
   - `age/height/weight/role/profile picture` remain visible but not in Practitioner contract.
3. Linkage interpretation risk:
   - Condition/DocumentReference are patient-latest, not strict per-observation linkage.
4. Grouped pages complexity:
   - Nested loops in grouped views increase maintenance cost for further UI changes.

## Keep (Do Not Break in Phase X Start)

1. Existing route surface and backend contract assumptions.
2. Current status/empty/error visual behavior in list pages.
3. Practitioner source alignment between doctor page and rekam performer.
4. Non-blocking fallback behavior for Condition/DocumentReference.

## Phase X X0 Guardrails

- No destructive data/UI rewrites.
- No contract-breaking payload or route changes.
- Prefer incremental toggled rollout (feature flags) before any visible redesign.
