# Phase X Feature Flags Draft (X0)

## Goal

Define minimum feature-flag design for safe Phase X rollout without breaking existing Phase 3 behavior.

## Flag Set (Minimum)

1. `phase_x_ui_enabled`
2. `phase_x_linkage_enabled`

## 1) `phase_x_ui_enabled`

## Purpose

Toggle new Phase X UI surfaces (layout refinements, copy unification, componentized list blocks).

## Default

- `false` (keep current Phase 3 UI)

## Rollout Strategy

1. Local/dev: enable for implementation checks.
2. Staging: enable for selected test sessions.
3. Production: progressive enable after smoke + rollback plan confirmation.

## Guardrail

- When `false`, existing page structures and actions remain unchanged.

## 2) `phase_x_linkage_enabled`

## Purpose

Toggle stricter linkage presentation behavior for Observation <-> Condition/DocumentReference (once backend linkage contract is available).

## Default

- `false` (keep patient-latest inference behavior)

## Rollout Strategy

1. Enable only after backend exposes stable linkage fields/contracts.
2. Run side-by-side checks against existing inference display.
3. Switch default after validation and rollback script readiness.

## Guardrail

- When `false`, current fallback badges and warnings remain as-is.

## Suggested Config Surface

- `.env` keys (draft):
  - `PHASE_X_UI_ENABLED=false`
  - `PHASE_X_LINKAGE_ENABLED=false`
- App config mapping:
  - `config/features.php` (or equivalent) should map env vars to booleans.

## Usage Pattern (Draft)

1. Controller/view layer reads typed feature flags.
2. Branch only on presentation behavior, not core create/update/delete contracts.
3. Keep analytics/log marker on flag-enabled paths for rollback diagnostics.

## Non-goals in X0

- No full flag framework implementation in this batch.
- No runtime behavior migration in this batch.
