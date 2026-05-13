# Regression Test Results

執行時間：2026-05-13 Asia/Taipei。

## PHP Syntax Checks

以下 `php -l` checks 均通過：

- `app/Services/Fhir/LesionViewer/FhirBackedLesionRepository.php`
- `app/Services/Fhir/LesionViewer/MockLesionRepository.php`
- `app/Services/Fhir/LesionViewer/LesionRepository.php`
- `app/Http/Controllers/LesionViewerController.php`
- `app/Http/Middleware/EnsureFhirFrontendReadOnly.php`
- `config/fhir.php`
- `routes/web.php`
- `routes/api.php`
- `resources/views/admin/lesions/index.blade.php`
- `resources/views/admin/lesions/show.blade.php`

## Laravel / Pest Filters

以下 filters 皆已執行，整批命令 exit code 為 0：

- `ControlledIngestionPlanningDocumentationTest`
- `ControlledIngestionRuntimeSafetyTest`
- `GovernanceReviewDocumentationTest`
- `GovernanceRuntimeSafetyTest`
- `FhirValidationWorkflowDocumentationTest`
- `FhirValidationRuntimeSafetyTest`
- `GatewayAiAgentContractDocumentationTest`
- `GatewayRuntimeSafetyTest`
- `FhirBackedLesionLinkingTest`
- `FhirBackedLesionEnrichmentTest`
- `FhirBackedLesionAggregationTest`
- `FhirBackedLesionRepositoryTest`
- `LesionRepositoryBindingTest`
- `LesionViewerSourceSwitchTest`
- `LesionViewerApiTest`
- `LesionViewerRouteTest`
- `LesionViewerUiTest`
- `LesionViewerDocumentationTest`
- `FrontendReadOnlyRouteGuardTest`
- `FrontendReadOnlyUiTest`

PHP 8.5 / Pest deprecation warnings appeared for `ReflectionMethod::setAccessible()`, but tests passed. This warning is not caused by Phase 10A.6.

## Result

Additional Phase 10A.6 documentation checks:

- `php -l tests/Feature/Fhir/FhirGitCheckpointPreparationDocumentationTest.php` -> passed.
- `php artisan test --filter=FhirGitCheckpointPreparationDocumentationTest` -> passed, 4 tests / 51 assertions.
- `php artisan test --filter=FhirDocumentationIndexTest` -> passed, 803 tests / 15055 assertions.

No Phase 10A.6 regression failure was observed.
