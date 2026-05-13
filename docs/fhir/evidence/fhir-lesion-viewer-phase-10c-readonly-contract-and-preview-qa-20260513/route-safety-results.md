# Route Safety Results

## Lesion Viewer Routes

Phase 10C reconfirmed:

- `/lesions` is GET|HEAD only.
- `/lesions/{lesion}` is GET|HEAD only.
- `/api/lesions` is GET|HEAD only.
- `/api/lesions/{lesion}` is GET|HEAD only.

No lesion-related POST, PUT, PATCH, DELETE, create, store, edit, update, or destroy route was introduced.

## Dev-only Mock Ingestion Preview Routes

Phase 10C.3 confirmed:

- `GET /dev/fhir/mock-ingestion`
- `POST /dev/fhir/mock-ingestion/preview`

`POST /dev/fhir/mock-ingestion/preview` is not formal ingestion. It is dev-only, mock-only, preview-only, and must remain behind the controlled ingestion prototype guard.

## Runtime Boundary

No HAPI backend route, docker route, env runtime, SMART production runtime, CDS runtime, Gateway runtime, Patient CRUD, Observation CRUD, Rekam CRUD, Practitioner CRUD, DocumentReference/Binary upload/delete/purge, or `FhirApiClient::expungeDeletedResource` behavior was changed in Phase 10C.5.
