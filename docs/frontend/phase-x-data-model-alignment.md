# Phase X Data Model Alignment (Frontend)

Date: 2026-04-29

## Scope

Align medical-record frontend inputs and list rendering with
`C:\Users\clamp\Desktop\project\fhir server\fhir-model\DATA_MODEL.md`
without breaking existing routes/session locale mechanism.

## Implemented

1. Global locale switch visibility
   - Added persistent EN/中 buttons in shared navigation (desktop + responsive menu).
   - Kept existing `route('locale.switch')` and session locale behavior unchanged.

2. Rekam create/edit input path alignment
   - Added patient profile fields in create/edit forms:
     - name (existing patient selector)
     - birthDate (age derived at display)
     - gender
     - education
     - occupation
     - income / expense
     - interests
     - psychological traits
     - behavior patterns
     - biomarkers
     - treating practitioner (existing performer/generalPractitioner linkage)
     - national ID
     - NHI card
   - Added auto-prefill from selected patient profile for create/edit consistency.

3. Rekam list rendering alignment
   - Added aligned display fields on medical record cards:
     - age derived from `birthDate`
     - gender, education, occupation
     - income/expense
     - interests, psychological traits, behavior patterns, biomarkers
     - treating practitioner
     - national ID, NHI card

4. Mapper + sync path
   - Extended `PatientVM` with profile attributes required by DATA_MODEL alignment.
   - Extended `PatientMapper`:
     - `Patient.birthDate`, `Patient.gender`
     - `Patient.identifier` for `urn:tw:national-id` / `urn:tw:nhi-card`
     - `Patient.generalPractitioner`
     - profile extensions under `urn:tw:patient:*`
   - Added non-blocking patient profile sync in `RekamController` store/update flow.

## Safety / Fallback

- Observation create/update remains primary path and is not blocked by profile sync failures.
- Patient profile sync is non-blocking (best effort) to preserve existing clinical flow.
- Existing condition/document reference fallback behavior remains intact.

## Deferred / Risk

1. DATA_MODEL source encoding appears garbled in current environment output; mapping was implemented using detectable field semantics and identifier systems from the document.
2. Some profile concepts in DATA_MODEL can be modeled as Observation/CareTeam in stricter implementations. Current frontend alignment keeps a conservative Patient-centric path to avoid contract breakage.
3. Full i18n coverage of all newly introduced labels remains incremental.
