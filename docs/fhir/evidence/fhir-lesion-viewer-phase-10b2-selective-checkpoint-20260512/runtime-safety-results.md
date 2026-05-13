# Runtime Safety Results

Runtime safety tests executed and passed:

- `ControlledIngestionPrototypeHardeningTest`: passed, 5 tests, 57 assertions
- `ControlledIngestionPrototypeFeatureFlagTest`: passed, 5 tests, 13 assertions
- `ControlledIngestionPrototypePreviewTest`: passed, 3 tests, 41 assertions
- `ControlledIngestionPrototypeNoWriteTest`: passed, 2 tests, 27 assertions
- `ControlledIngestionPrototypeUiTest`: passed, 1 test, 19 assertions
- `ControlledIngestionPrototypeDocumentationTest`: passed, 2 tests, 33 assertions
- `ControlledIngestionRuntimeSafetyTest`: passed, 5 tests, 46 assertions
- `GovernanceRuntimeSafetyTest`: passed, 4 tests, 40 assertions
- `FhirValidationRuntimeSafetyTest`: passed, 4 tests, 32 assertions
- `GatewayRuntimeSafetyTest`: passed, 4 tests, 29 assertions
- `FrontendReadOnlyRouteGuardTest`: passed, 13 tests, 88 assertions

Confirmed:

- no FHIR write
- no AI Agent runtime
- no CDS runtime activation
- no SMART production activation
- no production ingestion
- feature flag default disabled
- Lesion Viewer remains read-only

PHP 8.5 / Pest note:

- All listed tests emitted deprecation warnings from Pest / Reflection APIs on PHP 8.5.
- Warnings did not fail the tests.
