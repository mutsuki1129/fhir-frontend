# Phase 10I-J1 - Mock Ingestion Middleware Diagnosis Evidence

## 1. Purpose

This package records the Docker route/access diagnosis for the dev-only mock ingestion preview route.

The purpose is to preserve evidence that Docker `404 Not Found` for `/dev/fhir/mock-ingestion` is expected fail-closed behavior caused by the disabled controlled ingestion prototype feature flag.

This is documentation-only evidence.

No runtime behavior was changed.

## 2. Scope

This phase is evidence-only.

No implementation was performed.

No feature flag was enabled.

No cache was cleared.

No Docker container was restarted or rebuilt.

No route/controller/service/view/config/env file was modified.

No staging or commit was performed in this phase unless separately authorized later.

## 3. Current Checkpoint

Latest commit before this evidence package:

```text
cc6c364adf80b402e8818478f6ae7c34b4453243
docs(fhir): stabilize readonly lesion viewer ui wording
```

## 4. Route Safety Baseline

Host and Docker app route-list both confirmed:

```text
GET|HEAD api/lesions
GET|HEAD api/lesions/{lesion}
GET|HEAD lesions
GET|HEAD lesions/{lesion}
GET|HEAD dev/fhir/mock-ingestion
POST     dev/fhir/mock-ingestion/preview
```

No lesion POST / PUT / PATCH / DELETE / create / store / edit / update / destroy routes were found.

## 5. Middleware Boundary

Mock ingestion routes use:

```text
web
App\Http\Middleware\EnsureControlledIngestionPrototypeEnabled
```

The middleware requires all of these conditions:

- app environment is local or testing
- fhir.controlled_ingestion_prototype.enabled is true
- fhir.controlled_ingestion_prototype.mode is mock
- fhir.controlled_ingestion_prototype.allow_fhir_write is false

If any condition fails, the middleware aborts with 404.

This is intentional fail-closed behavior.

## 6. Docker Runtime Config Finding

Docker runtime config showed:

```text
app()->environment(): local

fhir.controlled_ingestion_prototype:
- enabled: false
- mode: mock
- allow_fhir_write: false
```

Docker `.env` contained:

```text
APP_ENV=local
FHIR_BASE_URL=http://host.docker.internal:8091/fhir
```

Docker `.env` did not contain:

```text
FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED
```

Therefore `config/fhir.php` defaults the controlled ingestion prototype enabled flag to false.

## 7. HTTP Behavior Finding

Docker URL checks showed:

```text
GET/HEAD http://localhost:8080/dev/fhir/mock-ingestion
404 Not Found

HEAD http://localhost:8080/dev/fhir/mock-ingestion/preview
405 Method Not Allowed
allow: POST

HEAD http://localhost:8080/lesions
302 Found -> /login
```

Interpretation:

- `/dev/fhir/mock-ingestion` returns 404 because middleware intentionally fails closed.
- `/dev/fhir/mock-ingestion/preview` exists as a POST route, as indicated by 405 on HEAD.
- `/lesions` remains available behind authentication and is independent from the mock ingestion feature flag.

## 8. Safety Interpretation

The disabled mock ingestion preview state is safe.

The route exists in host and Docker.

The source exists in Docker.

The preview page is intentionally inaccessible until explicitly enabled in a local/testing controlled environment.

Current state exposes:

- no formal ingestion
- no lesion CRUD
- no FHIR persistence
- no approval/signoff persistence
- no production FHIR write behavior

This diagnosis supports the read-only lesion viewer safety boundary.

## 9. Remaining Risks

- Mock preview page is still not browser-confirmed in Docker because the feature flag is disabled.
- Enabling the flag requires explicit authorization.
- A future temporary local/Docker visual QA pass may be useful, but should be separately scoped and non-committed.
- No cache clear, route clear, view clear, container restart, or rebuild was performed.
- No source change should be made before the diagnosis is accepted.

## 10. Recommended Next Step

Recommended next step:

```text
Option 10I-J1-A: Selective commit this evidence package only.
```

Alternative future options after commit:

- Option 10I-B: Create broader visual QA evidence package only.
- Option 10I-C: FHIR read integration verification only.
- Option 10I-J5: Temporarily enable mock preview in non-committed local/Docker env for browser QA only, if explicitly authorized.

## 11. Final Confirmation

Files modified by this evidence package:

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10i-j1-mock-ingestion-middleware-diagnosis-20260513/README.md`

Runtime files modified: No

Route/controller/service/view/config/env/docker files modified: No

Feature flag enabled: No

Cache cleared: No

Docker restarted/rebuilt: No

Formal ingestion introduced: No

Lesion CRUD introduced: No

FHIR persistence introduced: No

Approval/signoff persistence introduced: No

Unrelated dirty/untracked files touched: No
