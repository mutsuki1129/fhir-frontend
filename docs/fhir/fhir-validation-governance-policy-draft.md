# FHIR Validation Governance Policy Draft

## Purpose

本文件銜接 Phase 9B validation workflow，定義未來 validation governance。Validation passed 不等於 clinical correctness。Validation passed 不等於 signed-off。Validation passed 不等於 FHIR write approved。OperationOutcome 不代表醫療結論。

## Future Governance Requirements

1. validation environment。
2. selected Profile / IG。
3. terminology policy。
4. error taxonomy。
5. validation result retention。
6. manual review boundary。
7. safe logging。
8. no mutation。
9. no write without governance approval。
10. no live `$validate` in Phase 9C。

## Safety Boundary

Phase 9C 不新增 validation runtime endpoint，不新增 live HAPI `$validate` integration，不新增 production validation service，不新增 FHIR write path。OperationOutcome 只能作為技術驗證結果，不得當成 clinical conclusion。
