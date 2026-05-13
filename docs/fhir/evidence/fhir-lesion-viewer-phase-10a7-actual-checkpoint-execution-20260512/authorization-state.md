# Authorization State

## Input Flags

```text
EXECUTE_CHECKPOINT=true
ALLOW_BRANCH_CREATE=true
ALLOW_GIT_ADD=true
ALLOW_GIT_COMMIT=true
ALLOW_PATCH_EXPORT=false
```

## Effective Decision

Authorized:

- checkpoint branch creation
- exact-path selective `git add`
- split commits

Not authorized:

- patch export
- `git push`
- `git reset`
- `git clean`
- `git add .`
- `git add -A`
- wildcard staging
- deleting untracked files
- cleaning unrelated dirty / untracked changes
- Phase 10B work
