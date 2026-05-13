# Phase 10K-B - Read-only Viewer Demo Readiness Evidence

## 1. Purpose

This evidence package records the demo readiness review for the FHIR Read-only Lesion / Clinical Evidence Viewer.

The purpose is to preserve the current internal demo readiness decision, demo script, demo-able scope, explain-only scope, and remaining risks.

This phase is documentation-only evidence.

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

908c94c6dd3335d7c2ce847811ffb1d4d4a90081
docs(fhir): index phase 10i mock ingestion diagnosis

## 4. Route Safety Baseline

Route safety still holds:

GET|HEAD api/lesions
GET|HEAD api/lesions/{lesion}
GET|HEAD lesions
GET|HEAD lesions/{lesion}
GET|HEAD dev/fhir/mock-ingestion
POST     dev/fhir/mock-ingestion/preview

No lesion POST / PUT / PATCH / DELETE / create / store / edit / update / destroy routes were found.

## 5. Demo Readiness Decision

Decision:

Conditional Go for internal demo.

Conditions:

- Use Docker URL: http://localhost:8080
- Use an already authenticated browser session
- Present mock ingestion as explain-only fail-closed behavior
- Do not enable feature flags
- Do not clear cache
- Do not restart/rebuild Docker
- Do not modify environment
- Do not present this as production SMART/CDS/Gateway readiness
- Do not present this as formal ingestion readiness
- Do not present this as FHIR write readiness

## 6. Demo-able Items

The following can be demoed now, assuming an existing authenticated browser session is available:

- /login page reachability
- /lesions read-only lesion / clinical evidence list
- /lesions/lesion-001 read-only detail page
- missing lesion safe 404 behavior
- no visible create/edit/delete/save/upload/FHIR-write controls
- route safety: lesion routes remain GET|HEAD only
- mock ingestion preview fail-closed explanation
- docs/evidence index alignment for Phase 10C, 10E, and 10I-J1

## 7. Explain-only / Not Demo-able Items

The following should not be directly demoed yet:

- /dev/fhir/mock-ingestion browser page, because Docker intentionally returns 404 while the dev-only feature flag is disabled
- mock preview static page in Docker
- temporary feature flag enablement
- real formal ingestion
- FHIR write
- approval/signoff persistence
- production SMART/CDS/Gateway runtime
- responsive visual QA across screen sizes
- screenshot evidence, unless captured in a later scoped phase

## 8. Demo Risks

Current demo risks:

- Working tree is still heavily dirty; use no broad staging or cleanup.
- No push has been performed.
- Mock ingestion page returns 404 by design; explain as fail-closed safety, not broken routing.
- Local PHP CLI cannot support authenticated QA because PDO drivers are missing for MySQL.
- Docker browser QA depends on an existing authenticated Chrome session.
- Missing lesion 404 is safe but still default-style, not polished.
- Detail view may still show generic Consent references fallback wording in one section.
- Screenshots are not yet preserved as a committed evidence package.
- Feature flag remains disabled.

## 9. Existing Evidence Inventory

Available evidence and checks:

- Phase 10C read-only contract / preview QA evidence package
- Phase 10E dirty tree cleanup plan
- Phase 10I-J1 mock ingestion middleware diagnosis evidence package
- Docs index entries in docs/README.md and docs/fhir/fhir-docs-index.md
- Phase 10H-B / 10H-E UI wording stabilization tests
- Current route safety confirmations
- Phase 10I-F Docker browser QA observations for /lesions, /lesions/lesion-001, and missing lesion 404
- Phase 10I-J3 diagnosis confirming mock ingestion 404 is expected fail-closed middleware behavior

## 10. Test Evidence

Current targeted tests passed:

LesionViewerUiTest: 4 passed, 75 assertions
FrontendReadOnlyUiTest: 9 passed, 128 assertions
ControlledIngestionPrototypeUiTest: 1 passed, 26 assertions
LesionViewerDocumentationTest: 3 passed, 71 assertions
ControlledIngestionPrototypeDocumentationTest: 2 passed, 33 assertions

Warnings were PHP 8.5 vendor deprecation warnings from Pest/Collision only.

## 11. Recommended Demo Script

1. Position the project as a Read-only Lesion / Clinical Evidence Viewer.
2. Explain the data flow:
   server / FHIR supplies already-backed clinical evidence; the frontend only displays it.
3. Open http://localhost:8080/login.
4. Use the existing authenticated browser session, then open http://localhost:8080/lesions.
5. On the list page, show:
   - read-only positioning
   - lesion summary
   - patient / review / FHIR resource count metadata
   - no create/edit/delete/save/upload/write controls
6. Open a lesion detail page, for example http://localhost:8080/lesions/lesion-001.
7. On detail, show:
   - subject metadata
   - review metadata
   - observations / conditions / diagnosticReports / documents / consents / encounters
   - grouped FHIR references
   - no approval/signoff/edit/write action
8. Open http://localhost:8080/lesions/nonexistent-or-invalid-id.
   - Explain safe 404 behavior: no stack trace, no secret/config exposure, no create/edit/upload suggestion.
9. Explain mock ingestion:
   - route exists
   - feature flag is disabled
   - 404 is fail-closed by design
   - not formal ingestion
   - not FHIR persistence
   - not lesion CRUD

## 12. Recommended Next Step

Recommended next step:

Option 10K-B-A: Selective commit this demo readiness evidence package only.

Alternative future options after commit:

- Option 10K-C: Capture screenshots for demo evidence only.
- Option 10K-D: Missing-record 404 UX review only.
- Option 10K-E: FHIR read integration verification only.
- Option 10K-F: Push readiness review only.

## 13. Final Confirmation

Files modified by this evidence package:

- docs/fhir/evidence/fhir-lesion-viewer-phase-10k-b-demo-readiness-review-20260513/README.md

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
