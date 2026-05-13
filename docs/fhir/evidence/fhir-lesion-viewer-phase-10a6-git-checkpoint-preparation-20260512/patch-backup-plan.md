# Patch / Backup Plan

本文件提供 patch / backup 建議，但 Phase 10A.6 不執行 patch 產生。

## Full Diff Backup

```bash
git diff > fhir-readonly-lesion-viewer-phase-1-to-10a-full-diff.patch
```

注意：full diff 可能包含 unrelated changes。產生前需確認範圍。

## Staged Diff Backup

需在人工 selective staging 後執行：

```bash
git diff --cached > fhir-readonly-lesion-viewer-phase-1-to-10a-staged.patch
```

## Untracked List Backup

```bash
git ls-files --others --exclude-standard > untracked-files-before-checkpoint.txt
```

## Suggested Review Commands

```bash
git status --short
git diff --stat
git diff --name-only
git diff --cached --name-only
git diff --cached --stat
```

Patch 可能包含 unrelated changes，產生前需確認範圍。不要自動產生 patch，除非使用者明確授權。
