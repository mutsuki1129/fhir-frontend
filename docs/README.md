# 文件

## 建議結構

- `docs/frontend/README.md`
  - 前端基準、強化範圍與啟動備註
- `docs/frontend/commit-checklist.md`
  - 第一輪前端 commit checklist
- `docs/frontend/smoke-test.md`
  - Phase 1 regression 與 smoke test 備註

## Phase 1 備註

本 repo 的 Phase 1 工作聚焦在 FHIR `Patient` 與體溫 `Observation`。
`Condition`、`Media` / `DocumentReference` 與 `Practitioner` implementation 不屬於此處 Phase 1 範圍。

## FHIR Phase 3 Evidence

- `docs/fhir/evidence/phase-e1-encounter-read-list-detail-contract-hardening-20260511/`
  - Phase E1 Encounter read/list/detail contract hardening; mocked read-only Laravel tests, patient-compartment defense-in-depth, linked Observation/Condition display filtering, no live FHIR write/update/delete requests, and no CDS runtime activation.
- `docs/fhir/evidence/fhir-server-documentreference-metadata-static-profile-package-20260511/`
  - FHIR server DocumentReference metadata-only local/static profile package; local/static validation only, metadata/linkage only, not live-published, not live-validated through HAPI `$validate`, no live FHIR calls, no Binary lifecycle, no storage lifecycle, no attachment URL rendering or dereferencing, and no AuditEvent / Provenance writes.
- `docs/fhir/evidence/patient-observation-controlled-repair-phase-3a-20260511/`
  - Phase 3A backend temperature Observation profile hardening evidence; separate body-temperature profile, static validation only, no live FHIR write/update/delete requests, and no CDS runtime activation.
- `docs/fhir/phase-3-final-master-closure-report.md`
  - Phase 3 final master closure report; documentation-only final archive, no runtime activation approval, no execution gate enablement approval, and no clinical use approval.
- `docs/fhir/evidence/phase-3-runtime-action-rehearsal-plan-20260510/`
  - Phase 3-15 approval-controlled runtime action rehearsal plan templates 與 checklists。
- `docs/fhir/evidence/phase-3-runtime-action-candidate-design-20260510/`
  - Phase 3-16 single low-risk runtime action candidate design for `record_governance_rehearsal_marker`; documentation-only, no runtime action implementation.
- `docs/fhir/evidence/phase-3-runtime-action-rehearsal-readiness-review-20260510/`
  - Phase 3-17 readiness review for future rehearsal planning only; no runtime action implementation and no execution gate enablement.
- `docs/fhir/evidence/phase-3-non-executing-rehearsal-scenario-20260510/`
  - Phase 3-18 non-executing rehearsal scenario package for `record_governance_rehearsal_marker`; documentation-only, no runtime action implementation, no callable action, and no execution gate enablement.
- `docs/fhir/evidence/phase-3-non-executing-rehearsal-result-evidence-20260510/`
  - Phase 3-19 non-executing rehearsal result evidence package templates for `record_governance_rehearsal_marker`; documentation-only, no rehearsal execution, no runtime behavior, and no runtime activation approval.
- `docs/fhir/evidence/phase-3-runtime-governance-handoff-closure-20260510/`
  - Phase 3-20 runtime governance handoff and Phase 3 closure package; documentation-only, no runtime activation approval, no execution gate enablement approval, and no clinical use approval.

## FHIR Read-only Lesion Viewer Phase 9A

- `docs/fhir/gateway-ai-agent-adapter-contract.md`
  - Phase 9A Gateway / AI Agent Adapter Contract Draft; contract draft only, not Gateway runtime, not AI Agent runtime, not production gateway, and not FHIR write pipeline.
- `docs/fhir/contracts/gateway-ai-agent-payload.schema.json`
  - Phase 9A payload schema draft for documentation only; not runtime validation and not a formal production specification.
- `docs/fhir/ai-agent-data-classification.md`
  - Defines ai-generated, ai-suggested, clinician-observed, clinician-reviewed, signed-off, rejected, and superseded distinctions; AI-generated data is not clinician-confirmed.
- `docs/fhir/gateway-validation-boundary.md`
  - Defines future validation boundary draft; no live HAPI `$validate`, no live FHIR write, and no runtime Gateway validation in Phase 9A.
- `docs/fhir/gateway-to-fhir-mapping-draft.md`
  - Candidate mapping draft only; no mapping runtime and no FHIR Server write.
- `docs/fhir/gateway-error-handling-draft.md`
  - Future error handling draft; reject or hold unsafe input, do not write to FHIR Server, and do not mark as signed-off without review.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-9a-gateway-ai-agent-adapter-contract-20260512/`
  - Phase 9A evidence package for working tree snapshot, contract/schema/classification/validation/mapping/error handling results, route safety, regression tests, documentation validation, deferred issues, next-phase plan, and safety boundary.

## FHIR Read-only Lesion Viewer Phase 9B

- `docs/fhir/fhir-validation-workflow-draft.md`
  - Phase 9B FHIR Validation Workflow Draft; defines the future pre-write validation sequence for Gateway / AI Agent payloads after FHIR candidate mapping. It is not runtime, not live `$validate` integration, not production validation service, and not FHIR write pipeline.
- `docs/fhir/fhir-validation-error-taxonomy.md`
  - Validation error taxonomy draft for envelope, source, trust, payload, mapping, FHIR reference, terminology, profile, privacy, binary, review, and runtime-boundary errors.
- `docs/fhir/fhir-profile-ig-draft-boundary.md`
  - Profile / IG draft boundary; no Profile publication, no IG publication, no live profile validation, and no live HAPI `$validate`.
- `docs/fhir/fhir-validation-result-contract.md`
  - Validation result contract draft; not a runtime response, not an OperationOutcome replacement, not a write confirmation, and not signed-off confirmation.
- `docs/fhir/contracts/fhir-validation-result.schema.json`
  - Documentation-only validation result schema draft; no endpoint consumes it in Phase 9B.
- `docs/fhir/manual-review-gate-policy.md`
  - Manual review gate policy; validation-passed-candidate is not signed-off, AI-generated is not clinician-reviewed, and signed-off requires explicit evidence.
- `docs/fhir/future-hapi-validate-boundary.md`
  - Future HAPI `$validate` boundary; Phase 9B does not call live HAPI `$validate` and does not write to FHIR Server.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-9b-fhir-validation-workflow-draft-20260512/`
  - Phase 9B evidence package for working tree snapshot, workflow draft, taxonomy, profile / IG boundary, validation result contract, manual review gate, future `$validate` boundary, route safety, runtime safety, regression tests, documentation validation, deferred issues, next-phase plan, and safety boundary.

## FHIR Read-only Lesion Viewer Phase 9C

- `docs/fhir/governance-review-draft.md`
  - Phase 9C Governance Review Draft; defines activation criteria, prohibition criteria, risk boundary, governance domains, and non-activation outcomes.
- `docs/fhir/smart-production-activation-policy-draft.md`
  - SMART production activation policy draft only; Phase 9C does not enable SMART production, SMART launch runtime, production OAuth flow, or production scopes.
- `docs/fhir/cds-runtime-governance-policy-draft.md`
  - CDS runtime governance policy draft only; Phase 9C does not enable CDS runtime, CDS Hooks endpoint, clinical advice, automatic diagnosis, or treatment recommendation.
- `docs/fhir/document-reference-protected-download-policy-draft.md`
  - Protected download policy draft; DocumentReference metadata / reference display does not directly expose binary content.
- `docs/fhir/consent-ecsu-signoff-governance-draft.md`
  - Consent / eCSU / sign-off governance draft; Consent.active is not a medical result and validation-passed-candidate is not signed-off.
- `docs/fhir/patient-display-policy-draft.md`
  - Patient display policy draft; Patient remains subject reference / safe summary and does not display full sensitive data by default.
- `docs/fhir/ai-generated-review-policy-draft.md`
  - AI-generated review policy draft; AI-generated is not clinician-reviewed and AI Agent output cannot directly write to the production FHIR Server.
- `docs/fhir/fhir-validation-governance-policy-draft.md`
  - FHIR validation governance policy draft; OperationOutcome is not a clinical conclusion and validation passed is not write approval.
- `docs/fhir/gateway-ingestion-governance-policy-draft.md`
  - Gateway ingestion governance policy draft; Phase 9C does not add Gateway ingestion runtime, POST /gateway, webhook receiver, or queue worker.
- `docs/fhir/audit-provenance-logging-policy-draft.md`
  - Audit / Provenance / logging policy draft; Phase 9C does not add logging runtime, audit writer, Provenance writer, or AuditEvent writer.
- `docs/fhir/controlled-ingestion-readiness-checklist.md`
  - Controlled ingestion readiness checklist; checklist completed is not production approval, write path enabled, or clinical sign-off.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-9c-governance-review-draft-20260512/`
  - Phase 9C evidence package for working tree snapshot, governance policy results, route safety, runtime safety, regression tests, documentation validation, deferred issues, next-phase plan, and safety boundary confirmation.

## FHIR Phase 4 Evidence

- `docs/fhir/evidence/fhir-lesion-viewer-phase-7-condition-document-consent-linking-20260512/`
  - FHIR Read-only Lesion Viewer Phase 7 Condition / DocumentReference / Consent read-only linking; linking is optional and failure-isolated, Condition linking does not equal frontend automatic diagnosis, DocumentReference metadata / reference display does not expose sensitive file content directly, Consent linking does not represent a medical result, AI-generated content is not clinician-confirmed content, and there is no FHIR write/update/delete, no FHIR create/update/delete/patch/upload, no lesion write route, no SMART production activation, no CDS runtime activation, no clinical advice, no automatic diagnosis, and no treatment recommendation.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-1-to-7-handoff-20260512/`
  - FHIR Read-only Lesion Viewer Phase 1-7 handoff summary; records product positioning, architecture, supported resources, safety boundary, and deferred issues for handoff readiness.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-8-stabilization-handoff-20260512/`
  - FHIR Read-only Lesion Viewer Phase 8 stabilization / checkpoint / handoff evidence package; records working tree snapshot, file inventory, route safety, source switch, API contract, UI smoke, regression tests, documentation validation, demo flow, next-phase planning, and safety boundary confirmation without adding medical functionality or FHIR write flows.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/`
  - FHIR Read-only Lesion Viewer Phase 6 Patient / Observation / Encounter enrichment; enrichment is optional and failure-isolated, Patient does not expose full sensitive data, Observation values are displayed without interpretation, Encounter is shown only as an interaction / observation event, Condition / DocumentReference / Consent full aggregation remains deferred, and there is no FHIR write/update/delete, no FHIR create/update/delete/patch/upload, no lesion write route, no SMART production activation, no CDS runtime activation, no clinical advice, no automatic diagnosis, and no treatment recommendation.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-5-fhir-backed-aggregation-20260512/`
  - FHIR Read-only Lesion Viewer Phase 5 FHIR-backed DiagnosticReport-centered aggregation; mock source remains default, fhir source supports read-only DiagnosticReport search/read aggregation, no-data and read/search failures safely return empty/null without frontend 500, no live FHIR write/update/delete, no FHIR create/update/delete/patch/upload, no lesion write route, no SMART production activation, no CDS runtime activation, no clinical advice, no automatic diagnosis, and no treatment recommendation.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-4-repository-aggregation-skeleton-20260512/`
  - FHIR Read-only Lesion Viewer Phase 4 repository interface and FHIR-backed aggregation skeleton; mock source remains default, fhir source is skeleton-only, source switch config is fail-safe, no live FHIR write/update/delete, no lesion write route, no SMART production activation, no CDS runtime activation, no clinical advice, no automatic diagnosis, and no treatment recommendation.
- `docs/fhir/evidence/phase-4-runtime-activation-charter-20260510/`
  - Phase 4-0 runtime activation charter and safety boundary review; documentation-only, no runtime activation, no execution gate enablement, and no clinical use approval.
- `docs/fhir/evidence/phase-4-runtime-activation-scope-definition-20260510/`
  - Phase 4-1 runtime activation scope definition; documentation-only, no runtime action implementation, no execution gate enablement, no allowlist mutation, and no clinical use approval.
- `docs/fhir/evidence/phase-4-owner-reviewer-operator-assignment-plan-20260510/`
  - Phase 4-2 owner / reviewer / operator assignment plan; documentation-only, no runtime user creation, no RBAC permission, no role seed, no execution gate enablement, and no allowlist mutation.
- `docs/fhir/evidence/phase-4-rollback-planning-recovery-boundary-20260510/`
  - Phase 4-3 rollback planning and recovery boundary; documentation-only, no rollback command, no purge job, no destructive operation, and no rollback execution.
- `docs/fhir/evidence/phase-4-monitoring-planning-alert-boundary-20260510/`
  - Phase 4-4 monitoring planning and alert boundary; documentation-only, no monitoring job, no scheduler, no alert service, no notification integration, and no alert delivery.
- `docs/fhir/evidence/phase-4-incident-response-escalation-boundary-20260510/`
  - Phase 4-5 incident response planning and escalation boundary; documentation-only, no incident command, no escalation job, no scheduler, no alert service, no notification integration, and no escalation delivery.
- `docs/fhir/evidence/phase-4-production-like-rehearsal-planning-20260510/`
  - Phase 4-6 production-like rehearsal planning package; documentation-only, no rehearsal execution, no runtime action implementation, no execution gate enablement, no allowlist mutation, and no runtime activation approval.
- `docs/fhir/evidence/phase-4-pre-implementation-technical-design-review-20260510/`
  - Phase 4-7 pre-implementation technical design review; documentation-only, no skeleton implementation, no interface, no service, no handler, no route, no controller, no config mutation, no allowlist mutation, and no execution gate enablement.
- `docs/fhir/evidence/phase-4-implementation-safety-contract-20260510/`
  - Phase 4-8 implementation safety contract and disabled-by-default design; documentation-only, no skeleton implementation, no interface, no service, no handler, no route, no controller, no config mutation, no allowlist mutation, no execution gate enablement, and no runtime activation approval.
- `docs/fhir/evidence/phase-4-non-callable-skeleton-implementation-20260510/`
  - Phase 4-9 non-callable skeleton implementation for `record_governance_rehearsal_marker`; disabled by default, not routable, not allowlisted, not wired into runtime, fail-closed, no FHIR mutation, no clinical advice, and runtime activation remains prohibited.
- `docs/fhir/evidence/phase-4-skeleton-review-hardening-20260510/`
  - Phase 4-10 skeleton review and hardening evidence for `record_governance_rehearsal_marker`; skeleton review / hardening only, disabled, non-callable, not allowlisted, no runtime wiring, no FHIR mutation, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-design-review-20260510/`
  - Phase 4-11 dry-run design review evidence for `record_governance_rehearsal_marker`; dry-run design review only, no dry-run execution, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no FHIR mutation, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-safety-contract-synthetic-fixture-20260510/`
  - Phase 4-12 dry-run safety contract and synthetic fixture planning evidence for `record_governance_rehearsal_marker`; dry-run safety contract / synthetic fixture planning only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-output-schema-evidence-correlation-20260510/`
  - Phase 4-13 dry-run output schema review and evidence correlation planning evidence for `record_governance_rehearsal_marker`; output schema review / evidence correlation planning only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-execution-readiness-review-20260510/`
  - Phase 4-14 dry-run execution readiness review evidence for `record_governance_rehearsal_marker`; dry-run execution readiness review only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-execution-plan-preparation-20260510/`
  - Phase 4-15 dry-run execution plan preparation evidence for `record_governance_rehearsal_marker`; dry-run execution plan preparation only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-execution-approval-package-20260510/`
  - Phase 4-16 dry-run execution approval package evidence for `record_governance_rehearsal_marker`; dry-run execution approval package only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-final-preflight-review-20260510/`
  - Phase 4-17 dry-run final preflight review evidence for `record_governance_rehearsal_marker`; dry-run final preflight review only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-limited-dry-run-go-no-go-package-20260510/`
  - Phase 4-18 extremely limited dry-run go/no-go package evidence for `record_governance_rehearsal_marker`; extremely limited dry-run go/no-go package only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-explicit-limited-dry-run-authorization-review-20260510/`
  - Phase 4-19 explicit limited dry-run authorization review evidence for `record_governance_rehearsal_marker`; explicit limited dry-run authorization review only, no dry-run execution, no execution gate enablement, no allowlist mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-extremely-limited-direct-class-dry-run-20260510/`
  - Phase 4-20 extremely limited direct class-level dry-run evidence for `record_governance_rehearsal_marker`; direct class-level synthetic governance metadata dry-run only, controlled fail-closed RuntimeException observed, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dry-run-evidence-review-safety-assessment-20260510/`
  - Phase 4-21 dry-run evidence review and post-execution safety assessment for `record_governance_rehearsal_marker`; review-only, no new dry-run execution, no broader dry-run scope, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-runtime-dry-run-governance-checkpoint-20260510/`
  - Phase 4-22 runtime dry-run governance checkpoint for `record_governance_rehearsal_marker`; checkpoint-only, no new dry-run execution, no dry-run scope expansion, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-design-boundary-review-20260510/`
  - Phase 4-23 broader dry-run design boundary review for `record_governance_rehearsal_marker`; boundary review only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-scope-proposal-20260510/`
  - Phase 4-24 broader dry-run scope proposal for `record_governance_rehearsal_marker`; scope proposal only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-execution-plan-preparation-20260510/`
  - Phase 4-25 broader dry-run execution plan preparation for `record_governance_rehearsal_marker`; execution plan preparation only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-approval-package-20260510/`
  - Phase 4-26 broader dry-run approval package for `record_governance_rehearsal_marker`; approval package only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-final-preflight-review-20260510/`
  - Phase 4-27 broader dry-run final preflight review for `record_governance_rehearsal_marker`; final preflight review only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-go-no-go-package-20260510/`
  - Phase 4-28 broader dry-run go/no-go package for `record_governance_rehearsal_marker`; go/no-go package only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-execution-package-preparation-20260510/`
  - Phase 4-29 broader dry-run execution package preparation for `record_governance_rehearsal_marker`; execution package preparation only, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-broader-dry-run-execution-readiness-review-20260511/`
  - Phase 4-30 broader dry-run execution readiness review for `record_governance_rehearsal_marker`; documentation-only readiness review, no broader dry-run execution, no new direct class-level dry-run, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-dual-approval-governance-event-readiness-20260511/`
  - Phase 4-31 dual approval and governance event append readiness for `record_governance_rehearsal_marker`; documentation-only readiness package, no broader dry-run execution, no new direct class-level dry-run, no actual execution approval, no governance event append, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, broader dry-run execution remains prohibited, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-final-no-go-broader-dry-run-decision-20260511/`
  - Phase 4-32 final No-Go decision package for broader dry-run execution for `record_governance_rehearsal_marker`; documentation-only final no-go decision package, broader dry-run execution is not approved, no broader dry-run execution occurred, no new direct class-level dry-run, no actual dual approval, no actual governance event append, readiness documentation is not authorization, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-no-go-remediation-plan-20260511/`
  - Phase 4-33 No-Go remediation plan for `record_governance_rehearsal_marker`; documentation-only remediation planning, Phase 4-32 No-Go remains in effect, broader dry-run execution is not approved, no broader dry-run execution occurred, no new direct class-level dry-run, no actual dual approval, no actual governance event append, readiness remediation planning is not authorization, no blocker is resolved, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-remediation-evidence-template-package-20260511/`
  - Phase 4-34 remediation evidence template package for `record_governance_rehearsal_marker`; documentation-only template definition, Phase 4-32 No-Go remains in effect, Phase 4-33 remediation plan remains planning-only, no blocker is resolved, no re-review entry criteria are satisfied, template availability is not evidence approval, evidence template definition is not execution authorization, broader dry-run execution is not approved, no broader dry-run execution occurred, no new direct class-level dry-run, no actual dual approval, no actual governance event append, no runtime state changes, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-re-review-entry-criteria-package-20260511/`
  - Phase 4-35 re-review entry criteria package for `record_governance_rehearsal_marker`; documentation-only entry criteria definition, Phase 4-32 No-Go remains in effect, Phase 4-33 remediation plan remains planning-only, Phase 4-34 templates are not approved evidence, re-review has not started, no re-review entry criteria are satisfied, no blocker is resolved, entry criteria definition is not authorization, broader dry-run execution is not approved, no broader dry-run execution occurred, no new direct class-level dry-run, no actual dual approval, no actual governance event append, no runtime state changes, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, runtime activation remains prohibited, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-re-review-deferred-decision-package-20260511/`
  - Phase 4-36 re-review deferred decision package for `record_governance_rehearsal_marker`; documentation-only deferral record, Phase 4-32 No-Go remains in effect, re-review is deferred, re-review has not started, Phase 4-35 entry criteria are defined but not satisfied, no blocker is resolved, broader dry-run execution is not approved, no execution authorization is granted, no actual dual approval is granted, no actual governance event is appended, no runtime state changes are made, deferral is a governance decision and not a failure, future re-review may only be considered after all entry criteria are satisfied and separately approved, runtime activation remains prohibited, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-deferred-re-review-monitoring-criteria-package-20260511/`
  - Phase 4-37 deferred re-review monitoring criteria package for `record_governance_rehearsal_marker`; documentation-only monitoring criteria definition, monitoring criteria are not active monitoring, no runtime monitoring is created, no scheduled job or watcher is created, Phase 4-32 No-Go remains in effect, re-review remains deferred, re-review has not started, no blocker is resolved, no re-review entry criteria are satisfied, broader dry-run execution is not approved, future re-review request readiness signals may be defined but not triggered, no execution approval is granted, no actual dual approval is granted, no actual governance event is appended, no runtime state changes are made, runtime activation remains prohibited, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-deferred-re-review-governance-snapshot-20260511/`
  - Phase 4-38 deferred re-review governance snapshot for `record_governance_rehearsal_marker`; documentation-only governance snapshot, Phase 4-32 No-Go remains in effect, re-review remains deferred, re-review has not started, Phase 4-35 entry criteria remain unsatisfied, Phase 4-37 monitoring criteria remain criteria only, monitoring criteria are not active monitoring, no runtime monitoring is created, no scheduled job or watcher is created, no blocker is resolved, no re-review entry criteria are satisfied, broader dry-run execution is not approved, no execution approval is granted, no actual dual approval is granted, no actual governance event is appended, no runtime state changes are made, runtime activation remains prohibited, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, and clinical use remains prohibited.
- `docs/fhir/evidence/phase-4-deferred-re-review-communication-handoff-package-20260511/`
  - Phase 4-39 deferred re-review communication and handoff package for `record_governance_rehearsal_marker`; documentation-only communication and handoff guidance, Phase 4-32 No-Go remains in effect, re-review remains deferred, re-review has not started, Phase 4-35 entry criteria remain unsatisfied, Phase 4-37 monitoring criteria remain criteria only, monitoring criteria are not active monitoring, no runtime monitoring is created, no scheduled job or watcher is created, no blocker is resolved, no re-review entry criteria are satisfied, broader dry-run execution is not approved, communication/handoff guidance is not authorization, handoff documentation does not imply execution readiness, no execution approval is granted, no actual dual approval is granted, no actual governance event is appended, no runtime state changes are made, runtime activation remains prohibited, no execution gate enablement, no allowlist mutation, no route / controller / handler wiring, no runtime config mutation, no clinical payload use, no real patient data use, no FHIR mutation, no AuditEvent or Provenance write, no clinical advice, no advisory mode, no blocking workflow, no clinical inference, and clinical use remains prohibited.
# FHIR Read-only Lesion Viewer 文件索引

本專案目前定位為 FHIR Read-only Lesion Viewer：只讀病灶 / 臨床資料展示系統。前端只顯示病灶資料、臨床摘要、FHIR Metadata、文件 / 報告參照、資料來源、醫護簽核狀態與更新時間；不提供新增、編輯、刪除、上傳、診斷、推論或醫療建議。

## Read-only Lesion Viewer

- `docs/fhir/lesion-viewer-data-contract.md`
  - Lesion Viewer 前端展示 view model 草案；Lesion 不是新的 FHIR Resource，後端仍以 FHIR Resource 作為標準資料層。
- `docs/fhir/lesion-fhir-resource-mapping.md`
  - Lesion 欄位與 Patient、Observation、Condition、DiagnosticReport、DocumentReference、Consent、Encounter 的 mapping 草案。
- `docs/fhir/lesion-fhir-aggregation-strategy.md`
  - Phase 4 FHIR-backed aggregation strategy：Lesion is a read-only view model, mock source remains default, FHIR-backed source is skeleton-only, and future aggregation must stay read-only through approved Gateway / Adapter / validation workflow.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-6-patient-observation-encounter-enrichment-20260512/`
  - Phase 6 補強 Patient / Observation / Encounter optional enrichment；失敗時保留 FHIR reference，不拖垮 Lesion，不顯示完整敏感 Patient 資料，Observation value 只顯示不解讀，Encounter 只表示互動 / 觀察事件。
## FHIR Read-only Lesion Viewer Phase 10A

- `docs/fhir/controlled-ingestion-prototype-planning.md`
  - Phase 10A Controlled Ingestion Prototype Planning; planning only, not runtime, not Gateway ingestion, not FHIR write pipeline, not production ingestion, and not production approval.
- `docs/fhir/dev-only-mock-ingestion-scope.md`
  - Defines future Phase 10B dev-only and mock-only scope; no real PHI, no production FHIR Server, no AI Agent runtime, no CDS runtime, and no SMART production activation.
- `docs/fhir/mock-ingestion-payload-policy.md`
  - Defines synthetic mock payload policy; mock payload is not formal FHIR data, not signed-off, and not written to production FHIR Server.
- `docs/fhir/no-write-fhir-boundary.md`
  - Defines no FHIR create/update/delete/patch/upload boundary and read-only candidate preview limits.
- `docs/fhir/manual-review-queue-mock-plan.md`
  - Defines mock manual review queue planning; mock queue is not clinician sign-off and does not write to FHIR Server.
- `docs/fhir/validation-result-mock-plan.md`
  - Defines mock validation result planning; not live HAPI `$validate`, not OperationOutcome, not clinical correctness, and not FHIR write approval.
- `docs/fhir/candidate-resource-staging-plan.md`
  - Defines candidate resource staging; candidate preview is not persisted FHIR Resource and has no formal FHIR Server reference.
- `docs/fhir/controlled-ingestion-rollback-disablement-plan.md`
  - Defines future rollback and disablement controls; Phase 10A adds no feature flag runtime.
- `docs/fhir/controlled-ingestion-test-data-policy.md`
  - Defines synthetic test data only; no real PHI, no production output, and no production FHIR Server write.
- `docs/fhir/controlled-ingestion-prototype-exit-criteria.md`
  - Defines future prototype completion and termination criteria; completion is not production approval, write enablement, or clinical sign-off.
- `docs/fhir/phase-10b-readiness-checklist.md`
  - Defines pre-Phase 10B readiness checklist; checklist completed is not runtime implemented or FHIR write enabled.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10a-controlled-ingestion-prototype-planning-20260512/`
  - Phase 10A evidence package for working tree snapshot, planning results, route safety, runtime safety, regression tests, documentation validation, deferred issues, next phase plan, and safety boundary.

## Phase 10A.5 Baseline Checkpoint / Pre-Phase 10B Readiness Review

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10a5-baseline-checkpoint-20260512/`
  - Phase 10A.5 baseline checkpoint and pre-Phase 10B readiness review package. It records working tree snapshot, file inventory, change classification, route safety, runtime safety, source switch, API/UI read-only baseline, regression tests, documentation validation, Phase 10B readiness review, go/no-go checklist, commit / branch recommendation, rollback / recovery notes, deferred issues, next phase plan, and safety boundary. It is not Phase 10B, not runtime, not Gateway ingestion, not FHIR write pipeline, not production ingestion, and not production approval.

## Phase 10A.6 Git Checkpoint Preparation / Selective Commit Plan

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10a6-git-checkpoint-preparation-20260512/`
  - Phase 10A.6 Git checkpoint preparation and selective commit plan package. It records working tree snapshot, FHIR mainline file inventory, unrelated dirty file list, selective staging plan, commit split recommendation, branch recommendation, patch / backup recommendation, pre-commit checklist, post-commit checklist, route safety, runtime safety, regression tests, documentation validation, deferred issues, next phase plan, and safety boundary. It is not Phase 10B, not dev-only mock ingestion prototype, not runtime implementation, not Git cleanup, not automatic commit, and not automatic branch checkout.

## FHIR Read-only Lesion Viewer Phase 10B

- `docs/fhir/dev-only-mock-ingestion-prototype.md`
  - Phase 10B 是 dev-only mock prototype; feature flag default disabled, no real PHI, no production FHIR Server, no direct FHIR write, manual review queue mock only, validation result mock only, candidate preview only, not production ingestion, not AI Agent runtime, not CDS runtime, and not SMART production.
- `docs/fhir/no-write-fhir-boundary.md`
  - Confirms no FHIR create/update/delete/patch/upload, no live HAPI `$validate`, and candidate preview 不等於 persisted FHIR Resource.
- `docs/fhir/manual-review-queue-mock-plan.md`
  - Confirms manual review queue mock 不等於 signed-off.
- `docs/fhir/validation-result-mock-plan.md`
  - Confirms validation result mock 不等於 live HAPI `$validate`.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10b-dev-only-mock-ingestion-prototype-20260512/`
  - Phase 10B evidence package with working tree snapshot, feature flag results, parser results, validation mock results, manual review queue mock results, candidate preview results, UI/API/route/runtime/no-write/regression/documentation results, deferred issues, safety boundary, and next phase plan.

## FHIR Read-only Lesion Viewer Phase 10B.1

- `docs/fhir/dev-only-mock-ingestion-prototype.md`
  - Phase 10B.1 stabilization notes for the dev-only mock ingestion prototype: feature flag hardening, environment guard hardening, payload safety hardening, UI wording review, sample payload audit, no-write verification, and no Phase 10C.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10b1-prototype-stabilization-hardening-20260512/`
  - Phase 10B.1 evidence package for prototype stabilization / hardening / checkpoint preparation. It records working tree snapshot, feature flag and environment guard results, payload safety, validation result hardening, manual review queue hardening, candidate preview hardening, UI wording, sample payload audit, route safety, runtime safety, no-write verification, regression tests, documentation results, Phase 10B.2 checkpoint preparation, deferred issues, safety boundary, and next phase plan.

## FHIR Read-only Lesion Viewer Phase 10C

- `docs/fhir/lesion-viewer-data-contract.md`
  - Phase 10C aligned read-only lesion viewer contract; uses `read-only-aggregation-v3`, confirms lesion API remains read-only, and does not add lesion CRUD, FHIR write, formal ingestion, clinical advice, approval persistence, or signoff persistence.
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10c-readonly-contract-and-preview-qa-20260513/`
  - Phase 10C evidence package for API contract QA, dev-only mock ingestion preview QA, encoding / mojibake review, route safety, no-write boundary, targeted test results, remaining risks, and Phase 10C.6 selective commit planning. It records no HAPI/docker/env/SMART/CDS/Gateway runtime changes.

## FHIR Read-only Lesion Viewer Phase 10E

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10e-dirty-tree-cleanup-plan-20260513/`
  - Phase 10E documentation-only dirty tree cleanup plan for the read-only lesion viewer track; no runtime change, no route/controller/service change, no env/docker/HAPI/SMART/CDS/Gateway change, no lesion CRUD, no formal ingestion, no FHIR persistence, and unrelated dirty/untracked files are preserved.

## FHIR Read-only Lesion Viewer Phase 10I-J1

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10i-j1-mock-ingestion-middleware-diagnosis-20260513/`
  - Phase 10I-J1 mock ingestion middleware / feature-flag diagnosis evidence confirming that Docker 404 for `/dev/fhir/mock-ingestion` is expected fail-closed behavior when the dev-only controlled ingestion prototype flag is disabled; documentation-only evidence, no runtime change, no route/controller/service/view/config/env/docker change, no cache clear, no Docker restart/rebuild, feature flag remains disabled, no lesion CRUD, no formal ingestion, no FHIR persistence, no approval/signoff persistence, and unrelated dirty/untracked files are preserved.

## FHIR Read-only Lesion Viewer Phase 10K-B

- `docs/fhir/evidence/fhir-lesion-viewer-phase-10k-b-demo-readiness-review-20260513/`
  - Phase 10K-B read-only viewer demo readiness evidence package recording the Conditional Go for internal demo decision, demo script, demo-able scope, explain-only scope, and remaining risks; documentation-only evidence, no runtime change, no route/controller/service/view/config/env/docker change, no cache clear, no Docker restart/rebuild, feature flag remains disabled, no lesion CRUD, no formal ingestion, no FHIR persistence, no approval/signoff persistence, unrelated dirty/untracked files are preserved, and internal demo is conditional on using Docker URL and an existing authenticated browser session.
