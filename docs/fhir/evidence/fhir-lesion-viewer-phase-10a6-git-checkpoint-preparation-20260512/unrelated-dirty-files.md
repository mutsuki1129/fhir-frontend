# Unrelated Dirty Files List

本文件只記錄，不清理。
本階段不會 reset、checkout、delete 或 git clean。

## Clearly Unrelated

- `.e2e_cookies.txt`
- `.e2e_dash.html`
- `.e2e_register.html`
- `.e2e_register_post.html`
- `.e2e_rekam_after_backend1.html`
- `.e2e_rekam_after_backend2.html`
- `.e2e_rekam_after_backend_create.html`
- `.e2e_rekam_create.html`
- `.e2e_rekam_create_post.html`
- `.e2e_rekam_edit.html`
- `.e2e_rekam_edit2.html`
- `.e2e_rekam_edit2_post.html`
- `.e2e_rekam_edit_post.html`
- `.env.backup-20260512-101537`
- `.env.smart-manual-qa.bak`
- `public/logo-icon.png`
- `public/logo.png`
- `public/favicon.ico`

## Possibly Related But Needs Human Review

- `.env.docker`
- `app/Http/Controllers/DokterController.php`
- `app/Http/Controllers/PasienController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/RekamController.php`
- `app/Models/User.php`
- `app/Providers/AppServiceProvider.php`
- `database/**`
- `resources/views/admin/diagnostic-reports/`
- `resources/views/admin/document-references/`
- `resources/views/admin/encounters/`
- `resources/views/admin/medication-requests/`
- `resources/views/fhir/`
- `scripts/fhir-ig-validation.ps1`
- `scripts/phase1-legacy-fallback-smoke.ps1`
- `tests/Unit/Fhir/`
- `tests/Feature/DokterFhirDeleteTest.php`
- `tests/Feature/PasienFhirDeleteTest.php`
- `tests/Feature/RekamFhirDeleteTest.php`
- `tests/Feature/SmartFhirTest.php`

## Deleted Files Needing Review

- `BACKEND_GAPS_FOR_PHASE1.md`
- `DOCKER.md`
- `FRONTEND_FHIR_USAGE.md`
- `FRONTEND_PHASE1_PLAN.md`
- `INTEGRATION_TASKS_PHASE1.md`
- `PHASE2_CONDITION_PLAN.md`
- `PHASE2_TASKS.md`
- `SERVER_CAPABILITY.md`

這些刪除可能代表舊資料搬移到 `docs/舊資料/`，也可能是不相關清理。Phase 10A.6 不應自動 stage deletion，需人工確認。

## Untracked Files Needing Review

- `app/Console/Commands/`
- `app/Http/Controllers/EncounterController.php`
- `app/Http/Controllers/FhirMetadataController.php`
- `app/Http/Controllers/FhirPortalController.php`
- `app/Http/Controllers/SmartFhirController.php`
- `app/Models/Fhir*`
- `app/Services/Fhir/Audit/`
- `app/Services/Fhir/Auth/`
- `app/Services/Fhir/Cds/`
- `app/Services/Fhir/SmartFhirClient.php`
- `app/Services/Fhir/SmartScopeSet.php`
- `app/Services/Fhir/Storage/`
- `app/Services/Fhir/Terminology/`
- `app/Services/Fhir/Validation/`
- `docs/smart-on-fhir-client.md`
- `docs/smart-on-fhir-production-guide.zh-TW.md`

## Unknown Origin

- `PHASE3_PLAN.md`
- `readme.md`
- `resources/js/app.js`
- `tailwind.config.js`
- `tests/Feature/TestingRegisterTest.php`

## Boundary

不要把 unrelated dirty / untracked files 混入 FHIR 主線 commit plan。任何 `unknown-needs-human-review` 項目都不應被 wildcard 或 `git add .` 順手 stage。
