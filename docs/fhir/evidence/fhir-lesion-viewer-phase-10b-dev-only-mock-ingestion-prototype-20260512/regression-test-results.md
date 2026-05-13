# Regression Test Results

## 執行日期

2026-05-13

## PHP syntax checks

以下檢查均通過，回傳 `No syntax errors detected`：

- `php -l app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `php -l app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `php -l app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `php -l config/fhir.php`
- `php -l routes/web.php`
- `php -l routes/api.php`

## Phase 10B targeted tests

- `php artisan test --filter=ControlledIngestionPrototypeFeatureFlagTest`：PASS，5 tests，13 assertions。
- `php artisan test --filter=ControlledIngestionPrototypePreviewTest`：PASS，3 tests，41 assertions。
- `php artisan test --filter=ControlledIngestionPrototypeNoWriteTest`：PASS，2 tests，27 assertions。
- `php artisan test --filter=ControlledIngestionPrototypeUiTest`：PASS，1 test，19 assertions。
- `php artisan test --filter=ControlledIngestionPrototypeDocumentationTest`：PASS，2 tests，33 assertions。

## Runtime safety regression

- `php artisan test --filter=ControlledIngestionRuntimeSafetyTest`：PASS，5 tests，46 assertions。
- `php artisan test --filter=GovernanceRuntimeSafetyTest`：PASS，4 tests，40 assertions。
- `php artisan test --filter=FhirValidationRuntimeSafetyTest`：PASS，4 tests，32 assertions。
- `php artisan test --filter=GatewayRuntimeSafetyTest`：PASS，4 tests，29 assertions。
- `php artisan test --filter=FrontendReadOnlyRouteGuardTest`：PASS，13 tests，88 assertions。

## Lesion Viewer regression

- `php artisan test --filter=LesionViewerApiTest`：PASS，3 tests，73 assertions。
- `php artisan test --filter=LesionViewerUiTest`：PASS，4 tests，73 assertions。
- `php artisan test --filter=LesionViewerDocumentationTest`：PASS，3 tests，68 assertions。

## Warning note

測試執行期間出現 PHP 8.5 / Pest / Collision deprecation warnings，例如：

- `ReflectionMethod::setAccessible() is deprecated since 8.5`
- `ReflectionProperty::setAccessible() is deprecated since 8.5`

這些 warning 未造成上述測試失敗。
