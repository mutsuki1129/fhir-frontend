# Controlled Ingestion Prototype Planning

Phase 10A 是 planning。Phase 10B 已開始 dev-only mock ingestion prototype，但仍只允許 mock-only preview，不允許 production ingestion 或 FHIR write。

Phase 10B 是 dev-only mock prototype：feature flag default disabled、no real PHI、no production FHIR Server、no direct FHIR write、manual review queue mock only、validation result mock only、candidate preview only。

Phase 10B is not production ingestion, not AI Agent runtime, not CDS runtime, not SMART production, not Gateway runtime for production, not validation runtime, not live HAPI `$validate`, not clinical advice, not automatic diagnosis, and not treatment recommendation.

## Prototype Concept

```text
Synthetic Mock Gateway Payload
        -> Mock Payload Parser
        -> Mock Validation Result
        -> Mock Manual Review Queue Item
        -> Candidate Resource Preview
        -> No FHIR Write
```

## Runtime Limits

允許的 runtime surface 只有：

- `GET /dev/fhir/mock-ingestion`
- `POST /dev/fhir/mock-ingestion/preview`

不允許 production gateway / ingestion / agent / validate / validation route，不允許 FHIR writer，不允許 FHIR create/update/delete/patch/upload，不允許 live HAPI `$validate`。

## Phase 10B.1 Stabilization Boundary

Phase 10B.1 是 Prototype Stabilization / Hardening / Checkpoint Preparation。它只檢查並加固 Phase 10B prototype 的 feature flag、environment guard、mode=mock、no-write、payload safety、UI wording、sample payload、runtime safety 與 evidence package。

Phase 10B.1 不是 Phase 10C，不是 Controlled Write Path Review，不是 production ingestion，不是 production approval，不是 FHIR write pipeline，不是 AI Agent runtime，不是 CDS runtime，也不是 SMART production activation。

Phase 10B.1 不新增 runtime endpoint、不新增 queue worker、不新增 webhook receiver、不新增 FHIR writer、不呼叫 FHIR create/update/delete/patch/upload、不呼叫 live HAPI `$validate`，也不進入 controlled write path。

## Exit Boundary

Phase 10B / Phase 10B.1 完成後仍只代表 dev-only mock prototype 與 stabilization/hardening 完成，不代表 production approval、write enablement、clinical sign-off、SMART production activation、CDS runtime activation 或 AI Agent runtime activation，也不代表允許寫入 FHIR Server。
