# FHIR Read-only Lesion Viewer Data Contract

## Purpose

The FHIR Read-only Lesion Viewer is a Laravel frontend display surface for lesion-related summaries derived from server-side sources and contract-defined APIs. A lesion record in this viewer is a read-only view model. It is not a new FHIR Resource and does not create, edit, approve, sign off, upload, delete, purge, or persist clinical data.

The current FHIR-backed source uses DiagnosticReport-centered aggregation with optional linked metadata summaries. The current metadata status is `read-only-aggregation-v3`.

## Repository Source

- Default source: `mock`
- Config key: `fhir.lesion_viewer_source`
- Environment variable: `FHIR_LESION_VIEWER_SOURCE`
- Allowed values: `mock`, `fhir`
- Unknown values fail safe to the mock repository.

`mock` returns synthetic display records. `fhir` uses the read-only `FhirBackedLesionRepository`.

## Read-only Boundary

Allowed lesion viewer routes:

- `GET /lesions`
- `GET /lesions/{lesion}`
- `GET /api/lesions`
- `GET /api/lesions/{lesion}`

The Lesion Viewer does not provide lesion create, edit, update, delete, upload, approval, signoff, persistence, Gateway ingestion, SMART production activation, CDS runtime activation, clinical advice, automatic diagnosis, or treatment recommendation. The FHIR-backed repository must not write to the FHIR Server.

## FHIR-backed Meta

`GET /api/lesions` with `source=fhir` returns metadata like:

```json
{
  "readOnly": true,
  "source": "fhir-backed-lesion-repository",
  "contract": "docs/fhir/lesion-viewer-data-contract.md",
  "aggregation": "diagnostic-report-centered",
  "enrichment": {
    "patient": true,
    "observations": true,
    "encounter": true,
    "conditions": true,
    "documents": true,
    "consents": true
  },
  "status": "read-only-aggregation-v3"
}
```

FHIR read/search failures may add `lastError` with a safe exception class name only. They must not leak credential values, connection strings, or sensitive payload content.

## API Collection Response

`GET /api/lesions` returns:

```json
{
  "data": [
    {
      "lesionId": "lesion-report-001",
      "title": "Mock lesion-related report candidate",
      "status": "final",
      "severity": "unknown",
      "source": "clinician-reviewed workflow",
      "subject": {
        "patientReference": "Patient/patient-001",
        "displayId": "P-001",
        "gender": "unknown",
        "ageRange": "not-displayed"
      },
      "summary": "Read-only DiagnosticReport summary.",
      "clinicalStatus": "unknown",
      "verificationStatus": "unknown",
      "review": {
        "reviewStatus": "pending-review",
        "reviewedAt": "2026-05-12T00:00:00Z",
        "reviewedBy": "clinician-reviewed workflow"
      },
      "resources": {
        "observations": ["Observation/obs-001", "Observation/obs-002"],
        "conditions": [],
        "diagnosticReports": ["DiagnosticReport/report-001"],
        "documents": [],
        "consents": [],
        "encounters": ["Encounter/encounter-001"]
      },
      "lastUpdated": "2026-05-12T00:00:00Z"
    }
  ],
  "meta": {
    "readOnly": true,
    "source": "fhir-backed-lesion-repository",
    "contract": "docs/fhir/lesion-viewer-data-contract.md",
    "aggregation": "diagnostic-report-centered",
    "status": "read-only-aggregation-v3"
  }
}
```

When a FHIR DiagnosticReport search fails, `data` remains an empty array and the API must not return a 500 for the viewer surface.

## API Detail Response

`GET /api/lesions/{lesion}` returns one lesion object with the same `meta` block.

Missing records return:

```json
{
  "message": "Lesion viewer record not found."
}
```

HTTP status: `404`.

## Required Fields

| Field | Meaning |
| --- | --- |
| `lesionId` | Viewer id, not a FHIR Resource id. The FHIR-backed rule is `lesion-{DiagnosticReport.id}`. |
| `title` | DiagnosticReport report title display. |
| `status` | FHIR DiagnosticReport status or source status. |
| `severity` | Viewer display field, commonly `unknown` for FHIR-backed data. |
| `source` | Performer display or `FHIR DiagnosticReport`. |
| `subject` | Patient reference for display context only. |
| `summary` | Read-only summary text. It must not be interpreted as clinical advice. |
| `clinicalStatus` | Viewer display field, commonly `unknown` for FHIR-backed data. |
| `verificationStatus` | Viewer display field, commonly `unknown` for FHIR-backed data. |
| `review` | Review metadata. Fallback is `pending-review`; the viewer must not pretend a record is signed off. |
| `resources` | Linked FHIR Resource reference arrays. |
| `lastUpdated` | `meta.lastUpdated`, `issued`, or `null`. |

## Phase 6 Patient / Observation / Encounter Enrichment

Phase 6 keeps the DiagnosticReport-centered aggregation and adds optional read-only enrichment for three direct references:

- `DiagnosticReport.subject.reference` may be read as `Patient/{id}` for a safe subject summary.
- `DiagnosticReport.result[]` may be read as `Observation/{id}` for observation evidence display.
- `DiagnosticReport.encounter.reference` may be read as `Encounter/{id}` for interaction or observation-event display.

The API contract remains backward compatible. Existing fields remain present. A new optional `enrichment` object may be included when `FHIR_LESION_VIEWER_SOURCE=fhir`:

```json
{
  "enrichment": {
    "patient": {
      "reference": "Patient/patient-001",
      "displayId": "P-001",
      "gender": "female",
      "birthDate": "masked",
      "status": "available"
    },
    "observations": [
      {
        "reference": "Observation/obs-001",
        "title": "Mock observation candidate",
        "status": "final",
        "effective": "2026-05-12T00:00:00Z",
        "valueSummary": "36.8 Cel",
        "statusNote": "available"
      }
    ],
    "encounter": {
      "reference": "Encounter/encounter-001",
      "status": "finished",
      "periodStart": "2026-05-12T00:00:00Z",
      "periodEnd": "2026-05-12T00:30:00Z",
      "class": "ambulatory",
      "statusNote": "available"
    }
  }
}
```

If an enriched resource cannot be read, the lesion remains available and the original FHIR reference is retained with an unavailable status. Patient enrichment must not display full name, full birth date, address, phone, government identifier, or complete sensitive identifiers. Observation values are displayed only as value summaries and are not interpreted as normal, abnormal, confirmed, high risk, or treatment-relevant. Encounter enrichment represents only an interaction or observation event and is not interpreted as a medical outcome.

## Phase 7 Condition / DocumentReference / Consent Linking

Phase 7 keeps all Phase 5 and Phase 6 fields and adds optional read-only linking arrays under `enrichment`:

```json
{
  "enrichment": {
    "conditions": [
      {
        "reference": "Condition/condition-001",
        "title": "Mock condition candidate",
        "clinicalStatus": "active",
        "verificationStatus": "provisional",
        "recordedDate": "2026-05-12T00:00:00Z",
        "statusNote": "available"
      }
    ],
    "documents": [
      {
        "reference": "DocumentReference/doc-001",
        "title": "Mock document metadata",
        "status": "current",
        "type": "clinical-note",
        "date": "2026-05-12T00:00:00Z",
        "contentType": "application/pdf",
        "statusNote": "available"
      }
    ],
    "consents": [
      {
        "reference": "Consent/consent-001",
        "status": "active",
        "scope": "patient-privacy",
        "periodStart": "2026-05-12T00:00:00Z",
        "periodEnd": null,
        "statusNote": "available"
      }
    ]
  }
}
```

If a linked resource cannot be read, the array item keeps only the FHIR reference and `statusNote=unavailable`. Linking failure must not make the frontend return 500.

Phase 7 meta status is `read-only-aggregation-v3`, with `conditions`, `documents`, and `consents` marked as available optional enrichment.

## Safety Semantics

- DiagnosticReport-centered aggregation does not equal automatic diagnosis.
- Condition linking does not equal frontend automatic diagnosis.
- DocumentReference metadata/reference display does not directly expose sensitive file content.
- Consent linking does not represent a medical result.
- AI-generated content is not clinician-confirmed content.
- No clinical advice, automatic diagnosis, or treatment recommendation is provided.
- No live FHIR write/update/delete, FHIR create/update/delete/patch/upload/purge, POST/PATCH/DELETE lesion route, SMART production activation, CDS runtime activation, or AI Agent direct write to a formal FHIR Server is provided.

## Deferred

- Gateway or adapter validation workflow for future ingestion.
- AI Agent input/output review workflow without direct FHIR Server writes.
- Expanded review provenance after formal healthcare governance approval.
