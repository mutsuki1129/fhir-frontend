# Sample Payload Audit Results

Sample payload:

`resources/fhir/mock-ingestion/sample-gateway-payload.json`

Audit result：

- synthetic data only
- 沒有真實姓名
- 沒有真實生日
- 沒有身分證
- 沒有地址
- 沒有電話
- 沒有真實醫療紀錄
- 沒有真實 binary
- 沒有 real PHI
- 明確標示 mock / synthetic / not for clinical use

Sample 使用 `Patient/mock-patient-001`、`MOCK-P-001`、`mock-agent-system` 與 `dev-mock-only` runtime。
