# Excluded Files Report

以下類型不納入 Phase 10B.2 commits：

- unrelated dirty files
- unknown files
- legacy deleted docs
- root legacy doc deletions
- `.e2e_*` files
- environment backup files
- logo / favicon files
- unknown broad FHIR / CDS / storage / terminology / database files
- non-Phase 10B / 10B.1 controller, model, migration, seeder, view, language, config, frontend asset changes

明確排除範例：

- `.env.docker`
- `.env.backup-20260512-101537`
- `.env.smart-manual-qa.bak`
- `.e2e_*`
- `BACKEND_GAPS_FOR_PHASE1.md`
- `DOCKER.md`
- `FRONTEND_FHIR_USAGE.md`
- `FRONTEND_PHASE1_PLAN.md`
- `INTEGRATION_TASKS_PHASE1.md`
- `PHASE2_CONDITION_PLAN.md`
- `PHASE2_TASKS.md`
- `SERVER_CAPABILITY.md`
- unrelated `app/Http/Controllers/*`
- unrelated `app/Models/*`
- unrelated `app/Services/Fhir/Cds/*`
- unrelated `app/Services/Fhir/Storage/*`
- unrelated `app/Services/Fhir/Terminology/*`
- unrelated `database/migrations/*`
- unrelated `database/seeders/*`
- unrelated `resources/views/admin/*`
- unrelated `resources/views/fhir/*`
- unrelated `public/logo*.png`
- unrelated `public/favicon.ico`

Policy:

- excluded files will not be staged.
- unknown files will not be staged.
- files requiring human review outside the Phase 10B / 10B.1 scope will not be automatically staged.
