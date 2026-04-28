# Phase 3 M5 Smoke / Regression Checklist (Frontend)

## Scope

Regression focus for:

- Medical records page (`/rekam`)
- Patients page (`/pasiens`)
- Doctors page (`/dokters`)

## Pre-check

1. Login as admin user
2. Ensure backend services are reachable (Patient / Practitioner / Observation, plus optional Condition / DocumentReference)

## Medical Records (`/rekam`)

1. Success banner appears after create/update/delete action
2. FHIR error banner appears when backend returns OperationOutcome
3. Empty state shows when there is no Observation
4. Error state shows with retry when observation list loading fails
5. Condition status is shown consistently:
   - Condition available
   - Fallback: legacy note
   - Condition missing
6. DocumentReference status is shown consistently:
   - DocumentReference available
   - No document reference

## Patients (`/pasiens`)

1. Success banner appears after create/update actions
2. FHIR error banner appears when service returns failure
3. Empty state behavior:
   - No data: `ui.patients.empty`
   - With search keyword but no result: `ui.patients.empty_filtered`
4. Error state shows retry action when list loading fails
5. Search and pagination still work

## Doctors (`/dokters`)

1. List is sourced from Practitioner
2. Success banner appears after create/update/delete
3. FHIR error banner appears when service returns failure
4. Search by name/email/phone still works
5. Name sort asc/desc still works in Practitioner flow
6. Edit path updates Practitioner data and returns to list

## i18n Consistency Checks

1. Navigation labels change between `en` and `zh_TW`
2. Patients/doctors list title and search controls use `ui.*` keys
3. Regression check for accidental hard string additions in touched pages

## Deferred from M5

- Full i18n migration for all create/edit field labels and validation messages
- Automated smoke execution script (this checklist is manual-first)
