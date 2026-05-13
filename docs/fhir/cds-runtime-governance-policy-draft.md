# CDS Runtime Governance Policy Draft

## Purpose

本文件定義 CDS runtime 的禁止條件與未來啟用條件。Phase 9C 不啟用 CDS runtime。Phase 9C 不新增 CDS Hooks endpoint。Phase 9C 不新增 clinical advice。Phase 9C 不新增 automatic diagnosis。Phase 9C 不新增 treatment recommendation。

## Prohibition Criteria

CDS runtime 在下列條件任一成立時必須 blocked：

1. 未通過 clinical governance review。
2. 未定義責任歸屬。
3. 未定義人工審核流程。
4. 未定義錯誤處理。
5. 未定義 audit trail。
6. 未定義適用範圍。
7. 未定義禁用場景。
8. 未定義醫師 override。
9. 未定義風險分級。
10. 未定義法律 / 合規審查。

## Future Activation Criteria

未來若另行授權 CDS runtime，必須先以 dev/test only first 推進，以 non-blocking mode first 啟動，且必須明確維持 no automatic diagnosis、no automatic treatment recommendation、clear human review boundary 與 explicit clinical governance approval。

## Safety Boundaries

CDS runtime enabled、CDS Hooks enabled、clinical advice、automatic diagnosis、treatment recommendation 等語意只能出現在禁止、未啟用、deferred 或 future conditions 語境。Phase 9C 不提供任何 CDS hook runtime、handler、route、queue worker 或 clinical decision support runtime。
