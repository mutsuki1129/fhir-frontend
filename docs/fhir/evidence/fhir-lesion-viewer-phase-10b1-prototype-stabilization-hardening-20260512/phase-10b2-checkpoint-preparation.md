# Phase 10B.2 Checkpoint Preparation

Phase 10B.2 建議只做 selective commit / checkpoint for Phase 10B + Phase 10B.1。

Phase 10B.2 不新增 runtime、不進 Phase 10C、不新增 FHIR write route、不清理 unrelated dirty / untracked files。

## Required constraints

- selective staging only
- 禁止 `git add .`
- 禁止 `git add -A`
- 禁止 `git reset`
- 禁止 `git clean`
- 禁止清理 unrelated dirty files
- 禁止自動 commit，除非使用者另行授權

## Suggested commit split

- `feat(fhir): add dev-only mock ingestion prototype`
- `test(fhir): harden controlled ingestion prototype safety`
- `docs(fhir): add phase 10b prototype evidence`

Phase 10B.2 的 checkpoint 應只包含 Phase 10B / 10B.1 相關檔案，並保留 unrelated dirty state。
