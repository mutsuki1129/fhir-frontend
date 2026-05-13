# Test Results

## Phase 10C.2 Targeted API Contract Tests

Command:

```powershell
php artisan test tests\Feature\Fhir\LesionViewerApiTest.php tests\Feature\Fhir\FhirBackedLesionRepositoryTest.php tests\Feature\Fhir\FhirBackedLesionAggregationTest.php
```

Result:

- 11 passed.
- 151 assertions.
- PHP 8.5 warnings were vendor deprecation warnings only.

## Phase 10C.3 Controlled Ingestion Prototype Tests

Command:

```powershell
php artisan test tests\Feature\Fhir\ControlledIngestionPrototypePreviewTest.php tests\Feature\Fhir\ControlledIngestionPrototypeNoWriteTest.php tests\Feature\Fhir\ControlledIngestionPrototypeFeatureFlagTest.php tests\Feature\Fhir\ControlledIngestionPrototypeHardeningTest.php tests\Feature\Fhir\ControlledIngestionPrototypeUiTest.php
```

Result:

- 16 passed.
- 157 assertions.
- PHP 8.5 warnings were vendor deprecation warnings only.

## Phase 10C.4 Encoding / Contract Regression Tests

Command:

```powershell
php artisan test tests\Feature\Fhir\ControlledIngestionPrototypePreviewTest.php tests\Feature\Fhir\ControlledIngestionPrototypeNoWriteTest.php tests\Feature\Fhir\ControlledIngestionPrototypeFeatureFlagTest.php tests\Feature\Fhir\ControlledIngestionPrototypeHardeningTest.php tests\Feature\Fhir\ControlledIngestionPrototypeUiTest.php tests\Feature\Fhir\LesionViewerApiTest.php tests\Feature\Fhir\FhirBackedLesionRepositoryTest.php tests\Feature\Fhir\FhirBackedLesionAggregationTest.php
```

Result:

- 27 passed.
- 308 assertions.
- PHP 8.5 warnings were vendor deprecation warnings only.

## Phase 10C.5 Documentation Index Tests

Command:

```powershell
php artisan test --filter FhirDocumentationIndexTest
```

Result:

- 803 passed.
- 15055 assertions.
- PHP 8.5 warnings were vendor deprecation warnings only.

Command:

```powershell
php artisan test --filter LesionViewerDocumentationTest
```

Result:

- 3 passed.
- 71 assertions.
- Phase 10C.5.1 aligned the stale documentation-test expectation to the v3-only contract.
- The test now confirms `docs/fhir/lesion-viewer-data-contract.md` uses `read-only-aggregation-v3` and does not contain `read-only-aggregation-v1` or `read-only-aggregation-v2`.
- No runtime failure was observed in this test run.
- PHP 8.5 warnings were vendor deprecation warnings only.

Command:

```powershell
php artisan test --filter ControlledIngestionPrototypeDocumentationTest
```

Result:

- 2 passed.
- 33 assertions.
- PHP 8.5 warnings were vendor deprecation warnings only.
