# Gateway to FHIR Mapping Draft

Phase 9A 只定義 candidate mapping。不實作 mapping runtime。不寫入 FHIR Server。不新增 FHIR write route。不新增 production ingestion。

| Gateway Payload | Candidate FHIR Resource | Phase 9A 規則 |
|---|---|---|
| subject.patientReference | Patient | 只作 reference，不建立 Patient |
| event.encounterReference | Encounter | 只作 reference，不建立 Encounter |
| payload.observations[] | Observation | AI value 只可 pending，不可自動解讀 |
| payload.conditions[] | Condition | 不可直接 confirmed，需 verificationStatus |
| payload.diagnosticReports[] | DiagnosticReport | 不等於自動診斷 |
| payload.documents[] | DocumentReference | metadata only，不暴露 binary content |
| payload.consents[] | Consent | 授權/簽核，不代表醫療結果 |
| provenance | Provenance / AuditEvent | Future draft only |

## Resource Rules

Patient / Encounter reference 僅用於關聯候選資料。Phase 9A 不建立 Patient，不建立 Encounter，不查詢或寫入正式 FHIR Server。

Observation candidate 可以保留數值、單位、時間與來源，但 AI-generated value 只可 pending reference，不可自動解讀為異常、診斷或治療建議。

Condition candidate 必須保留 verification boundary。Condition.provisional 不等於 confirmed；AI-suggested Condition 不得自動成為 confirmed。

DiagnosticReport candidate 可以保存 metadata 與 reference，但 DiagnosticReport.final 不等於 signed-off，DiagnosticReport 不等於自動診斷。

DocumentReference candidate 只處理 metadata / reference display。DocumentReference 不暴露 binary content，不建立下載授權，不處理 protected download policy runtime。

Consent candidate 只表示授權、簽署或治理狀態。Consent.active 不代表醫療結果成立。

Provenance / AuditEvent 是 future draft only。Phase 9A 不寫 Provenance，不寫 AuditEvent，不新增 FHIR mutation。
