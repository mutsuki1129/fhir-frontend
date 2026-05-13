# FHIR Profile / IG Draft Boundary

Phase 9B 只定義 Profile / IG draft boundary。Phase 9B 不發布 Profile，不發布 IG，不執行 live profile validation，不執行 live HAPI `$validate`，不接 HAPI runtime，也不寫入 FHIR Server。

本文件中的 profile / IG 名稱都是 future candidate boundary，不是正式規格，不是 publication package，也不是 validation runtime。

## Draft Profile / IG Scope

- DiagnosticReport lesion profile draft
  - 描述病灶檢查結果中心聚合的候選欄位與 reference 需求。
- Observation evidence profile draft
  - 描述影像、量測、臨床觀察或輔助 evidence 的候選欄位。
- Condition lesion status profile draft
  - 描述病灶狀態候選資料，但不產生 automatic diagnosis。
- DocumentReference metadata profile draft
  - 只處理 metadata 與 reference display policy，不包含 binary content 發布。
- Consent / eCSU reference profile draft
  - 描述 Consent / eCSU reference linkage，不代表醫療結果成立。
- Encounter interaction event profile draft
  - 描述互動或檢查事件脈絡，不推論治療或診斷。
- Patient subject display policy draft
  - 定義 read-only Lesion Viewer 可顯示的 Patient subject 最小必要資訊。
- Provenance / AuditEvent draft
  - 描述未來 controlled write 前可能需要準備的 provenance / audit candidate。

## Governance Boundary

目前只是 draft boundary。未來正式 Profile / IG 需另行 governance review，並且必須分開處理：

- profile authoring review
- IG publication approval
- terminology binding review
- privacy / PHI review
- patient compartment review
- manual review gate alignment
- dev/test `$validate` approval
- production activation review

Phase 9B 的文件不得被解讀為 Profile 已發布、IG 已發布、live `$validate` 已接線、FHIR write pipeline enabled、production validation service enabled、或 SMART production activated。
