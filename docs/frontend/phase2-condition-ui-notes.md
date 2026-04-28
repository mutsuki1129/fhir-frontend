# Phase 2 Condition UI Notes

## 目的

本文件說明 Phase 2 在病例（rekam）頁面的 Condition UI 規則，以及同頁的 Practitioner、DocumentReference baseline 行為，確保前端與目前後端契約一致，且不破壞既有 Phase 1 Observation 流程。

## 契約對齊（目前版本）

對應 `RekamController` 現行驗證：

- `condition_code`: `nullable|string|max:64`
- `condition_text`: `nullable|string|max:255`

前端最小驗證採同一組規則：

- create/edit 表單輸入框分別設定 `maxlength=64`、`maxlength=255`
- 欄位維持 optional，不新增比後端更嚴格的格式限制
- 錯誤訊息仍由後端驗證回傳，前端透過既有 alert/input-error 顯示

## UI 行為規則

### 1) create/edit 頁面

- 保留 Phase 1 既有欄位（patient / temperature / effective datetime / legacy note）
- 新增並保留 Condition 欄位：
  - `Condition Code (Optional)`
  - `Condition Text (Optional)`
- 提示文字明確標示 backend contract 的最大長度
- 新增並保留 Practitioner 欄位：
  - `Practitioner (Optional)`（來源為 FHIR `Practitioner`）
- 新增 DocumentReference baseline 欄位：
  - `Document Title (Optional)`
  - `Document URL (Optional)`

### 2) `/rekam` list 卡片狀態

- 有 Condition（text 或 code）：
  - 顯示 `Condition available` 標記
  - 顯示 Condition 摘要（text，若有 code 則附上）
- 無 Condition 但有 legacy note：
  - 顯示 `Fallback: legacy note` 標記
  - 顯示 legacy note 內容
- Condition 服務不可用：
  - 頁面上方顯示 non-blocking 警示
  - Observation 卡片仍正常顯示，流程不中斷
- DocumentReference 可用時：
  - 顯示 `DocumentReference available` 標記
  - 顯示可點擊附件連結
- DocumentReference 不可用或未提供時：
  - 顯示 `No document reference` 狀態，不阻斷 Observation

## Fallback 準則

- Observation 仍為既有主流程，Condition 同步為加值流程
- Condition API 失敗時：
  - Observation 儲存/更新不應被阻斷
  - 使用者可透過警示得知 Condition 未成功同步
  - legacy note 仍可作為過渡資訊
- DocumentReference API 失敗時：
  - Observation 儲存/更新不應被阻斷
  - 使用者可透過警示得知附件未成功同步

## 測試現況

- 已有 smoke script 驗證 Condition create/update/list 成功路徑：
  - `scripts/phase2-condition-smoke.ps1`
- Condition API 失敗路徑的自動化測試目前 deferred（需後端提供可控故障注入契約）。

## 邊界

- 本批次已引入 `DocumentReference` URL 型 baseline，不含 binary upload
- 本批次已在 rekam 流程接入 `Practitioner`，但 doctor 模組仍未全量遷移
- 不做大幅 UI 改版與破壞性改動
