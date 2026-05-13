# Phase 10B Dev-only Mock Ingestion Prototype

Phase 10B 是 dev-only mock ingestion prototype。它只提供受 feature flag 與 environment guard 保護的 mock preview surface，用於 synthetic mock payload、mock validation result、mock manual review queue item 與 candidate resource preview。

Phase 10B 不是 production ingestion，不是 Gateway runtime for production，不是 AI Agent runtime，不是 FHIR write path，不是 validation runtime，不是 live HAPI `$validate`，不是 SMART production activation，不是 CDS runtime，不是 clinical advice，不是 automatic diagnosis，也不是 treatment recommendation。

## Feature Flag

Feature flag default disabled:

```text
FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED=false
FHIR_CONTROLLED_INGESTION_PROTOTYPE_MODE=mock
```

`config/fhir.php` 固定 `controlled_ingestion_prototype.allow_fhir_write=false`。此值不得由 env 開啟，也不得成為 FHIR create / update / delete / patch / upload 的開關。

## Dev-only Guard

`EnsureControlledIngestionPrototypeEnabled` 必須同時符合：

- app environment 是 `local` 或 `testing`
- `FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED=true`
- mode 是 `mock`
- `allow_fhir_write=false`

任何不符合條件時回 404，避免 production-like environment 暴露 prototype route。

## Runtime Surface

Phase 10B 只允許：

- `GET /dev/fhir/mock-ingestion`
- `POST /dev/fhir/mock-ingestion/preview`

這些 route 都是 dev-only、mock-only、feature-flag guarded、no real PHI、no production FHIR Server、no direct FHIR write。

## Mock Processing

Mock payload parser 做 envelope-level safe parsing，檢查 `schemaVersion`、`messageId`、`correlationId`、`sourceSystem`、`subject`、`event`、`trust`、`payload`，並拒收 real-PHI-like subject fields 與 DocumentReference binary-like content。

Validation result mock 不等於 live HAPI `$validate`，不等於 OperationOutcome，不等於 clinical correctness，不等於 signed-off，不等於 FHIR write approval。

Manual review queue mock 不等於 signed-off，不等於 clinician-confirmed，不等於 production-approved，不等於 written-to-fhir。

Candidate preview 不等於 persisted FHIR Resource。`candidateId` 不等於 FHIR Resource id，`persisted=false` 表示未寫入 FHIR Server，`fhirReference=null` 表示沒有正式 FHIR Server reference。

## Phase 10B.1 Stabilization / Hardening

Phase 10B.1 是 Prototype Stabilization / Hardening / Checkpoint Preparation。這不是 Phase 10C，不是 Controlled Write Path Review，不是 production ingestion，不是 production approval，不是 FHIR write pipeline，不是 AI Agent runtime，不是 CDS runtime，也不是 SMART production activation。

Phase 10B.1 只加固既有 Phase 10B dev-only mock prototype：

- feature flag hardening
- environment guard hardening
- mode=mock guard verification
- `allow_fhir_write=false` verification
- payload safety hardening
- UI wording review
- sample payload audit
- no-write verification
- Phase 10B.2 checkpoint preparation

Phase 10B.1 沒有新增 Gateway runtime endpoint、ingestion runtime endpoint、AI Agent runtime endpoint、validation runtime endpoint、queue worker、webhook receiver、background job、FHIR writer service、live HAPI `$validate`、SMART production mode、CDS runtime、clinical advice、automatic diagnosis、treatment recommendation，且沒有進入 Phase 10C。

## Safety Boundary

Phase 10B / Phase 10B.1 必須維持：

- no real PHI
- no production FHIR Server
- no direct FHIR write
- no FHIR create/update/delete/patch/upload
- no live HAPI `$validate`
- no AI Agent runtime
- no CDS runtime
- no SMART production
- no clinical advice
- no automatic diagnosis
- no treatment recommendation
- no DocumentReference binary exposure
- Lesion Viewer 仍維持 read-only
