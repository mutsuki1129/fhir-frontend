# Candidate Preview Hardening Results

Phase 10B.1 確認 candidate preview 永遠保持：

- `persisted=false`
- `fhirReference=null`
- `candidateId` 不等於 FHIR Resource id
- candidate preview 不等於 persisted FHIR Resource
- DiagnosticReport preliminary 不等於 final
- Condition provisional 不等於 confirmed

Phase 10B.1 禁止自動產生：

- DiagnosticReport final
- Condition confirmed
- signed-off
- FHIR id
- FHIR reference

Candidate preview 僅為 read-only mock display。
