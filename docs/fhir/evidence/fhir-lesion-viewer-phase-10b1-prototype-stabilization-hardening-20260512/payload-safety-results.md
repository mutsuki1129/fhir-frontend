# Payload Safety Results

Phase 10B.1 payload safety hardening：

- invalid JSON 不造成 500。
- missing required fields 不造成 500。
- unknown payload keys 不造成 500。
- oversized payload 回 safe validation result。
- binary-like content 不被接受為正式文件。
- real-PHI-like subject fields 不被回顯為正式資料。
- response 不回傳完整 raw payload。
- response 不回傳 raw binary content。
- service 沒有記錄完整 payload、token、secret 或 full PHI。

Safe error result 使用：

```json
{
  "status": "hold-for-review",
  "outcome": "manual-review-required",
  "errors": [
    {
      "code": "payload.invalid_or_missing_required_fields",
      "severity": "error",
      "message": "Mock payload must be valid JSON.",
      "safeDetails": "No sensitive payload included."
    }
  ]
}
```
