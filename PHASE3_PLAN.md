# Frontend Phase 3 Plan

## 目標

- 在不破壞既有 Phase 1/2 行為前提下，完成前端臨床流程的契約化與可維運化。
- 將 Phase 2 deferred 項目轉為可交付實作，並補齊測試與觀測能力。

## 範圍

1. Condition fallback 測試正式化
2. Media/DocumentReference 二進位上傳流程（含授權與安全策略）
3. Observation/Condition/DocumentReference 精準關聯
4. Doctor 模組遷移到 Practitioner（list/create/edit）
5. Practitioner 全模組驗證與 fallback 統一

## 非目標

- 不進行大規模視覺重設計
- 不改寫整個應用架構
- 不新增與臨床流程無關的大型功能模組

## 里程碑

### M1：契約與測試先行
- 對齊 backend 故障注入契約（Condition/DocumentReference）
- 將 fallback 測試納入 smoke/CI

### M2：附件流程升級
- 完成 binary upload UI（含驗證、錯誤處理、授權提示）
- 完成 DocumentReference/Media 正式提交流程

### M3：關聯模型收斂
- 導入 observation 級關聯欄位/策略
- 由 patient-latest 顯示改為明確關聯顯示

### M4：Practitioner 模組遷移
- doctor list/create/edit 切換到 Practitioner
- 與 rekam performer 的資料來源與驗證規則統一

### M5：封版與回歸
- 全路徑 smoke + 回歸驗證
- 文件、待辦、release notes 同步完成

## 風險

- 後端契約晚於前端排程，導致等待時間或重工
- 二進位附件流程牽涉安全與授權，若契約不完整會影響上線品質
- Practitioner 全模組遷移涉及 legacy 流程，回歸成本高

## 驗收標準

- 所有 Phase 2 deferred 項目在 Phase 3 中有明確結果（完成或新 deferred，需附理由）
- 主要路徑具可重複自動化驗證（至少 smoke 級）
- 附件與 condition 顯示在多筆病歷下可正確關聯，不再依賴 patient-latest 推論
- doctor 與 performer 流程在資料來源、驗證、fallback 行為一致

## 與後端依賴

1. Condition/DocumentReference 故障注入與測試契約
2. Binary upload 契約（格式、大小、儲存、授權、錯誤碼）
3. Observation/Condition/DocumentReference 關聯欄位契約
4. Practitioner profile 與 doctor 模組遷移契約
5. OperationOutcome 與 facade 錯誤映射一致性
