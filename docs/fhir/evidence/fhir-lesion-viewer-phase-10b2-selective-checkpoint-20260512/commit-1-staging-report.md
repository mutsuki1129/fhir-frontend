# Commit 1 Staging Report

Commit message:

```text
feat(fhir): add dev-only mock ingestion prototype
```

Planned staged files:

- `config/fhir.php`
- `app/Http/Kernel.php` partial staged hunk only for `fhir.controlled_ingestion_prototype`
- `routes/web.php`
- `app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `resources/views/dev/fhir/mock-ingestion/index.blade.php`
- `resources/fhir/mock-ingestion/sample-gateway-payload.json`

Pre-commit checks:

- no docs evidence staged in commit 1
- no tests staged in commit 1
- no unrelated deleted files staged
- no FHIR writer staged
- no production Gateway / ingestion / AI Agent / CDS runtime staged

Actual staged files confirmed before commit:

- `app/Http/Controllers/Dev/FhirMockIngestionController.php`
- `app/Http/Kernel.php`
- `app/Http/Middleware/EnsureControlledIngestionPrototypeEnabled.php`
- `app/Services/Fhir/ControlledIngestion/MockIngestionPreviewService.php`
- `config/fhir.php`
- `resources/fhir/mock-ingestion/sample-gateway-payload.json`
- `resources/views/dev/fhir/mock-ingestion/index.blade.php`
- `routes/web.php`

Actual cached stat:

```text
8 files changed, 510 insertions(+)
```

Commit created:

```text
38847cb feat(fhir): add dev-only mock ingestion prototype
```
