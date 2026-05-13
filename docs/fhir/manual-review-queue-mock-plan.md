# Manual Review Queue Mock Plan

本文件定義未來 mock manual review queue 的 planning。Manual review queue mock 不是真實審核系統。Manual review queue mock 不代表醫師簽核。Manual review queue mock 不會寫入 FHIR Server。Manual review queue mock 不會產生 clinical advice。

## Queue Item Draft

```json
{
  "queueItemId": "review-item-001",
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

## Allowed Mock Status

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

上述禁止狀態除非未來 governance 另案明確授權，否則不得在 prototype 中使用。

