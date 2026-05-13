# Candidate Resource Staging Plan

Candidate resources 只能是暫存 / preview 概念。

## Candidate Resource Draft

```json
{
  "candidateId": "candidate-diagnosticreport-001",
  "resourceType": "DiagnosticReport",
  "mappingStatus": "candidate",
  "validationStatus": "profile-validation-required",
  "reviewStatus": "pending-review",
  "persisted": false,
  "fhirReference": null
}
```

`persisted=false` 表示尚未寫入 FHIR Server。`fhirReference=null` 表示沒有正式 FHIR Server reference。`candidateId` 不等於 FHIR Resource id。candidate preview 不等於 clinical conclusion。

## Forbidden Automation

- 自動產生正式 FHIR id
- 自動寫入 FHIR Server
- 自動建立 DiagnosticReport final
- 自動建立 Condition confirmed
- 自動建立 signed-off 狀態

Candidate preview 不等於 persisted FHIR Resource。

