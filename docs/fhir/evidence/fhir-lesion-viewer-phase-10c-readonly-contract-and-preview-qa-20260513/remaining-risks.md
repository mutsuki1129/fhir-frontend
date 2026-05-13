# Remaining Risks

## Dirty Working Tree

The working tree remains dirty. There are many unrelated modified, deleted, and untracked files. Phase 10C.5 did not clean, normalize, reset, delete, stage, or commit unrelated files.

## Untracked Contract File

`docs/fhir/lesion-viewer-data-contract.md` remains untracked unless separately staged later. It is part of the intended Phase 10C documentation-only contract alignment, but it still needs explicit selective staging authorization in a later phase.

## Existing Index Mojibake

Some existing docs index content contains historical mojibake outside the Phase 10C.5 change. Phase 10C.5 did not perform a broad index cleanup because the authorized scope was only to add the Phase 10C evidence package entry.

## Future Commit Risk

Because the working tree is dirty, Phase 10C.6 should use a selective commit plan and verify every staged path before commit. A broad `git add .` would risk staging unrelated changes.

## Documentation Test Drift

The Phase 10C.5 documentation-test drift was resolved in Phase 10C.5.1. `LesionViewerDocumentationTest` now passes against the v3-only contract and confirms that `docs/fhir/lesion-viewer-data-contract.md` does not contain the older aggregation markers.
