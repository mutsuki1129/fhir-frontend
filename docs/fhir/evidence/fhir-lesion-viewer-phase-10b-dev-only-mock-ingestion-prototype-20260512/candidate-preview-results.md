# Candidate Preview Results

Phase 10B 產生 candidate resource preview only。

Candidate preview 欄位包含：

- `candidateId`
- `resourceType`
- `mappingStatus=candidate`
- `validationStatus=profile-validation-required`
- `reviewStatus=pending-review`
- `persisted=false`
- `fhirReference=null`
- `preview`

candidate preview 不等於 persisted FHIR Resource。`candidateId` 不等於 FHIR Resource id。DiagnosticReport preliminary 不等於 final。Condition provisional 不等於 confirmed。
