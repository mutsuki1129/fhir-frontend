# Controlled Ingestion Prototype Planning

## Purpose

本文件定義未來 Phase 10B dev-only mock ingestion prototype 的規劃草案。

Phase 10A is planning. Phase 10A is not runtime. Phase 10A is not an endpoint. Phase 10A is not a queue worker. Phase 10A is not a FHIR write path. Phase 10A is not production ingestion.

Phase 10A 是 Controlled Ingestion Prototype Planning。這不是 ingestion runtime。這不是 Gateway runtime。這不是 AI Agent runtime。這不是 validation runtime。這不是 FHIR write pipeline。這不是 production ingestion。這不是 production approval。這不是 CDS runtime。這不是 SMART production activation。

本階段產出 prototype planning、dev-only scope、mock-only boundary、no-write boundary、manual review queue mock plan、validation result mock plan、candidate staging plan、rollback plan、test data policy、prototype exit criteria、readiness checklist、runtime safety tests、evidence package。

本階段不產出 runtime endpoint、POST /gateway、POST /ingestion、webhook receiver、queue worker、FHIR writer、AI Agent connector、live validation service、production gateway、production ingestion、clinical decision engine、automatic diagnosis engine、treatment recommendation system。

## Prototype Concept

未來 Phase 10B 如果另案明確授權，概念只能是：

```text
Mock Gateway Payload
        ↓
Mock Ingestion Parser
        ↓
Mock Validation Result
        ↓
Mock Manual Review Queue
        ↓
Mock Candidate Resource Preview
        ↓
No FHIR Write
```

Phase 10A 不實作上面任何 runtime。Phase 10A 只定義規劃與邊界。

## Planning Goals

1. Dev-only
2. Mock-only
3. No real PHI
4. No production FHIR Server
5. No direct FHIR write
6. No AI Agent runtime
7. Manual review required
8. Validation result mock required
9. Candidate data staged only
10. Explicit disablement / rollback

## Boundary

Phase 10A 不新增 Gateway runtime endpoint、不新增 ingestion runtime endpoint、不新增 AI Agent runtime endpoint、不新增 validation runtime endpoint、不新增 POST endpoint、不新增 queue worker、不新增 webhook receiver、不新增 background job、不新增 ingestion controller、不新增 FHIR writer service、不修改 FHIR server runtime、不呼叫 live HAPI `$validate`、不寫入 FHIR Server。

