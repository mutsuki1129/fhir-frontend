# Regression Test Results

## PHP Syntax Checks

All requested `php -l` checks passed for:

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

## Targeted Laravel Tests

All requested filtered tests were executed and returned exit code 0.

PHP 8.5 / Pest deprecation warnings appeared for `ReflectionMethod::setAccessible()`, but tests passed.
