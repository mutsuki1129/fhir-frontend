# Deferred Issues

Deferred by design:

- Phase 10C Controlled Write Path Review is not started.
- Production ingestion remains out of scope.
- FHIR write path remains disabled and unimplemented.
- Live HAPI `$validate` remains out of scope.
- AI Agent connector remains out of scope.
- CDS runtime remains out of scope.
- SMART production activation remains out of scope.
- Queue worker / webhook receiver / background job remain out of scope.

Repository state:

- Large unrelated dirty / untracked tree remains intentionally untouched.
- Legacy root doc deletions require separate human review.
- Broad CDS / SMART / terminology / storage / database files remain excluded from this checkpoint.

Post-commit evidence:

- `post-commit-verification-results.md` is expected to be produced after the three checkpoint commits and may remain uncommitted by design. No `commit --amend` will be used.
