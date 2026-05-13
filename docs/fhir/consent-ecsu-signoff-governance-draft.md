# Consent / eCSU / Sign-off Governance Draft

## Purpose

本文件定義 Consent / eCSU / 簽核治理。Consent.active 不代表醫療結果。Consent.active 不代表病灶確認。Consent.active 不代表 RWE 成立。Consent.active 不代表 signed-off。eCSU / 簽核 workflow 目前是 governance draft，不是 runtime。

## Concept Boundaries

- Consent：授權 / 同意 / 資料使用範圍。
- eCSU：電子醫、病評估承認書的未來治理概念。
- signed-off：需要明確簽核證據。
- clinician-reviewed：已審閱但不一定 signed-off。
- validation-passed-candidate：格式 / 驗證候選，不等於簽核。

DiagnosticReport.final 不等於 signed-off。Condition.provisional 不等於 confirmed。AI-generated data 不等於 clinician-reviewed。AI Agent output 不得直接轉成 signed-off。

## Future Sign-off Preconditions

1. reviewer identity。
2. reviewer role。
3. reviewedAt。
4. sign-off evidence。
5. consent scope。
6. related document reference。
7. audit / provenance candidate。
8. no AI-only sign-off。
9. no auto sign-off from validation。
10. no auto sign-off from DiagnosticReport.final。

## Safety Boundary

Phase 9C 不新增 sign-off runtime、不新增 eCSU runtime、不新增 clinical workflow runtime、不把 Consent.active 當成醫療結果。
