# Manual Review Queue Mock Results

Phase 10B 產生 mock manual review queue item：

- `queueItemId=review-{messageId}`
- `status=pending-review`
- `dataOrigin=ai-generated`
- `validationStatus=hold-for-review`
- `reviewRequired=true`
- `signedOff=false`

manual review queue mock only。manual review queue mock 不等於 signed-off，不等於 clinician-confirmed，不等於 production-approved，不等於 written-to-fhir。
