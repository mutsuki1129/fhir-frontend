# Manual Review Queue Mock Plan

Phase 10B manual review queue 是 mock only。它只是 dev-only mock prototype 的 preview object，不是 queue worker，不是 reviewer assignment runtime，不是 signed-off，不是 clinician-confirmed，不是 production-approved，也不是 written-to-fhir。

## Queue Item Draft

```json
{
  "queueItemId": "review-msg-001",
  "messageId": "msg-001",
  "correlationId": "case-001",
  "status": "pending-review",
  "dataOrigin": "ai-generated",
  "candidateTypes": ["Observation", "DiagnosticReport"],
  "validationStatus": "hold-for-review",
  "reviewRequired": true,
  "signedOff": false
}
```

## Allowed Mock Status For Phase 10B / 10B.1

- pending-review
- requires-correction
- reviewed-in-mock
- rejected-in-mock
- superseded-in-mock

## Forbidden Status

- signed-off
- clinician-confirmed
- production-approved
- written-to-fhir

Phase 10B.1 只加固 mock queue item 的安全語意。它不新增 automatic sign-off、不新增 reviewer workflow runtime、不新增 controlled write path、不寫入 FHIR Server，也不產生 clinical advice。
