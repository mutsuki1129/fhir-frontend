# Phase 10M-C Visual QA Evidence

Date: 2026-05-14

Scope: FHIR Read-only Lesion / Clinical Evidence Viewer visual QA evidence only.

This package captures desktop and mobile screenshots for the reviewer-facing demo path. It does not enable write flows, formal ingestion, FHIR persistence, approval/signoff persistence, production SMART activation, CDS runtime activation, Gateway runtime activation, or HAPI server mutation.

## Pages Captured

Base URL: `http://localhost:8080`

Browser: installed Chrome through Python Playwright channel `chrome`.

Authentication: existing documented demo account was used. Password is not recorded in this package.

| Viewport | Page | Path | HTTP | Screenshot |
| --- | --- | --- | --- | --- |
| Desktop | Home | `/` | 200 | `screenshots/desktop-home.png` |
| Desktop | Login | `/login` | 200 | `screenshots/desktop-login.png` |
| Desktop | Lesion list | `/lesions` | 200 | `screenshots/desktop-lesions-index.png` |
| Desktop | Lesion detail | `/lesions/lesion-001` | 200 | `screenshots/desktop-lesion-detail.png` |
| Desktop | FHIR metadata | `/fhir` | 200 | `screenshots/desktop-fhir-metadata.png` |
| Mobile | Home | `/` | 200 | `screenshots/mobile-home.png` |
| Mobile | Login | `/login` | 200 | `screenshots/mobile-login.png` |
| Mobile | Lesion list | `/lesions` | 200 | `screenshots/mobile-lesions-index.png` |
| Mobile | Lesion detail | `/lesions/lesion-001` | 200 | `screenshots/mobile-lesion-detail.png` |
| Mobile | FHIR metadata | `/fhir` | 200 | `screenshots/mobile-fhir-metadata.png` |

## Automated Visual QA Summary

Source results: `visual-qa-results.json`

| Check | Result |
| --- | --- |
| Captures completed | 10 |
| HTTP status | all 200 |
| Console errors | 0 |
| Page errors | 0 |
| Translation key leaks | none |
| Marketplace wording | none |
| Developer-heavy first-screen markers | none |
| Write action labels | none |

The screenshot files are non-empty and have expected desktop/mobile dimensions. The visual capture includes `/`, `/login`, `/lesions`, `/lesions/lesion-001`, and `/fhir`.

## Route Safety

Checked during this phase:

```text
php artisan route:list --path=lesions
php artisan route:list --path=mock-ingestion
```

Lesion routes remain display-only:

```text
GET|HEAD api/lesions
GET|HEAD api/lesions/{lesion}
GET|HEAD lesions
GET|HEAD lesions/{lesion}
```

Mock ingestion remains dev-only preview-scoped:

```text
GET|HEAD dev/fhir/mock-ingestion
POST     dev/fhir/mock-ingestion/preview
```

No `POST /lesions`, `PUT /lesions`, `PATCH /lesions`, `DELETE /lesions`, lesion create/store/edit/update/destroy routes, formal ingestion, FHIR persistence, approval persistence, or signoff persistence were introduced.

## Commands Run

```text
git status --short
git status --branch --short
git diff --cached --stat
git log -1 --oneline
git branch -vv
php artisan route:list --path=lesions
php artisan route:list --path=mock-ingestion
npx playwright --version
python docs\fhir\evidence\fhir-lesion-viewer-phase-10m-c-visual-qa-20260514\capture_visual_qa.py
php artisan test --filter LesionViewerUiTest
```

Notes:

- `npx playwright --version` was blocked by npm cache mode (`ENOTCACHED`), so the package used Python Playwright with installed Chrome.
- Python Playwright first failed in the sandbox while launching a subprocess; the capture was rerun with approved escalation.
- The Python Playwright bundled browser was missing, so the capture script now supports `FHIR_PLAYWRIGHT_CHANNEL=chrome`.

## Files In This Package

```text
README.md
capture_visual_qa.py
visual-qa-results.json
screenshots/desktop-home.png
screenshots/desktop-login.png
screenshots/desktop-lesions-index.png
screenshots/desktop-lesion-detail.png
screenshots/desktop-fhir-metadata.png
screenshots/mobile-home.png
screenshots/mobile-login.png
screenshots/mobile-lesions-index.png
screenshots/mobile-lesion-detail.png
screenshots/mobile-fhir-metadata.png
```

## Boundary Confirmation

This phase is evidence packaging and visual QA. It does not modify routes, controllers, services, migrations, Docker configuration, HAPI configuration, SMART runtime activation, CDS runtime activation, Gateway runtime activation, or FHIR write behavior.
