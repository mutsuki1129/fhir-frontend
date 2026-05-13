# Working Tree Snapshot

執行時間：2026-05-13 Asia/Taipei。執行位置：`C:\Users\clamp\Desktop\project\fhir`。

本文件只記錄狀態。本階段沒有 reset、checkout、clean、刪除 untracked files 或清理 unrelated dirty files。

## Commands Executed

- `git status --short`
- `git diff --stat`
- `git diff --name-only`
- `git ls-files --others --exclude-standard`

## Snapshot Summary

- `git status --short` 顯示大量 tracked modified、tracked deleted 與 untracked files。
- `git diff --stat` 顯示 tracked diff 目前有 76 files changed，約 4507 insertions / 2738 deletions。
- `git diff --name-only` 顯示 tracked changed/deleted files 包含根層舊文件、app controllers/services/support/view models、config、docs/frontend、lang、resources views、routes、tests 等。
- `git ls-files --others --exclude-standard` 顯示大量 untracked files，包含 Phase 1～10A.5 FHIR docs/evidence/tests/code，也包含 `.e2e_*.html`、`.env.*.bak`、logo assets、SMART/CDS/terminology/document storage/governance files 等需要分類的項目。

## git status --short Highlights

Tracked modified examples:

- `.env.docker`
- `PHASE3_PLAN.md`
- `app/Http/Controllers/DokterController.php`
- `app/Http/Controllers/PasienController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/RekamController.php`
- `app/Http/Kernel.php`
- `app/Providers/AppServiceProvider.php`
- `app/Services/Fhir/FhirApiClient.php`
- `app/Services/Fhir/FhirApiException.php`
- `app/Support/Fhir/*Mapper.php`
- `config/services.php`
- `docs/README.md`
- `lang/en/ui.php`
- `lang/zh_TW/ui.php`
- `phpunit.xml`
- `resources/views/**`
- `routes/api.php`
- `routes/web.php`
- `tests/Feature/TestingRegisterTest.php`

Tracked deleted examples:

- `BACKEND_GAPS_FOR_PHASE1.md`
- `DOCKER.md`
- `FRONTEND_FHIR_USAGE.md`
- `FRONTEND_PHASE1_PLAN.md`
- `INTEGRATION_TASKS_PHASE1.md`
- `PHASE2_CONDITION_PLAN.md`
- `PHASE2_TASKS.md`
- `SERVER_CAPABILITY.md`

Untracked examples:

- `app/Http/Controllers/LesionViewerController.php`
- `app/Http/Middleware/EnsureFhirFrontendReadOnly.php`
- `app/Services/Fhir/LesionViewer/`
- `config/fhir.php`
- `docs/fhir/`
- `lang/en/fhir.php`
- `lang/zh_TW/fhir.php`
- `resources/views/admin/lesions/`
- `tests/Feature/Fhir/`
- `tests/Unit/Fhir/`
- `.e2e_*.html`
- `.env.backup-20260512-101537`
- `.env.smart-manual-qa.bak`

## Current Risk Note

Repo 已有大量 unrelated 或 unknown dirty / untracked changes。Phase 10A.6 只處理 checkpoint planning / evidence / documentation 範圍，不把 unknown / unrelated files 自動視為 FHIR 主線成果，也不清理它們。
