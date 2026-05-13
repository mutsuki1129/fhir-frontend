# Gateway Ingestion Governance Policy Draft

## Purpose

本文件定義未來 Gateway ingestion 的治理條件。Phase 9C 不新增 Gateway ingestion runtime。Phase 9C 不新增 POST /gateway。Phase 9C 不新增 webhook receiver。Phase 9C 不新增 queue worker。

## Future Ingestion Readiness Requirements

1. adapter contract approved。
2. payload schema reviewed。
3. validation workflow reviewed。
4. error handling reviewed。
5. manual review gate defined。
6. FHIR mapping reviewed。
7. audit / provenance plan defined。
8. PHI minimization reviewed。
9. dev-only prototype approved。
10. production write disabled by default。

## Safety Boundary

Gateway ingestion 不得直接污染正式 FHIR Server。AI Agent 不得直接寫入正式 FHIR Server。所有 candidate data 必須經 validation / review boundary。

Phase 9C 不新增 production gateway、不新增 Gateway runtime endpoint、不新增 AI Agent runtime endpoint、不新增 ingestion endpoint、不新增 validation endpoint、不新增 FHIR write pipeline。
