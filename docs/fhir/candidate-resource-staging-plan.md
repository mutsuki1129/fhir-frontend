# Candidate Resource Staging Plan

Phase 10B candidate resource 是 preview only。Candidate preview 是 dev-only mock prototype 的 read-only output，不是 persisted FHIR Resource，不是 FHIR Server reference，也不是 clinical conclusion。

## Candidate Resource Draft

```json
{
  "candidateId": "candidate-diagnosticreport-msg-001",
  "resourceType": "DiagnosticReport",
  "mappingStatus": "candidate",
  "validationStatus": "profile-validation-required",
  "reviewStatus": "pending-review",
  "persisted": false,
  "fhirReference": null
}
```

`persisted=false` 表示未寫入 FHIR Server。`fhirReference=null` 表示沒有正式 FHIR Server reference。`candidateId` 不等於 FHIR Resource id。DiagnosticReport preliminary 不等於 final。Condition provisional 不等於 confirmed。

## Forbidden Automation

- 不自動產生 FHIR id
- 不自動寫入 FHIR Server
- 不自動產生 DiagnosticReport final
- 不自動產生 Condition confirmed
- 不自動產生 signed-off
- 不暴露 DocumentReference binary

## Phase 10B.1 Hardening

Phase 10B.1 確認 candidate preview 永遠保持 `persisted=false`、`fhirReference=null`，且不會被視為 persisted FHIR Resource、clinical conclusion、FHIR write approval 或 Phase 10C controlled write path。
