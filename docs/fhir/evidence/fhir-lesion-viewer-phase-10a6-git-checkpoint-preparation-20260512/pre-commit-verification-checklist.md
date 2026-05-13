# Pre-commit Verification Checklist

- [ ] git status reviewed
- [ ] unrelated files identified
- [ ] no `git add .`
- [ ] no `git add -A`
- [ ] staged files reviewed with `git diff --cached --name-only`
- [ ] staged diff reviewed with `git diff --cached --stat`
- [ ] route safety verified
- [ ] runtime safety verified
- [ ] regression tests passed
- [ ] documentation validation passed
- [ ] no lesion write route
- [ ] no FHIR write route
- [ ] no Gateway runtime
- [ ] no AI Agent runtime
- [ ] no ingestion runtime
- [ ] no real PHI
- [ ] no production activation
