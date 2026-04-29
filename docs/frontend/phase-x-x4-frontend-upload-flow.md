# Phase X X4 前端附件流程（第一版）

## 目標

在不破壞現有後端契約（DocumentReference 仍以 URL 儲存）的前提下，提供病例頁最小可用附件流程 UI。

## 本輪完成

1. 在 `/rekam/create`、`/rekam/{id}/edit` 新增附件區塊：
   - 檔案選擇器
   - 可接受格式提示
   - 5 MB 上限提示
2. 新增前端驗證：
   - 未選檔案
   - 檔案大小超過 5 MB
   - 不支援 MIME 類型
3. 新增上傳中狀態：
   - 顯示「附件驗證中...」非阻斷提示
4. 新增成功/失敗提示：
   - 成功：檔案驗證通過，若標題空白則自動帶入檔名
   - 失敗：顯示明確錯誤原因（無檔案/過大/格式不支援）
5. 補齊 i18n key（`en`、`zh_TW`）：
   - `ui.rekam.attachment_upload_*`

## 契約與相容性說明

- 本版不新增後端上傳 API 呼叫。
- 既有 `document_reference_url` 欄位仍是最終送出依據。
- 附件區塊屬於 Phase X 第一版掛點，避免破壞既有 Observation/Condition/DocumentReference 流程。

## 後續建議（Deferred）

1. 後端提供 Binary/Media 上傳端點與存取策略後，再把「驗證」升級為真正上傳。
2. 補齊檔案掃毒、內容類型雙重檢查與下載授權策略。
3. 將目前前端提示改為「可追蹤上傳任務」與可重試流程。
