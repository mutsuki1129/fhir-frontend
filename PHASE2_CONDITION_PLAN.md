# Phase 2 Condition Plan

## Goal

Move clinical condition input from legacy `kondisi` note to FHIR `Condition` while preserving existing Phase 1 Observation behavior.

## Minimal Viable Flow (implemented)

1. Create medical record (`/rekam/create`)
   - keep Observation fields (patient, temperature, effective datetime)
   - keep legacy `kondisi` note field
   - add `condition_code` and `condition_text` inputs
   - submit Observation first, then sync Condition

2. Edit medical record (`/rekam/{id}/edit`)
   - load Observation as primary record
   - load latest Condition for selected patient
   - allow update of `condition_code` and `condition_text`
   - if Condition id exists, update Condition; otherwise create Condition

3. List medical records (`/rekam`)
   - continue showing Observation values
   - show Condition summary (`text`, optional `code`) per patient
   - continue showing legacy note if present

## Contract and Safety

- Observation remains source of truth for Phase 1 temperature workflow.
- Condition sync is additive and non-blocking.
- If Condition API fails:
  - Observation save/update still succeeds
  - UI shows warning message
  - legacy note fallback remains visible

## Data Mapping

### ViewModel

- `ConditionVM`
  - `id`
  - `patientId`
  - `code`
  - `text`
  - `recordedDate`
  - `note`

### Mapper

- `ConditionMapper::fromFhirCondition`
- `ConditionMapper::toFhirCondition`

## Open Risks

- Current list matching is patient-based latest Condition, not observation-specific linkage.
- Condition search currently fetches a global bundle then filters client-side.
- Backend profile constraints for required Condition fields may differ by environment.

## Follow-up

- Add observation-to-condition linkage strategy when backend contract is finalized.
- Add tests for:
  - Condition create/update success path
  - Condition API failure fallback path
  - list rendering with/without Condition data
