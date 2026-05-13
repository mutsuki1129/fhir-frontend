# Validation Result Hardening Results

Phase 10B.1 確認 validation result mock：

- `validationResult.readOnly = true`
- `validationResult.runtime = dev-mock-only`
- `review.required = true`
- `review.signedOff = false`
- `candidateResources[].persisted = false`
- `candidateResources[].fhirReference = null`

Validation result mock 不輸出：

- signed-off
- clinician-confirmed
- written-to-fhir
- production-approved
- clinical advice
- automatic diagnosis
- treatment recommendation

允許 outcome vocabulary 僅限設計文件中的 mock/review 狀態；本 prototype 目前輸出 `manual-review-required`。
