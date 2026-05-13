# Commit Split Recommendation

本文件只提供 commit 拆分建議。Phase 10A.6 不執行 commit。

## Commit 1

```text
feat(fhir): add read-only lesion viewer baseline
```

範圍：

- read-only UI
- Lesion list/detail
- routes
- controller
- repository interface
- mock source
- read-only guard
- language/navigation wording

## Commit 2

```text
feat(fhir): add fhir-backed lesion aggregation
```

範圍：

- DiagnosticReport-centered aggregation
- Patient / Observation / Encounter enrichment
- Condition / DocumentReference / Consent linking
- FHIR-backed source switch

## Commit 3

```text
docs(fhir): add gateway validation governance drafts
```

範圍：

- Phase 9A Gateway / AI Agent Adapter Contract
- Phase 9B FHIR Validation Workflow
- Phase 9C Governance Review

## Commit 4

```text
docs(fhir): add controlled ingestion planning and checkpoint
```

範圍：

- Phase 10A Controlled Ingestion Prototype Planning
- Phase 10A.5 baseline checkpoint
- Phase 10A.6 git checkpoint preparation

## Commit 5

```text
test(fhir): add runtime safety and documentation coverage
```

範圍：

- `FrontendReadOnlyUiTest`
- `FrontendReadOnlyRouteGuardTest`
- Lesion Viewer tests
- Gateway runtime safety tests
- FHIR validation runtime safety tests
- Governance runtime safety tests
- Controlled ingestion runtime safety tests
- Documentation tests

## Reminder

實際 commit 前需人工確認 staging。
不要自動 commit。
不要自動 squash unrelated changes。
