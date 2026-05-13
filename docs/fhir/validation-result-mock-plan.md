# Validation Result Mock Plan

Phase 10B validation result 是 mock only。它參考 Phase 9B `fhir-validation-result-contract.md` 與 `contracts/fhir-validation-result.schema.json` 的概念，但本階段不啟用 runtime validator。

Validation result mock 不等於 live HAPI `$validate`，不等於 OperationOutcome，不等於 clinical correctness，不等於 signed-off，不等於 FHIR write approval。

## Phase 10B / 10B.1 Mock Outcome

- status: hold-for-review
- outcome: manual-review-required
- runtime: dev-mock-only
- readOnly: true
- review.required: true
- review.signedOff: false
- candidateResources[].persisted: false
- candidateResources[].fhirReference: null

## Allowed Outcome Vocabulary

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
- clinical advice
- automatic diagnosis
- treatment recommendation

Phase 10B.1 不呼叫 live HAPI `$validate`，不產生 validation pass，不允許 FHIR create/update/delete/patch/upload。
