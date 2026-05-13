# Working Tree Snapshot

## Commands Run

```bash
git rev-parse --show-toplevel
git branch --show-current
git status --short
git diff --stat
git diff --name-only
git ls-files --others --exclude-standard
git status --porcelain=v1
git diff --cached --stat
```

## Start State

- Repo root: `C:/Users/clamp/Desktop/project/fhir`
- Starting branch: `main`
- Cached diff at start: empty
- Unmerged paths: none detected
- Working tree: dirty, with tracked modified files, tracked deleted files, and many untracked files

## Tracked Deleted Files Present At Start

- `BACKEND_GAPS_FOR_PHASE1.md`
- `DOCKER.md`
- `FRONTEND_FHIR_USAGE.md`
- `FRONTEND_PHASE1_PLAN.md`
- `INTEGRATION_TASKS_PHASE1.md`
- `PHASE2_CONDITION_PLAN.md`
- `PHASE2_TASKS.md`
- `SERVER_CAPABILITY.md`

These deletions were not staged.

## Important Dirty/Untracked Boundary

The working tree included unrelated and unknown files. Only dry-run-listed exact paths were staged for Commit 1 through Commit 5. Unrelated dirty / untracked files were not cleaned.
