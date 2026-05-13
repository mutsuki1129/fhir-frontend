# FHIR Validation Workflow Draft

## Purpose

Phase 9B 是 FHIR Validation Workflow Draft。本文定義未來 Gateway / AI Agent payload 轉成 FHIR candidate 後，在寫入正式 FHIR Server 前應經過的驗證、分類、阻擋、回報與人工審核流程草案。

本文件是 workflow draft，不是 validation runtime，不是 live HAPI `$validate` integration，不是 FHIR write path，不是 production validation service，不是 Gateway ingestion runtime，不是 AI Agent runtime，不是 CDS runtime，也不是 SMART production activation。

Phase 9B 的產出只包含 validation workflow draft、validation error taxonomy、validation result contract、profile / IG draft boundary、manual review gate policy、future `$validate` boundary、runtime safety tests 與 evidence package。

## Draft Workflow

1. Gateway envelope validation
   - 檢查 `messageId`、`schemaVersion`、`correlationId`、來源標記與最小必要 envelope 欄位。
   - envelope 不完整時不得進入 mapping 或 write path，只能 reject 或 hold。

2. Source system validation
   - 檢查來源系統是否已列入未來允收清單、是否可追溯、是否可分辨測試與正式來源。
   - unknown 或 untrusted source 不得自動寫入 FHIR Server。

3. Trust / review status validation
   - 驗證 `dataOrigin`、`reviewStatus`、`signedOff` 等信任欄位。
   - AI-generated 不等於 clinician-reviewed。
   - AI-suggested 不等於 signed-off。
   - signed-off 必須有明確簽核證據。

4. Payload structure validation
   - 檢查 payload 是否符合文件用 schema draft 的必要欄位與資料形狀。
   - 不允許 binary content、過量 PHI、或無法分類的自由文字直接成為 FHIR candidate。

5. FHIR reference validation
   - 檢查 Patient、Encounter、Observation、Condition、DiagnosticReport、DocumentReference、Consent 等 reference 是否可辨識。
   - reference 格式錯誤或跨病患歸屬不明時必須 reject 或 hold。

6. Candidate FHIR Resource mapping validation
   - 檢查 payload 到 FHIR candidate 的 resource type、identifier、subject、effective time、status 與 reference mapping 是否明確。
   - ambiguous mapping 必須 hold-for-review，不能自動推論臨床意義。

7. Terminology validation draft
   - 檢查 code system、code、display 與 local terminology mapping 是否可辨識。
   - unknown code system 或未核准 mapping 只能 hold，不能直接成為 signed-off。

8. Profile / IG validation draft
   - 檢查候選 resource 是否標記未來要套用的 profile / IG 草案。
   - Phase 9B 不發布 Profile、不發布 IG、不執行 live profile validation。

9. Future FHIR `$validate` boundary
   - 未來可考慮 dev/test only 的 HAPI `$validate`，但 Phase 9B 不呼叫 live HAPI `$validate`。
   - OperationOutcome 只能作為 validation result，不是臨床結論。

10. Validation result contract
    - 產出 draft validation result envelope，包含狀態、outcome、candidateResources、errors 與 review gate 資訊。
    - validation result 不是 write confirmation，也不是 signed-off confirmation。

11. Manual review gate
    - 需要人工審核的 candidate 必須停在 pending-review、clinician-reviewed、requires-correction、rejected、superseded 或 signed-off 的明確狀態。
    - validation-passed-candidate 不等於 signed-off。

12. Future controlled FHIR write path
    - 只有未來治理另行核准後，才可考慮 controlled FHIR write path。
    - Phase 9B 不新增任何 FHIR write route、writer、queue worker、webhook receiver 或 ingestion endpoint。

## Validation Outcomes

- `accepted-for-review`: 結構與來源足以進入人工或治理審核，但尚未寫入 FHIR Server。
- `hold-for-review`: 需要補充來源、mapping、terminology、profile 或審核證據。
- `rejected`: payload、來源、信任狀態、privacy 或 reference 風險不可接受。
- `mapping-required`: 尚未能安全轉成 FHIR candidate。
- `profile-validation-required`: 需要未來 Profile / IG 或 `$validate` 邊界確認。
- `manual-review-required`: 需要 clinician / authorized reviewer 進一步審核。
- `validation-passed-candidate`: validation draft 條件暫時通過，可作為候選結果進入後續審核。
- `validation-failed`: validation draft 條件不通過。

`validation-passed-candidate` 不等於 signed-off。`validation-passed-candidate` 不等於 clinician-reviewed。`validation-passed-candidate` 不代表已寫入 FHIR Server。

## Explicit Non-Runtime Boundary

Phase 9B 不啟用 validation runtime，不執行 live HAPI `$validate`，不寫入 FHIR Server，不建立 POST validation endpoint，不建立 queue worker，不建立 Gateway runtime，不建立 AI Agent runtime，不建立 ingestion runtime，不啟用 CDS runtime，不啟用 SMART production mode，也不產生 clinical advice、automatic diagnosis 或 treatment recommendation。
