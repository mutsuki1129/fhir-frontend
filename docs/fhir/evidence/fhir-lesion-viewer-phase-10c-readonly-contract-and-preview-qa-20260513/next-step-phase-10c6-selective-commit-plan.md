# Next Step: Phase 10C.6 Selective Commit Plan

Phase 10C.6 should be a selective commit preparation phase only, unless separately authorized to commit.

## Recommended Scope

Review and selectively stage only Phase 10C documentation artifacts:

- `docs/fhir/lesion-viewer-data-contract.md`
- `docs/fhir/fhir-docs-index.md`
- `docs/README.md`
- `docs/fhir/evidence/fhir-lesion-viewer-phase-10c-readonly-contract-and-preview-qa-20260513/`

## Do Not Stage

Do not stage unrelated dirty files, including:

- HAPI backend config.
- Docker or env files.
- SMART production config.
- CDS runtime config.
- Gateway runtime config.
- Patient, Observation, Rekam, or Practitioner CRUD files.
- DocumentReference/Binary upload/delete/purge files.
- `FhirApiClient::expungeDeletedResource` changes.
- Any unrelated deleted or untracked files.

## Suggested Verification Before Any Commit

Run:

```powershell
git status --short
git diff -- docs/fhir/lesion-viewer-data-contract.md docs/fhir/fhir-docs-index.md docs/README.md docs/fhir/evidence/fhir-lesion-viewer-phase-10c-readonly-contract-and-preview-qa-20260513
git diff --cached --name-only
```

Only after explicit authorization, use narrow path-based staging for the Phase 10C files. Do not use broad staging.

## Suggested Commit Message

```text
docs(fhir): add phase 10c readonly lesion viewer qa evidence
```
