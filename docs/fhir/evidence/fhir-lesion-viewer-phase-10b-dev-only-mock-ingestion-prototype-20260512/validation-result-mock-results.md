# Validation Result Mock Results

Phase 10B 產生 mock validation result：

- `status=hold-for-review`
- `outcome=manual-review-required`
- `readOnly=true`
- `runtime=dev-mock-only`
- `review.required=true`
- `review.signedOff=false`
- candidate resources `persisted=false`
- candidate resources `fhirReference=null`

validation result mock only。validation result mock 不等於 live HAPI `$validate`，不等於 OperationOutcome，不等於 clinical correctness，不等於 signed-off，不等於 FHIR write approval。
