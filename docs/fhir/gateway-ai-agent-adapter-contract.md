# Gateway / AI Agent Adapter Contract Draft

## Purpose

本文件是未來 AI Agent / 外部臨床流程進入 FHIR 生態前的 adapter contract draft。它定義外部資料在進入 Gateway / Adapter 之前應具備的 envelope、來源標記、信任狀態、審核狀態、FHIR candidate mapping 與 validation boundary。

暫定資料流：

```text
AI Agent / Concordance Backend / MPSW / External Workflow
        ↓
Gateway / Adapter
        ↓
Validation / Review Boundary
        ↓
FHIR Mapping Candidate
        ↓
Future controlled FHIR write path
        ↓
FHIR Server
        ↓
FHIR Read-only Lesion Viewer
```

目前只完成 Lesion Viewer read-only display。Gateway / Adapter 本階段只做 contract draft。不啟用 runtime。不寫入 FHIR Server。

Phase 9A 是 Gateway / AI Agent Adapter Contract Draft。
這不是 Gateway runtime。
這不是 AI Agent runtime。
這不是 FHIR write pipeline。
這不是 production ingestion。
這不是 CDS runtime。
這不是 SMART production activation。

本階段產出是 contract draft、payload schema draft、mapping boundary、validation boundary、governance notes、deferred runtime plan。它不是 POST endpoint、queue worker、FHIR writer、AI Agent connector、production gateway 或 clinical decision engine。

## Envelope Contract

未來 Gateway / Adapter 應只接收具備明確 envelope 的資料。Envelope 必須至少描述：

- `schemaVersion`: payload schema draft 版本。
- `messageId`: 單一訊息識別碼，用於冪等性與安全錯誤摘要。
- `correlationId`: 外部案例、流程或工作項的關聯識別碼，不應包含真實病患姓名或敏感敘述。
- `sourceSystem`: 外部系統來源，包含 `systemId`、`systemType`、`displayName`。
- `agentContext`: AI Agent 或外部流程的角色、版本與 runtime 狀態；Phase 9A 必須固定表明 runtime 未啟用。
- `subject`: Patient reference 或 display id。Phase 9A 只定義 reference，不建立 Patient。
- `event`: event time、Encounter reference 與 event type。
- `trust`: data origin 與 review status。
- `payload`: observations、conditions、diagnosticReports、documents、consents 等候選資料。
- `provenance`: safe source references 與最小化 notes。

## Source Marking

來源標記必須保存「誰產生資料」與「資料是否已審閱」兩個不同概念。`sourceSystem.systemType` 可為 `ai-agent`、`concordance-backend`、`mpsw`、`external-workflow`。`trust.dataOrigin` 可為 `ai-generated`、`ai-suggested`、`clinician-observed`、`clinician-reviewed`、`signed-off`。

AI output 不得直接轉成 signed-off。AI-generated data 不得當成 clinician-confirmed。AI Agent 不得直接寫入正式 FHIR Server。

## Review / Trust Status

`trust.reviewStatus` 應區分：

- `pending-review`: 候選資料等待醫護審核。
- `clinician-reviewed`: 已審閱，但不代表完成簽核。
- `signed-off`: 需要明確簽核證據，例如 reviewer、timestamp、簽核工作流 ID 或未來治理紀錄。
- `rejected`: 候選資料被拒絕，不得寫入正式臨床結果。
- `superseded`: 已被後續版本取代，只能作歷史或審計參考。

`DiagnosticReport.final` 不等於 signed-off，也不等於自動診斷。`Consent.active` 不代表醫療結果成立。

## FHIR Mapping Boundary

Phase 9A 只定義 candidate mapping。可映射欄位必須先通過 validation / review boundary，並保留來源與信任狀態。

- `subject.patientReference` 可作 Patient reference，不建立 Patient。
- `event.encounterReference` 可作 Encounter reference，不建立 Encounter。
- `payload.observations[]` 可作 Observation candidate；AI value 只可 pending，不可自動解讀。
- `payload.conditions[]` 可作 Condition candidate；不得直接標為 confirmed，需 `verificationStatus` 與醫護審核。
- `payload.diagnosticReports[]` 可作 DiagnosticReport candidate；不得呈現為自動診斷。
- `payload.documents[]` 可作 DocumentReference metadata candidate；不得暴露 binary content。
- `payload.consents[]` 可作 Consent candidate；只代表授權或簽核狀態，不代表醫療結果。
- `provenance` 未來可映射 Provenance / AuditEvent，但 Phase 9A 是 future draft only。

## Must Wait For Review

以下資料必須等待醫護審核，不得直接寫入正式 FHIR Server：

- AI-generated observation value、interpretation 或 abnormal flag。
- AI-suggested condition、diagnostic label 或 clinical summary。
- DiagnosticReport 結論文字或狀態升級。
- Consent 與簽核狀態的臨床解釋。
- DocumentReference 內容摘要、敏感 metadata、binary attachment 或下載授權。
- 任何可能形成 clinical advice、automatic diagnosis、treatment recommendation 的內容。

## Prohibited Direct Write

以下資料不得直接寫入正式 FHIR Server：

- `ai-generated` 或 `ai-suggested` 且未審核資料。
- review status 不明確或互相矛盾的資料。
- 缺少 `messageId`、`sourceSystem`、`subject`、`trust` 的資料。
- 無法確認 Patient / Encounter reference 的資料。
- binary content、token、secret、完整敏感 payload。
- 任何要求啟用 Gateway runtime、AI Agent runtime、FHIR write pipeline、CDS runtime 或 SMART production 的資料。

## Gateway Validation Boundary

Gateway validation boundary 是未來設計草案，不是 Phase 9A runtime validator。Phase 9A 不執行 live HAPI `$validate`，不執行 live FHIR write，不執行 runtime Gateway validation。

未來 boundary 應至少包含 envelope validation、required field validation、source system validation、trust / review status validation、FHIR reference validation、payload type validation、terminology validation draft、privacy / PHI minimization check、mapping candidate validation、human review boundary 與 future FHIR `$validate` boundary。

## Deferred Runtime Plan

Gateway runtime、AI Agent connector、queue worker、webhook receiver、background job、FHIR writer、production gateway、clinical decision engine、SMART production activation 與 CDS runtime activation 全部 deferred。Phase 9A 不新增 endpoint、不新增 route、不新增 FHIR write path、不修改 FHIR server runtime。
