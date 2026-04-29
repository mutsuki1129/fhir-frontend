# Phase X Linkage Rendering Rules

## Purpose

Define rendering rules for current and target linkage behavior in medical-record UI.

## Terminology

- **Patient-latest linkage**: Condition/DocumentReference selected by latest resource per patient.
- **Deterministic linkage**: Condition/DocumentReference linked by explicit relation to a specific Observation/event.

## A) Current Rule Set (Patient-latest)

Applies to current `/rekam` list and grouped variants.

1. Resolve Observation list first.
2. Build patient ID set from observations.
3. For each patient:
   - fetch Condition set, choose latest by recorded date
   - fetch DocumentReference set, choose latest by date
4. Render status badges:
   - Condition: available / fallback legacy note / missing
   - DocumentReference: available / missing
5. Display linkage warning note:
   - current record may not share timestamp with displayed Condition/DocumentReference.

## Current Rendering Priority

### Condition block

1. If `Condition.text` or `Condition.code` exists: render structured condition.
2. Else if `Observation.note` exists: render `Fallback: legacy note`.
3. Else: render `Condition missing`.

### DocumentReference block

1. If `DocumentReference.url` exists: render link block.
2. Else: render `No document reference`.

## B) Target Rule Set (Deterministic Linkage)

Target state for Phase X later milestones when backend linkage contract is available.

1. Resolve explicit linkage key from Observation (or visit/event context).
2. Render only Condition/DocumentReference attached to same linkage key.
3. Remove patient-latest warning when deterministic relation is proven.
4. If deterministic target missing:
   - show `linked data missing` hint, not patient-latest substitution.

## Transition Rule (Compatibility Window)

During migration:

1. Default remains patient-latest unless feature flag and backend readiness are both true.
2. Use feature flag gate:
   - `phase_x_linkage_enabled = false` => current patient-latest behavior
   - `phase_x_linkage_enabled = true` => deterministic mode (when supported)
3. Keep non-blocking rendering even when linkage service is degraded.

## Risk Controls

1. Do not silently mix deterministic and patient-latest in the same card without explicit marker.
2. Always provide a visible hint when fallback mode is active.
3. Preserve legacy note fallback until deterministic coverage is complete.
