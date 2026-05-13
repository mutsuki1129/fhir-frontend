# Controlled Ingestion Test Data Policy

本文件定義未來 prototype 的 test data policy。

## Allowed Test Data

- synthetic data
- fake patient references
- fake observation values
- fake report titles
- fake consent references
- fake document metadata
- local mock payload files

## Forbidden Test Data

- real patient data
- real clinical records
- real binary files
- real AI production output
- real consent documents
- real hospital identifiers
- unmasked PHI

測試資料不得用來做醫療判斷。測試資料不得對外展示為真實案例。測試資料不得寫入 production FHIR Server。

