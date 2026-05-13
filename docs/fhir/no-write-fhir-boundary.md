# No-write FHIR Boundary

未來 Phase 10B prototype 必須維持 no-write FHIR boundary。

## Prohibited Writes

No FHIR create.
No FHIR update.
No FHIR delete.
No FHIR patch.
No FHIR upload.
No DocumentReference binary write.
No Patient write.
No Observation write.
No Condition write.
No DiagnosticReport write.
No Consent write.
No Encounter write.

## Allowed Displays

- read-only preview
- mock candidate resource display
- mock validation result display
- manual review queue mock display
- candidate JSON preview

Candidate Resource Preview 不等於 FHIR Server persisted Resource。Manual Review Queue Mock 不等於 clinician sign-off。Validation Result Mock 不等於 FHIR write approval。

