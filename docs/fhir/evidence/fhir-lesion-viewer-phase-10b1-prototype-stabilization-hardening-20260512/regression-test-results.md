# Regression Test Results

Phase 10B.1 regression checks completed.

## PHP Syntax Checks

All requested PHP lint checks passed:

- `php -l app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `php -l app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `php -l app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `php -l config/fhir.php`
- `php -l routes/web.php`
- `php -l routes/api.php`
- `php -l resources/views/dev/fhir/mock-ingestion/index.blade.php`

## Phase 10B.1 Hardening Tests

- `php artisan test --filter=ControlledIngestionPrototypeHardeningTest`: PASS, 5 tests, 57 assertions

## Phase 10B Prototype Tests

- `php artisan test --filter=ControlledIngestionPrototypeFeatureFlagTest`: PASS, 5 tests, 13 assertions
- `php artisan test --filter=ControlledIngestionPrototypePreviewTest`: PASS, 3 tests, 41 assertions
- `php artisan test --filter=ControlledIngestionPrototypeNoWriteTest`: PASS, 2 tests, 27 assertions
- `php artisan test --filter=ControlledIngestionPrototypeUiTest`: PASS, 1 test, 19 assertions
- `php artisan test --filter=ControlledIngestionPrototypeDocumentationTest`: PASS, 2 tests, 33 assertions

## Runtime Safety Tests

- `php artisan test --filter=ControlledIngestionRuntimeSafetyTest`: PASS, 5 tests, 46 assertions
- `php artisan test --filter=GovernanceRuntimeSafetyTest`: PASS, 4 tests, 40 assertions
- `php artisan test --filter=FhirValidationRuntimeSafetyTest`: PASS, 4 tests, 32 assertions
- `php artisan test --filter=GatewayRuntimeSafetyTest`: PASS, 4 tests, 29 assertions
- `php artisan test --filter=FrontendReadOnlyRouteGuardTest`: PASS, 13 tests, 88 assertions

## Lesion Viewer Regression Tests

- `php artisan test --filter=LesionViewerApiTest`: PASS, 3 tests, 73 assertions
- `php artisan test --filter=LesionViewerUiTest`: PASS, 4 tests, 73 assertions
- `php artisan test --filter=LesionViewerDocumentationTest`: PASS, 3 tests, 68 assertions

## Deprecation Warning Note

The test runner emitted PHP 8.5 / Pest / Collision deprecation warnings, including `ReflectionMethod::setAccessible()` and `ReflectionProperty::setAccessible()` deprecation notices. The warnings did not fail the test runs; all targeted tests passed.

## Result

Regression result: PASS.
