# Phase 3 i18n Key Guideline (Frontend)

## Scope

This guideline defines the minimal key naming for Phase 3 M2.

## Naming Rules

- Use namespace prefix: `ui.`
- Use page or module scope: `nav`, `patients`, `doctors`, `rekam`
- Use stable semantic key names, not visual wording
- Keep values in `lang/en/ui.php` and `lang/zh_TW/ui.php` synchronized

## Current Key Groups

- `ui.nav.*`: top navigation, profile, logout, language
- `ui.lang.*`: locale names
- `ui.patients.*`: patient page title and search/add controls
- `ui.doctors.*`: doctor page title and search/add controls
- `ui.rekam.*`: medical record page title

## Extension Pattern

When adding a new page:

1. Add keys under `ui.<page>.*`
2. Use `__('ui.<page>.<key>')` directly in blade
3. Keep existing fallback behavior unchanged (Laravel fallback locale)
