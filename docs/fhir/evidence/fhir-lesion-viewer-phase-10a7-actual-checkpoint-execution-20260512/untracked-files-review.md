# Untracked Files Review

## Decision

Only dry-run-listed untracked files were staged for Commit 1 through Commit 5.

Untracked files outside the dry-run selective staging plan were not staged, deleted, or cleaned.

## Examples Left Unstaged

- `.e2e_*`
- `.env.backup-*`
- broad `app/Console/Commands/`
- broad `app/Services/Fhir/Cds/`
- broad `app/Services/Fhir/Storage/`
- broad `app/Services/Fhir/Terminology/`
- broad `database/**`
- broad `tests/Unit/Fhir/`
