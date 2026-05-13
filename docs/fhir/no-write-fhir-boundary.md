# No-write FHIR Boundary

Phase 10B 與 Phase 10B.1 維持 dev-only mock prototype。`controlled_ingestion_prototype.allow_fhir_write` 固定為 `false`，不得由 env 開啟，也不得成為任何 FHIR writer 的入口。

## Prohibited Writes

- No FHIR create.
- No FHIR update.
- No FHIR delete.
- No FHIR patch.
- No FHIR upload.
- No DocumentReference binary write.
- No Patient write.
- No Observation write.
- No Condition write.
- No DiagnosticReport write.
- No Consent write.
- No Encounter write.
- No live HAPI `$validate`.

## Allowed Displays

- mock payload parser result
- mock validation result display
- mock manual review queue display
- candidate preview only
- read-only JSON response

Manual review queue mock 不等於 signed-off。Validation result mock 不等於 live HAPI `$validate`，也不等於 FHIR write approval。Candidate preview 不等於 persisted FHIR Resource；`persisted=false` 與 `fhirReference=null` 必須維持。

## Phase 10B.1 Hardening

Phase 10B.1 加固 no-write verification：Controller 不呼叫 FHIR write，MockIngestionPreviewService 不注入也不呼叫 `FhirApiClient`，沒有新增 FHIR writer service，沒有 queue worker，沒有 webhook receiver，沒有 background job，沒有 production FHIR Server write path。
