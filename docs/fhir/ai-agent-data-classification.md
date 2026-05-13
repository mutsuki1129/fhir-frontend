# AI Agent Data Classification

本文件定義 Phase 9A Gateway / AI Agent Adapter Contract Draft 的資料分類與審核語意。這是文件與 governance draft，不是 runtime policy engine，不是 production ingestion，也不是 FHIR write pipeline。

## Classification

- `ai-generated`: AI Agent 或外部自動化流程產生的資料。ai-generated 不等於 clinician-reviewed。
- `ai-suggested`: AI Agent 或外部系統提出的候選建議。ai-suggested 不等於 signed-off。
- `clinician-observed`: 醫護觀察或記錄的資料，但不代表完成簽核。
- `clinician-reviewed`: 醫護已審閱資料，但不一定完成正式簽核。
- `signed-off`: 已有明確簽核證據的資料。signed-off 需要明確簽核證據。
- `rejected`: 候選資料被拒絕，不得被當成臨床確認結果。
- `superseded`: 已被新版本取代，只能作歷史或審計參考。

## Required Distinctions

ai-generated 不等於 clinician-reviewed。
ai-suggested 不等於 signed-off。
DiagnosticReport.final 不等於 signed-off。
Condition.provisional 不等於 confirmed。
Consent.active 不等於醫療結果成立。
DiagnosticReport 不等於自動診斷。
DocumentReference metadata / reference display 不暴露 binary content。

## Display Rules

| 狀態 | 可否進 Lesion Viewer | 可否標示 signed-off | 說明 |
|---|---|---|---|
| ai-generated | 可作 pending reference | 不可 | 僅表示 AI 產生資料 |
| ai-suggested | 可作 pending reference | 不可 | 僅表示 AI 建議候選資料 |
| clinician-observed | 可顯示 | 不可自動 signed-off | 醫護觀察但不代表簽核 |
| clinician-reviewed | 可顯示 | 不一定 | 已審閱但未必完成簽核 |
| signed-off | 可顯示 | 可 | 需明確簽核證據 |
| rejected | 可視情況顯示 | 不可 | 被拒絕的候選資料 |
| superseded | 可作歷史記錄 | 不可 | 已被新版本取代 |

## Governance Boundary

Lesion Viewer 只能呈現 read-only display。任何 AI-generated 或 AI-suggested 資料若未通過醫護審核，不得寫入正式 FHIR Server，不得轉成 clinician-reviewed，不得轉成 signed-off，不得呈現為 clinical advice、automatic diagnosis 或 treatment recommendation。
