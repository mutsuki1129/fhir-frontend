# Phase X Smart Client Notes (X1)

## Scope

This note defines the minimum frontend-aligned SMART on FHIR integration baseline for Phase X X1.

No full SMART login flow is implemented in this batch; this is a contract-ready baseline and config hook.

## Smart Configuration Read Location

Primary config source:

- `config/services.php` -> `services.fhir.*`

Planned keys (added as non-breaking hooks):

- `services.fhir.smart_enabled`
- `services.fhir.smart_well_known_url`
- `services.fhir.smart_client_id`
- `services.fhir.smart_scopes`

Expected env variables:

- `FHIR_SMART_ENABLED`
- `FHIR_SMART_WELL_KNOWN_URL`
- `FHIR_SMART_CLIENT_ID`
- `FHIR_SMART_SCOPES`

## SMART Metadata Endpoint

Recommended discovery endpoint:

- `/.well-known/smart-configuration` (server-specific base)

Frontend/planning implication:

1. Resolve authorization/token endpoints from smart-configuration
2. Validate requested scopes against exposed capability metadata

## Scope Mapping (Minimum for Current Read Flows)

For current Phase X planning baseline:

1. Patient read
   - Scope: `patient/Patient.read`
   - Corresponding frontend flow: patient list/detail read paths (`/pasiens`, patient edit load)
2. Observation read
   - Scope: `patient/Observation.read`
   - Corresponding frontend flow: rekam list/grouped/detail read paths (`/rekam*`)

Note:

- Practitioner/Condition/DocumentReference scopes are not mandated in this minimum X1 baseline, but may be appended in follow-up.

## Frontend Error Prompt Strategy (401 / 403)

## 401 Unauthorized

User-facing strategy:

1. Show non-blocking auth-expired message:
   - "Session expired or unauthorized. Please sign in again."
2. Keep current page state when possible.
3. Provide re-auth entry action.

Backend/frontend integration note:

- `FhirApiException` now normalizes `401` to error key `UNAUTHORIZED` (Phase X X1 hook).

## 403 Forbidden

User-facing strategy:

1. Show non-blocking permission message:
   - "You do not have permission to access this data scope."
2. Hide destructive/restricted actions for the current session context.
3. Keep navigation available; avoid full hard-fail screen unless route cannot render.

Backend/frontend integration note:

- `FhirApiException` now normalizes `403` to error key `FORBIDDEN` (Phase X X1 hook).

## Non-goals in X1

- No OAuth redirect implementation
- No token storage implementation
- No runtime replacement of existing session login flow

## Next Suggested Steps

1. Add feature-flagged smart auth adapter (`phase_x_ui_enabled` compatible)
2. Add centralized frontend error copy table keyed by `UNAUTHORIZED` / `FORBIDDEN`
3. Add smoke test cases for simulated 401/403 responses
