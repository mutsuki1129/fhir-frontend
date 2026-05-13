# Audit / Provenance / Logging Policy Draft

## Purpose

本文件定義 audit / provenance / logging 政策。Phase 9C 只做 policy draft。不新增 logging runtime。不新增 audit writer。不寫 FHIR Provenance / AuditEvent。

## Future Required Fields

未來若進入 controlled ingestion 或 review runtime，應記錄 messageId、correlationId、sourceSystem、agentId、modelVersion where applicable、dataOrigin、reviewStatus、validationId、candidateResource references、review action、sign-off evidence reference、timestamp、actor / role。

## Prohibited Log Content

禁止記錄 token、secret、password、full PHI payload、binary content、raw base64 document、unmasked identifier、sensitive attachment content。

## Candidate FHIR Resources

未來候選 FHIR Resource 可包含 Provenance、AuditEvent、DocumentReference metadata、Consent reference。但 Phase 9C 不寫 FHIR Provenance / AuditEvent，不新增 logging runtime，不新增 audit writer。
