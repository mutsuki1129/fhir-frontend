# Phase X X3 Frontend Linkage Implementation (Stage 1)

## Scope

X3 Stage 1 delivers minimum frontend linkage rendering upgrade without contract-breaking backend changes.

Touched areas:

- `rekam/list`
- `rekam/pasien`
- `rekam/dokter`
- FHIR error-key mapping usage in `RekamController`, `PasienController`, `DokterController`

## 1) Linkage Rendering Upgrade (Stage 1)

## Rule Applied

1. **Explicit linkage preferred** in UI when resource identity and content are both present:
   - Condition: `id` + (`text` or `code`)
   - DocumentReference: `id` + `url`
2. **Fallback badges** only when explicit linkage criteria are not satisfied but fallback content exists:
   - legacy note fallback for Condition
   - URL-only fallback for DocumentReference

## UI Labels Added/Aligned

- `Linked condition`
- `Linked document reference`
- `Fallback: legacy note` (existing)
- `Fallback document reference` (new fallback state)

## 2) Cross-page Semantic Alignment

The same priority order is now used in:

- Medical-record main list
- Grouped-by-patient page
- Grouped-by-practitioner page

This reduces interpretation drift between three clinical entry views.

## 3) Error Key -> UI Key Consistency (401/403/NOT_FOUND/VALIDATION)

Controllers now map `FhirApiException::errorKey()` to `ui.error.*` keys for user-facing messages:

- `UNAUTHORIZED` -> `ui.error.unauthorized`
- `FORBIDDEN` -> `ui.error.forbidden`
- `PATIENT_NOT_FOUND` / `OBSERVATION_NOT_FOUND` -> `ui.error.not_found`
- `VALIDATION_ERROR` -> `ui.error.validation`

Affected controllers:

- `RekamController`
- `PasienController`
- `DokterController`

## Notes

- This is still Stage 1: deterministic backend linkage is not introduced in this batch.
- Existing patient-latest behavior remains active where explicit deterministic contract is unavailable.
