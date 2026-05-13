# Feature Flag Hardening Results

## 結果

Phase 10B.1 確認並加固：

- `FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED` 預設 false。
- `FHIR_CONTROLLED_INGESTION_PROTOTYPE_MODE` 預設 mock。
- `controlled_ingestion_prototype.allow_fhir_write` 固定 false。
- `allow_fhir_write` 不由 env 開啟。
- feature flag disabled 時 GET / POST route 均不可用。
- mode 非 mock 時 route 不可用。
- allow_fhir_write 被測試覆寫成 true 時 guard 仍回 404。

不符合 guard 條件時維持 404，避免 production-like environment 暴露 prototype route。
