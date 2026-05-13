# Working Tree Snapshot

執行時間：2026-05-13，Phase 10B.2 開始前。

## Commands

```text
git branch --show-current
git status --short
git diff --stat
git diff --name-only
git diff --cached --stat
git status --porcelain=v1
```

## Summary

- Current branch: `checkpoint/fhir-readonly-lesion-viewer-phase-1-to-10a`
- Cached diff at start: empty
- Unmerged paths: none observed
- Working tree: dirty before checkpoint execution
- `git diff --stat`: `78 files changed, 4032 insertions(+), 2745 deletions(-)`

## Phase 10B / 10B.1 Relevant Dirty Files

- `config/fhir.php`
- `routes/web.php`
- `app/Http/Kernel.php`，只允許 stage controlled ingestion middleware alias；其他 alias 不納入本 checkpoint。
- `app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `resources/views/dev/fhir/mock-ingestion/index.blade.php`
- `resources/fhir/mock-ingestion/sample-gateway-payload.json`
- `tests/Feature/Fhir/ControlledIngestionPrototype*.php`
- Phase 10B / 10B.1 docs and evidence packages

## Unrelated Dirty / Untracked State

開始前已存在大量 unrelated dirty / untracked files，包含 legacy root doc deletions、`.e2e_*` files、environment backups、FHIR CDS / SMART / terminology / storage / database related files、logo/favicon files、legacy UI/controller edits 等。

本階段不清理、不 reset、不 checkout、不 clean、不 stage unrelated files。
