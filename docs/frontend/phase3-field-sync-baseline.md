# Phase 3 Field Sync Baseline (Frontend)

## 來源基準

- Backend baseline: `C:\Users\clamp\Desktop\project\fhir server\fhir-model\examples\patient-intake-bundle.json`
- 用途：作為前端欄位同步與 mapper 擴充的最低對齊參考，不代表一次性全量實作。

## 說明

- 本文件聚焦五個前端核心資源：`Patient` / `Practitioner` / `Observation` / `Condition` / `DocumentReference`。
- 若某資源欄位未出現在基準 bundle（例如 Condition、DocumentReference），以現行 Phase 2 契約欄位補齊，並註明為「擴充基線」。

## 1) Patient 映射

| FHIR 來源欄位 | ViewModel / 畫面欄位 | 現況 |
|---|---|---|
| `Patient.id` | `PatientVM.id` | 已映射 |
| `Patient.identifier[].value` | 病人識別（文件基線） | 部分映射（toFhir 有輸出，fromFhir 未完整暴露多識別） |
| `Patient.name[].text/family+given` | `PatientVM.name`、病人頁姓名 | 已映射 |
| `Patient.gender` | 病人頁性別（後續欄位） | 基線保留（目前 VM 未顯式欄位） |
| `Patient.birthDate` | 病人頁生日（後續欄位） | 基線保留（目前 VM 未顯式欄位） |
| `Patient.telecom[email/phone]` | `PatientVM.email` / `PatientVM.phone` | 已映射（phase2 現況） |
| `Patient.generalPractitioner[].reference` | 病人主責醫師（後續欄位） | 基線保留（待頁面擴充） |

## 2) Practitioner 映射

| FHIR 來源欄位 | ViewModel / 畫面欄位 | 現況 |
|---|---|---|
| `Practitioner.id` | `PractitionerVM.id` | 已映射 |
| `Practitioner.identifier[].value` | 醫師識別（後續欄位） | 基線保留 |
| `Practitioner.name[].text/family+given` | `PractitionerVM.name`、rekam performer 選單 | 已映射 |
| `Practitioner.telecom[email]` | `PractitionerVM.email` | 已映射（可能為空） |
| `Practitioner.telecom[phone]` | `PractitionerVM.phone` | 已映射（可能為空） |

## 3) Observation 映射

| FHIR 來源欄位 | ViewModel / 畫面欄位 | 現況 |
|---|---|---|
| `Observation.id` | `TemperatureObservationVM.id` | 已映射 |
| `Observation.subject.reference` | `TemperatureObservationVM.patientId` | 已映射 |
| `Observation.performer[0].reference` | `TemperatureObservationVM.performerId` | 已映射 |
| `Observation.effectiveDateTime` | `TemperatureObservationVM.effectiveDateTime` | 已映射 |
| `Observation.note[0].text` | `TemperatureObservationVM.note`（legacy note） | 已映射 |
| `Observation.valueQuantity.value` | `TemperatureObservationVM.valueCelsius` | 已映射（現行主流程） |
| `Observation.valueString` | intake 類行為欄位（後續） | 基線保留（目前 mapper 未納入） |
| `Observation.valueCodeableConcept` | intake 類枚舉欄位（後續） | 基線保留（目前 mapper 未納入） |
| `Observation.component[]` | intake 收支/複合欄位（後續） | 基線保留（目前 mapper 未納入） |

## 4) Condition 映射（擴充基線）

| FHIR 來源欄位 | ViewModel / 畫面欄位 | 現況 |
|---|---|---|
| `Condition.id` | `ConditionVM.id` | 已映射 |
| `Condition.subject.reference` | `ConditionVM.patientId` | 已映射 |
| `Condition.code.coding[0].code` | `ConditionVM.code`、condition code 欄位 | 已映射 |
| `Condition.code.text / coding.display` | `ConditionVM.text`、condition text 欄位 | 已映射 |
| `Condition.recordedDate` | `ConditionVM.recordedDate` | 已映射 |
| `Condition.note[0].text` | `ConditionVM.note` | 已映射（可用於 fallback） |

## 5) DocumentReference 映射（擴充基線）

| FHIR 來源欄位 | ViewModel / 畫面欄位 | 現況 |
|---|---|---|
| `DocumentReference.id` | `DocumentReferenceVM.id` | 已映射 |
| `DocumentReference.subject.reference` | `DocumentReferenceVM.patientId` | 已映射 |
| `DocumentReference.content[0].attachment.title` | `DocumentReferenceVM.title`、附件標題欄位 | 已映射 |
| `DocumentReference.content[0].attachment.url` | `DocumentReferenceVM.url`、附件 URL 欄位 | 已映射 |
| `DocumentReference.content[0].attachment.contentType` | `DocumentReferenceVM.contentType` | 已映射 |
| `DocumentReference.date/meta.lastUpdated` | `DocumentReferenceVM.date` | 已映射 |

## 基線約束與後續開發提醒

1. 目前病例頁 observation mapper 是「體溫流程優先」子集，不等同 patient-intake 所有 observation 類型。
2. Patient/Practitioner 的識別欄位（identifier）仍以頁面最小可用為主，Phase 3 後續可擴充到完整欄位顯示與編輯。
3. Condition / DocumentReference 已有可用 baseline，但與 observation 的精準關聯仍依後端契約收斂後再強化。
