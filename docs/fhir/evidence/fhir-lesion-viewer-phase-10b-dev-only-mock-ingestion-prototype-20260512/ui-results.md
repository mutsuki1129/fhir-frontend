# UI Results

新增 dev-only page：

- `resources/views/dev/fhir/mock-ingestion/index.blade.php`
- `GET /dev/fhir/mock-ingestion`

UI 顯示：

- Dev-only Mock Ingestion Prototype
- Feature flag status
- No-write FHIR boundary
- Mock payload textarea
- Preview button
- Mock validation result
- Mock manual review queue item
- Candidate resource preview
- Safety notices

UI 不加入主產品 sidebar。UI 不宣稱 production ingestion、FHIR write enabled、AI diagnosis、clinical advice、treatment recommendation、signed-off、clinician-confirmed 或 written-to-fhir。
