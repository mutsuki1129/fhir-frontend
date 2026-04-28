# Phase 3 M3 Linkage UI Notes

## Scope

This batch adds minimum cross-page linkage consistency for:

- `rekam/list`
- `rekam/pasien`
- `rekam/dokter`

No backend contract was changed.

## UI Rules

1. Each record card should show Observation core fields:
   - patient or performer context
   - body temperature
   - effective datetime
2. Condition status is shown with consistent priority:
   - `Condition available` when Condition text/code exists
   - `Fallback: legacy note` when Condition is empty but Observation note exists
   - `Condition missing` otherwise
3. DocumentReference status is shown consistently:
   - `DocumentReference available` + link when URL exists
   - `No document reference` otherwise

## Patient-Latest Inference Clarification

- Condition and DocumentReference are currently resolved per patient by latest record in that resource stream.
- They may not be timestamp-aligned with the current Observation card.
- Each grouped page now displays a non-blocking note to reduce interpretation risk.

## Fallback Behavior

- If Condition or DocumentReference service is unavailable, warning banners remain non-blocking.
- Observation and legacy note remain visible and editable.

## Deferred

- Strict timeline linkage between a specific Observation and a specific Condition/DocumentReference is deferred until backend exposes explicit linkage fields/contract.
