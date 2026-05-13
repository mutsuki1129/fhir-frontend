# Dev-only Mock Ingestion Scope

未來 Phase 10B 若被明確授權，scope 只能是 dev environment only、mock payload only、test data only、no real PHI、no production FHIR Server、no live AI Agent、no production Gateway、no direct FHIR write、no CDS runtime、no SMART production activation、no clinical advice、no automatic diagnosis、no treatment recommendation。

## In Scope

- Dev-only prototype surface.
- Mock-only payload and parser behavior.
- Synthetic test data only.
- Mock validation result display.
- Mock manual review queue display.
- Candidate JSON preview only.
- Feature flag default disabled, if a future Phase 10B implementation is separately approved.

## Out of Scope

- Real AI Agent connector.
- Production ingestion.
- FHIR create/update/delete.
- DocumentReference binary upload.
- Real patient data.
- Clinical decision support.
- Automatic sign-off.
- Automatic validation-to-write.
- Background job writer.
- Queue-based writer.

Phase 10A does not implement runtime and does not approve Phase 10B.

