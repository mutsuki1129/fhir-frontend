# FHIR Validation Result Contract

Phase 9B 定義 validation result contract draft。Validation result 是 draft，不是 runtime response，不是 FHIR OperationOutcome replacement，不是 write confirmation，不是 signed-off confirmation，也不是 production validation service 的 API contract。

## Draft Result Envelope

```json
{
  "schemaVersion": "0.1-draft",
  "validationId": "validation-001",
  "messageId": "msg-001",
  "correlationId": "case-001",
  "status": "hold-for-review",
  "outcome": "manual-review-required",
  "readOnly": true,
  "runtime": "not-enabled-in-phase-9b",
  "candidateResources": [
    {
      "candidateId": "candidate-diagnosticreport-001",
      "resourceType": "DiagnosticReport",
      "mappingStatus": "candidate",
      "validationStatus": "profile-validation-required"
    }
  ],
  "errors": [
    {
      "code": "review.human_review_required",
      "severity": "warning",
      "message": "Human review is required before controlled write.",
      "safeDetails": "No sensitive payload included."
    }
  ],
  "review": {
    "required": true,
    "reviewStatus": "pending-review",
    "signedOff": false
  }
}
```

## Required Sections

- `schemaVersion`: 文件用 schema draft 版本。
- `validationId`: validation result draft 的識別值。
- `messageId`: 來源 message envelope 的識別值。
- `correlationId`: case、request 或 workflow 追蹤識別值。
- `status`: `accepted-for-review`、`hold-for-review`、`rejected`、`validation-failed` 等狀態。
- `outcome`: `manual-review-required`、`profile-validation-required`、`mapping-required`、`validation-passed-candidate` 等結果。
- `readOnly`: Phase 9B 必須為 true。
- `runtime`: Phase 9B 必須表達 `not-enabled-in-phase-9b`。
- `candidateResources`: 候選 FHIR resources 的草案摘要，不含真實病患 payload。
- `errors`: 安全摘要錯誤列表，不含完整 PHI。
- `review`: manual review gate 狀態與 signed-off flags。

## Safety Rules

- `validation-passed-candidate` 不等於 signed-off。
- `validation-passed-candidate` 不等於 clinician-reviewed。
- `validation-passed-candidate` 不代表已寫入 FHIR Server。
- `DiagnosticReport.final` 不等於 signed-off。
- `Condition.provisional` 不等於 confirmed。
- `Consent.active` 不代表醫療結果。
- AI-generated 不等於 clinician-reviewed。
- signed-off 需要明確簽核證據。

## Non-Runtime Boundary

Phase 9B 不新增 endpoint 使用本 schema，不做 runtime validation，不呼叫 live HAPI `$validate`，不寫入 FHIR Server，不建立 FHIR write pipeline，也不把 schema 當正式規格。
