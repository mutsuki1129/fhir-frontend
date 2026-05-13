# Environment Guard Results

`EnsureControlledIngestionPrototypeEnabled` 條件：

- app environment in `local`, `testing`
- feature flag enabled = true
- mode = `mock`
- allow_fhir_write = false

Phase 10B.1 測試確認：

- production-like environment -> route unavailable
- feature flag disabled -> route unavailable
- mode != mock -> route unavailable
- allow_fhir_write true -> route unavailable

不符合條件時回 404。
