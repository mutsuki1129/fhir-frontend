# Future HAPI $validate Boundary

Phase 9B 不呼叫 live HAPI `$validate`。Phase 9B 不接 HAPI runtime。Phase 9B 不執行 validation request。Phase 9B 不寫入 FHIR Server。

本文只定義未來可能使用 HAPI `$validate` 時的安全邊界。這不是 validation runtime，不是 live integration，不是 production validation service，也不是 FHIR write path。

## Future Preconditions

未來若要使用 `$validate`，必須至少符合：

- dev/test only first
- no mutation
- no write
- no expunge
- no profile publication without review
- safe logging only
- OperationOutcome must be treated as validation result, not clinical conclusion
- validation passed does not equal signed-off

## Safety Rules

- `$validate` 結果不得自動寫入 FHIR Server。
- `$validate` pass 不等於 clinician-reviewed。
- `$validate` pass 不等於 signed-off。
- `$validate` failure 不得暴露完整敏感 payload。
- OperationOutcome 不是 clinical advice、automatic diagnosis 或 treatment recommendation。
- Future `$validate` 必須和 manual review gate、profile / IG governance、privacy review、provenance / audit planning 分開治理。

## Phase 9B No-Op Boundary

Phase 9B 只能在文件與測試中記錄 future HAPI `$validate` boundary。不得新增 live HAPI `$validate` caller、route、controller、service wiring、queue worker、background job、webhook receiver、FHIR create/update/delete/patch/upload、SMART production activation 或 CDS runtime activation。
