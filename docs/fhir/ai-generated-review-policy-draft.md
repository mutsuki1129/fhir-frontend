# AI-generated Review Policy Draft

## Purpose

本文件定義 AI-generated / AI-suggested 資料審核政策。AI-generated 不等於 clinician-reviewed。AI-suggested 不等於 signed-off。AI Agent output 不得直接寫入正式 FHIR Server。AI Agent output 不得直接成為 Condition.confirmed。AI Agent output 不得直接成為 DiagnosticReport signed-off。

## Data States

- ai-generated：由模型或 AI Agent 產生，尚未人工審核。
- ai-suggested：作為候選建議，尚未確認。
- pending-review：等待人工審核。
- clinician-reviewed：已由指定審閱者審閱，但不一定 signed-off。
- signed-off：有明確簽核證據。
- rejected：審核後拒絕。
- superseded：已被更新版本取代。
- requires-correction：需要修正後再審。

## Future Review Requirements

1. source system known。
2. model version recorded where applicable。
3. payload classified。
4. validation result available。
5. human reviewer assigned。
6. reviewer action recorded。
7. rejected / superseded handled。
8. no automatic diagnosis。
9. no treatment recommendation。
10. no AI-only sign-off。

## Safety Boundary

AI confirmed、AI diagnosis、signed-off without review 等語意不得作為已完成或已啟用狀態。Phase 9C 不新增 AI Agent runtime，不讓 AI Agent 直接寫入正式 FHIR Server。
