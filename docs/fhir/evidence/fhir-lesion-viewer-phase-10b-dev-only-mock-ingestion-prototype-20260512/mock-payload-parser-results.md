# Mock Payload Parser Results

`MockIngestionPreviewService` 接收 string / array mock payload，解析 JSON 後只做 safe envelope validation。

## Required Fields

- `schemaVersion`
- `messageId`
- `correlationId`
- `sourceSystem`
- `subject`
- `event`
- `trust`
- `payload`

缺欄位時回 `hold-for-review` / `manual-review-required`，error 只包含 safeDetails，不回傳完整 payload。

## Safe Input Boundary

- 只接受 synthetic mock patient reference，例如 `Patient/mock-patient-001`
- 只接受 synthetic display id，例如 `MOCK-P-001`
- 拒絕 DocumentReference binary-like content 欄位
- 不記錄完整 payload 到 log
- 不記錄 token / secret / full PHI
