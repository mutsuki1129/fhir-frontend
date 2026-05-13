# Gateway Validation Boundary Draft

本文件定義未來 Gateway 應做的 validation 層次。Phase 9A 不執行 live HAPI `$validate`。Phase 9A 不執行 live FHIR write。Phase 9A 不執行 runtime Gateway validation。Phase 9A 只定義 validation boundary draft。

## Validation Layers

1. Envelope validation: 檢查 `schemaVersion`、`messageId`、`correlationId` 與 envelope 結構。
2. Required field validation: 檢查 `sourceSystem`、`subject`、`event`、`trust`、`payload`、`provenance` 是否存在。
3. Source system validation: 檢查 `systemType` 是否屬於草案允許值，並避免未知來源直接進入 mapping。
4. Trust / review status validation: 檢查 `dataOrigin` 與 `reviewStatus` 是否一致；AI output 不得直接成為 signed-off。
5. FHIR reference validation: 檢查 Patient / Encounter reference 格式；Phase 9A 不查 live server。
6. Payload type validation: 檢查 observations、conditions、diagnosticReports、documents、consents 等資料型別。
7. Terminology validation draft: 未來可檢查 code system / code / display，但 Phase 9A 不做 terminology runtime。
8. Privacy / PHI minimization check: 拒絕 token、secret、binary content、過量敏感敘述與不必要 PHI。
9. Mapping candidate validation: 僅形成 mapping candidate，不產生 FHIR write payload。
10. Human review boundary: pending-review 必須保留到醫護審核，不得自動升級。
11. Future FHIR `$validate` boundary: 未來才可在受控流程中使用，且不得等同 production write approval。

## Reject / Hold Boundary

缺欄位、格式不合、來源未知、trust status 衝突、FHIR reference 格式錯誤、payload 過大、含 binary content 或含敏感 token 時，未來 Gateway 應 reject or hold。處理結果不得寫入 FHIR Server，不得標為 clinician-reviewed，不得標為 signed-off。

## Phase 9A Non-Runtime Statement

Phase 9A 不新增 Gateway runtime endpoint，不新增 queue worker，不新增 webhook receiver，不新增 background job，不呼叫 FHIR create/update/delete/patch/upload，不啟用 SMART production mode，不啟用 CDS runtime。
