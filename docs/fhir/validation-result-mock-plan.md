# Validation Result Mock Plan

本文件定義未來 prototype 可以顯示 mock validation result，並銜接 Phase 9B 的 `fhir-validation-result-contract.md` 與 `fhir-validation-result.schema.json`。

Validation result mock 不等於 live HAPI `$validate`。Validation result mock 不等於 FHIR OperationOutcome。Validation result mock 不等於 clinical correctness。Validation result mock 不等於 signed-off。Validation result mock 不等於 FHIR write approval。

## Allowed Mock Outcome

- accepted-for-review
- hold-for-review
- mapping-required
- profile-validation-required
- manual-review-required
- validation-failed

## Forbidden Automatic Outcome

- signed-off
- clinician-confirmed
- written-to-fhir
- production-approved

Phase 10A does not add validation runtime and does not call live HAPI `$validate`.

