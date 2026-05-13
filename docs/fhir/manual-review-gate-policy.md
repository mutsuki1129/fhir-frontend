# Manual Review Gate Policy

Phase 9B 定義人工審核閘門草案。這是 governance / validation workflow 文件，不是 runtime，不是 live HAPI `$validate` integration，不是 FHIR write pipeline，也不是 production validation service。

## Core Policy

- AI-generated 不可直接寫入正式 FHIR Server。
- AI-suggested 不可直接成為 signed-off。
- validation-passed-candidate 不可直接成為 signed-off。
- clinician-reviewed 不一定等於 signed-off。
- signed-off 必須有明確簽核證據。
- DiagnosticReport.final 不等於 signed-off。
- Condition.provisional 不等於 confirmed。
- Consent.active 不等於醫療結果成立。

## Review Gate Outcomes

- `pending-review`: 等待人工審核，不能寫入正式 FHIR Server。
- `clinician-reviewed`: 已有臨床人員審閱紀錄，但不一定 signed-off。
- `signed-off`: 有明確簽核證據，且仍需未來 controlled write governance 允許才可進入寫入流程。
- `rejected`: 審核拒絕，不得寫入 FHIR Server。
- `superseded`: 被較新版本或修正版本取代。
- `requires-correction`: 需要修正來源、mapping、terminology、profile 或審核證據。

## Future Controlled Write Preconditions

未來若要考慮 controlled FHIR write，至少必須另行滿足：

1. envelope validation passed
2. source system accepted
3. trust status valid
4. mapping candidate reviewed
5. FHIR candidate validation completed
6. human review completed where required
7. signed-off evidence present where required
8. audit / provenance candidate prepared
9. write path explicitly enabled by future governance

## Phase 9B Boundary

Phase 9B 不啟用 validation runtime，不新增 Gateway runtime，不新增 AI Agent runtime，不新增 ingestion runtime，不新增 queue worker，不新增 webhook receiver，不新增 FHIR writer，不呼叫 live HAPI `$validate`，不寫入 FHIR Server，也不提供 clinical advice、automatic diagnosis 或 treatment recommendation。
