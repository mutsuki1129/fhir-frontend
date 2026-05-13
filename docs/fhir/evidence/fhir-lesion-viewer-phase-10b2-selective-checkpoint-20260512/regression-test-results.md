# Regression Test Results

## Syntax Checks

All passed:

- `php -l app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `php -l app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `php -l app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `php -l config/fhir.php`
- `php -l routes/web.php`
- `php -l routes/api.php`
- `php -l resources/views/dev/fhir/mock-ingestion/index.blade.php`

## Targeted Tests

All passed:

- `ControlledIngestionPrototypeHardeningTest`
- `ControlledIngestionPrototypeFeatureFlagTest`
- `ControlledIngestionPrototypePreviewTest`
- `ControlledIngestionPrototypeNoWriteTest`
- `ControlledIngestionPrototypeUiTest`
- `ControlledIngestionPrototypeDocumentationTest`
- `ControlledIngestionRuntimeSafetyTest`
- `GovernanceRuntimeSafetyTest`
- `FhirValidationRuntimeSafetyTest`
- `GatewayRuntimeSafetyTest`
- `FrontendReadOnlyRouteGuardTest`
- `LesionViewerApiTest`: passed, 3 tests, 73 assertions
- `LesionViewerUiTest`: passed, 4 tests, 73 assertions
- `LesionViewerDocumentationTest`: passed, 3 tests, 68 assertions

PHP 8.5 / Pest note:

- Tests emitted PHP 8.5 / Pest deprecation warnings around `ReflectionMethod::setAccessible()` and `ReflectionProperty::setAccessible()`.
- All tests passed despite warnings.
