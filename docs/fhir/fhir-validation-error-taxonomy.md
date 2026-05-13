# FHIR Validation Error Taxonomy

Phase 9B 定義 validation error taxonomy draft。這是文件草案，不是 runtime error handler，不是 production validation service，也不是 FHIR OperationOutcome replacement。

錯誤結果不得自動寫入 FHIR Server。錯誤結果不得自動標成 clinician-reviewed。錯誤結果不得自動標成 signed-off。任何 AI-generated 或 AI-suggested 資料不得因通過 schema 或 mapping 檢查而直接成為正式臨床簽核資料。

## Error Families

- `envelope.*`: message envelope、schema version、correlation metadata 問題。
- `source.*`: source system 身分、信任、環境分層問題。
- `trust.*`: data origin、review status、signed-off 宣告問題。
- `payload.*`: payload 結構、必要欄位、支援資源型別問題。
- `mapping.*`: Gateway / AI Agent payload 到 FHIR candidate 的 mapping 問題。
- `fhir-reference.*`: FHIR reference、patient compartment、cross-resource link 問題。
- `terminology.*`: code system、code、display、local mapping 問題。
- `profile.*`: Profile / IG 選擇、草案邊界、future validation 問題。
- `privacy.*`: PHI 暴露、最小必要資料、安全摘要問題。
- `binary.*`: binary payload、file content、attachment content 問題。
- `review.*`: 人工審核、簽核證據、修正流程問題。
- `runtime-boundary.*`: Phase 9B runtime 禁止事項或未啟用邊界問題。

## Draft Error Codes

| Error Code | 說明 | 建議處理 |
| --- | --- | --- |
| `envelope.missing_message_id` | 缺少 `messageId` | reject |
| `envelope.missing_schema_version` | 缺少 `schemaVersion` | reject |
| `source.unknown_system` | 未知來源系統 | hold |
| `source.untrusted_system` | 未信任來源 | reject / hold |
| `trust.invalid_data_origin` | `dataOrigin` 不合法 | reject |
| `trust.invalid_review_status` | `reviewStatus` 不合法 | reject |
| `trust.ai_marked_signed_off` | AI 資料被標成 signed-off | reject |
| `payload.missing_subject` | 缺少 subject | reject |
| `payload.unsupported_resource_type` | 不支援的 payload type | reject |
| `mapping.ambiguous_resource_mapping` | 無法安全 mapping | hold |
| `fhir-reference.invalid_reference` | FHIR reference 格式錯誤 | reject |
| `terminology.unknown_code_system` | 未知 code system | hold |
| `profile.profile_not_selected` | 未選定 Profile | hold |
| `privacy.phi_overexposure` | 可能過度暴露 PHI | reject / hold |
| `binary.binary_content_not_allowed` | payload 含 binary content | reject |
| `review.human_review_required` | 需要人工審核 | hold |
| `runtime.live_validate_not_enabled` | live `$validate` 未啟用 | hold / no-op |

## Handling Rules

- `reject` 表示不得建立 candidate write request，僅可回報安全摘要與錯誤碼。
- `hold` 表示進入人工或治理補件流程，不得自動寫入 FHIR Server。
- `hold / no-op` 表示 Phase 9B 沒有 live runtime，因此只能記錄為文件邊界。
- error detail 不得包含完整敏感 payload；只允許 safe summary。
- error result 不得升級為 clinician-reviewed 或 signed-off。

## Runtime Boundary

Phase 9B 不新增 validation runtime endpoint、不新增 Gateway runtime endpoint、不新增 AI Agent runtime endpoint、不新增 ingestion runtime endpoint、不新增 live HAPI `$validate` caller、不新增 FHIR writer，也不啟用 production ingestion。
