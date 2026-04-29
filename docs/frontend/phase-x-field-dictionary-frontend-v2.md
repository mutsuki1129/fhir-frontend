# Phase X Field Dictionary Frontend v2

## Scope

Resources covered in this dictionary:

- Patient
- Observation (temperature-focused UI baseline)
- Practitioner
- Condition
- DocumentReference

## Patient

| UI field | Source field (FHIR / VM) | Fallback rule |
|---|---|---|
| Patient ID | `Patient.id` / `PatientVM.id` | required; no fallback |
| Patient name | `Patient.name[].text` or composed name / `PatientVM.name` | fallback to `Patient.id` |
| Email | `Patient.telecom[system=email].value` / `PatientVM.email` | `-` in list display |
| Phone | `Patient.telecom[system=phone].value` / `PatientVM.phone` | `-` in list display |
| Photo URL | `Patient.photo[0].url` / `PatientVM.photoUrl` | default avatar in profile UI |

## Observation

| UI field | Source field (FHIR / VM) | Fallback rule |
|---|---|---|
| Observation ID | `Observation.id` / `TemperatureObservationVM.id` | required; no fallback |
| Patient reference | `Observation.subject.reference` / `TemperatureObservationVM.patientId` | required for clinical card |
| Patient display | include-resolved patient name / `TemperatureObservationVM.patientDisplay` | fallback to patient ID |
| Performer reference | `Observation.performer[0].reference` / `TemperatureObservationVM.performerId` | nullable |
| Performer display | include-resolved practitioner / `TemperatureObservationVM.performerDisplay` | fallback to performer ID, else `-` |
| Temperature value | `Observation.valueQuantity.value` / `TemperatureObservationVM.valueCelsius` | required in current flow |
| Effective datetime | `Observation.effectiveDateTime` / `TemperatureObservationVM.effectiveDateTime` | `-` |
| Legacy note | `Observation.note[0].text` / `TemperatureObservationVM.note` | hidden if empty |

## Practitioner

| UI field | Source field (FHIR / VM) | Fallback rule |
|---|---|---|
| Practitioner ID | `Practitioner.id` / `PractitionerVM.id` | required |
| Name | `Practitioner.name[].text` or composed name / `PractitionerVM.name` | fallback to practitioner ID |
| Email | `Practitioner.telecom[system=email].value` / `PractitionerVM.email` | `-` |
| Phone | `Practitioner.telecom[system=phone].value` / `PractitionerVM.phone` | `-` |

## Condition

| UI field | Source field (FHIR / VM) | Fallback rule |
|---|---|---|
| Condition ID | `Condition.id` / `ConditionVM.id` | nullable in display context |
| Patient reference | `Condition.subject.reference` / `ConditionVM.patientId` | required for lookup grouping |
| Condition code | `Condition.code.coding[0].code` / `ConditionVM.code` | optional |
| Condition text | `Condition.code.text` or display / `ConditionVM.text` | optional |
| Recorded date | `Condition.recordedDate` / `ConditionVM.recordedDate` | optional |
| Condition note | `Condition.note[0].text` / `ConditionVM.note` | optional |

## DocumentReference

| UI field | Source field (FHIR / VM) | Fallback rule |
|---|---|---|
| DocumentReference ID | `DocumentReference.id` / `DocumentReferenceVM.id` | nullable in display context |
| Patient reference | `DocumentReference.subject.reference` / `DocumentReferenceVM.patientId` | required for lookup grouping |
| Title | `content[0].attachment.title` / `DocumentReferenceVM.title` | fallback label: `Open attachment` |
| URL | `content[0].attachment.url` / `DocumentReferenceVM.url` | if empty, show `No document reference` |
| Content type | `content[0].attachment.contentType` / `DocumentReferenceVM.contentType` | optional |
| Date | `DocumentReference.date` or `meta.lastUpdated` / `DocumentReferenceVM.date` | optional |

## Cross-resource Fallback Summary

1. Name resolution always prefers explicit display text; fallback to resource ID.
2. Optional contact fields render as `-` in list pages.
3. Condition and DocumentReference are currently patient-latest resolved in medical-record UI context.
4. Legacy observation note remains fallback display when structured Condition is absent.
