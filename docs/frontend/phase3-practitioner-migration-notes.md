# Phase 3 M4 Practitioner Migration Notes (Frontend)

## Goal

Migrate doctor page data source from local `dokters` table semantics to FHIR `Practitioner` semantics with minimal UI change.

## Delivered in M4 (Step 1)

1. `DokterController` now uses `FhirApiClient` + `PractitionerMapper`
2. `dokter` list/create/edit core flow now reads/writes `Practitioner`
3. Rekam performer and doctor list now share the same upstream resource type (`Practitioner`)
4. Kept existing page structure and routes to avoid broad UI changes

## Data Mapping in this Step

- `name` -> `Practitioner.name[0].text`
- `email` -> `Practitioner.telecom(system=email)`
- `phone_number` -> `Practitioner.telecom(system=phone)`

## Compatibility/Fallback Notes

- Legacy doctor profile fields (`age`, `height`, `weight`, `role_id`, `profile_picture`) are not part of current Practitioner contract.
- For list/edit rendering compatibility, these fields are provided as placeholder values in view model adaptation.
- Profile picture update endpoint is intentionally non-blocking with clear message in this phase.

## Risks

- Existing doctor page still visually shows legacy columns/inputs that are now deferred fields.
- Sorting currently keeps reliable support for name-based sort in Practitioner flow.

## Next Step (Deferred)

- Replace legacy columns with Practitioner-aligned fields only
- Remove or redesign non-contract profile picture path
- Add explicit OperationOutcome-to-UI mapping on doctor forms
