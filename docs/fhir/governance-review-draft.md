# FHIR Read-only Lesion Viewer Phase 9C Governance Review Draft

## Purpose

本文件定義 FHIR Read-only Lesion Viewer 後續要從 draft / read-only / contract 狀態進入 runtime、controlled ingestion 或 production review 前，必須經過的 governance review draft。

Phase 9C 是治理草案。這不是 runtime activation。這不是 production approval。這不是 SMART production activation。這不是 CDS runtime activation。這不是 FHIR write approval。

本文件也明確保留安全邊界：Phase 9C 不啟用 runtime，不新增 production gateway，不新增 AI Agent runtime，不新增 ingestion endpoint，不新增 validation endpoint，不新增 FHIR write path，不啟用 SMART production，不啟用 CDS runtime。

## Governance Domains

1. Read-only Lesion Viewer governance：確認 Lesion Viewer 仍只做病灶 / 臨床資料 read-only display，mock source 仍可用，source=fhir 仍維持 read-only。
2. Gateway / AI Agent adapter governance：延續 Phase 9A contract draft，只定義 adapter envelope、payload schema、classification、mapping draft 與 error boundary。
3. FHIR validation governance：延續 Phase 9B validation workflow draft，只定義 validation result、manual review gate、Profile / IG boundary 與 future HAPI `$validate` boundary。
4. SMART production activation governance：定義未來 SMART production activation 條件，但 Phase 9C 不啟用 SMART production。
5. CDS runtime governance：定義 CDS runtime 禁止條件與未來啟用條件，但 Phase 9C 不啟用 CDS runtime。
6. DocumentReference protected download governance：DocumentReference 目前只顯示 metadata / reference，不直接暴露 binary content。
7. Consent / eCSU / sign-off governance：Consent.active 不代表醫療結果，eCSU / 簽核 workflow 目前只是治理草案，不是 runtime。
8. Patient display governance：Patient 只作 subject reference / 安全摘要，不恢復成 Patient management UI。
9. AI-generated review governance：AI-generated 不等於 clinician-reviewed，AI Agent output 不得直接轉為 signed-off。
10. Audit / Provenance / logging governance：定義未來需要記錄與禁止記錄的欄位，但 Phase 9C 不新增 logging runtime 或 audit writer。
11. Controlled ingestion readiness governance：定義 Phase 10 controlled ingestion prototype 前的 readiness checklist，但 checklist completed 不等於 production approval。

## Governance Outcomes

- draft-only：僅可作為草案、規劃、審查材料，不得進入 runtime。
- requires-review：需要治理、資安、隱私、臨床或工程審查後才能進入下一階段。
- approved-for-dev-prototype：只允許 dev-only prototype；approved-for-dev-prototype 不等於 production approved。
- approved-for-test-runtime：只允許受控測試 runtime；approved-for-test-runtime 不等於 production approved。
- approved-for-production-review：只代表可進入 production review；approved-for-production-review 不等於 production activated。
- blocked：因安全、合規、臨床風險或資料邊界未滿足而禁止推進。
- deferred：保留未來討論，不代表允許 implementation。

## Non-Activation Statement

Phase 9C 是 Governance Review Draft。這不是 production activation。這不是 SMART production activation。這不是 CDS runtime activation。這不是 Gateway runtime。這不是 AI Agent runtime。這不是 ingestion runtime。這不是 validation runtime。這不是 FHIR write pipeline。這不是 clinical decision support runtime。

## Required Review Question

核心問題是：什麼條件下，某個功能才可以從 draft / read-only / contract 進入 runtime 或 production？

本文件的答案是：必須先有明確 activation criteria、prohibition criteria、risk boundary、manual review gate、audit / provenance plan、PHI / privacy review、security review、clinical workflow review，並取得明確且分階段的治理核准。任何 draft-only、requires-review 或 deferred 狀態均不得被解讀為 runtime 或 production approval。
