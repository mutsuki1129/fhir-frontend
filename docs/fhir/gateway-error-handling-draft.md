# Gateway Error Handling Draft

本文件定義未來 Gateway / Adapter 錯誤處理原則。Phase 9A 是文件草案，不是 runtime Gateway validation，不是 POST endpoint，不是 production ingestion。

## Error Cases

- missing messageId
- missing subject
- invalid sourceSystem
- invalid trust status
- unsupported resource type
- invalid FHIR reference
- unsafe PHI exposure
- ambiguous review status
- payload too large
- binary content not allowed

## Handling Principles

錯誤處理原則：

```text
reject or hold
do not write to FHIR Server
do not mark as clinician-reviewed
do not mark as signed-off
return safe error summary
do not log token / secret / full sensitive payload
```

任何錯誤資料都不得觸發 FHIR create/update/delete/patch/upload，不得建立 DiagnosticReport、Observation、Condition、DocumentReference、Consent、Provenance 或 AuditEvent。

## Safe Error Summary

未來錯誤摘要應只回傳安全、最小化資訊，例如 `messageId`、error code、field path、safe reason。錯誤摘要不得包含 token、secret、完整病患敘述、binary content 或完整 payload。

## Review Boundary

錯誤資料不得自動進入 clinician-reviewed 或 signed-off。AI-generated 或 AI-suggested 資料若格式錯誤，只能 reject or hold，不能讓 AI Agent 直接寫入正式 FHIR Server。
