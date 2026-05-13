# API Contract Results

`POST /dev/fhir/mock-ingestion/preview` 回應：

```json
{
  "data": {
    "messageId": "msg-001",
    "correlationId": "case-001",
    "validationResult": {},
    "manualReviewQueueItem": {},
    "candidateResources": []
  },
  "meta": {
    "readOnly": true,
    "devOnly": true,
    "mockOnly": true,
    "noFHIRWrite": true,
    "featureFlag": "FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED",
    "source": "dev-mock-controlled-ingestion-prototype"
  }
}
```

錯誤時仍回 safe mock validation result，不回傳完整敏感 payload。
