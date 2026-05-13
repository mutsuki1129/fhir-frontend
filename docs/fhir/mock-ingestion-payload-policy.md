# Mock Ingestion Payload Policy

本政策定義未來 prototype 可使用的 mock payload 規則。Mock payload 不代表正式資料。Mock payload 不得寫入正式 FHIR Server。Mock payload 不得被標示為 signed-off。

## Allowed Mock Fields

- synthetic messageId
- synthetic correlationId
- synthetic sourceSystem
- synthetic patientReference
- synthetic Observation candidate
- synthetic Condition candidate
- synthetic DiagnosticReport candidate
- synthetic DocumentReference metadata candidate
- synthetic Consent reference candidate
- synthetic validation result

## Forbidden Payload Content

- real patient name
- real birthday
- real national ID
- real address
- real phone
- real medical record
- real binary document
- real AI Agent output from production
- real PHI payload
- unmasked identifier

Mock-only payload is planning material for a future dev-only prototype. It is not production Gateway input and not production ingestion.

