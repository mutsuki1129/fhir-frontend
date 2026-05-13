# Route Safety Results

執行時間：2026-05-13 Asia/Taipei。

## Commands

- `php artisan route:list --path=lesions`
- `php artisan route:list --path=api/lesions`
- `php artisan route:list --path=gateway`
- `php artisan route:list --path=agent`
- `php artisan route:list --path=ingestion`
- `php artisan route:list --path=validate`
- `php artisan route:list --path=validation`
- `php artisan route:list --path=smart`
- `php artisan route:list --path=cds`
- `php artisan route:list --path=hooks`
- `php artisan route:list --path=queue`

## Results

`lesions`:

- `GET|HEAD api/lesions`
- `GET|HEAD api/lesions/{lesion}`
- `GET|HEAD lesions`
- `GET|HEAD lesions/{lesion}`

`api/lesions`:

- `GET|HEAD api/lesions`
- `GET|HEAD api/lesions/{lesion}`

`gateway`:

- `GET|HEAD fhir/gateway`

這是 documentation / portal display route，不是 Gateway ingestion runtime route，不是 `POST /gateway` route，也不是 ingestion runtime route。

`agent`:

- No matching routes.

`ingestion`:

- No matching routes.

`validate`:

- No matching routes.

`validation`:

- No matching routes.

`smart`:

- `GET|HEAD .well-known/smart-configuration`
- `GET|HEAD fhir/smart`
- `GET|HEAD smart/callback`
- `GET|HEAD smart/launch`
- `POST smart/logout`
- `GET|HEAD smart/status`

這些是既有 SMART status / launch / callback / logout / configuration routes。Phase 10A.6 沒有新增 SMART production runtime route，也沒有 SMART production activation。

`cds`:

- `GET|HEAD fhir/cds/completeness`
- `GET|HEAD fhir/cds/completeness/audits`
- `GET|HEAD fhir/cds/completeness/audits/{audit}`
- `POST fhir/cds/completeness/audits/{audit}/ignore`
- `POST fhir/cds/completeness/audits/{audit}/review`
- `GET|HEAD fhir/cds/completeness/rules`
- `GET|HEAD fhir/cds/governance/approvals`
- `GET|HEAD fhir/cds/governance/events`
- `GET|HEAD fhir/cds/governance/execution-gate`
- `GET|HEAD fhir/cds/governance/permissions`
- `GET|HEAD fhir/cds/governance/runtime-actions`

這些是既有 CDS governance / completeness display and audit review routes，不是 CDS Hooks endpoint，不代表 CDS runtime activation。Phase 10A.6 沒有新增 CDS runtime route。

`hooks`:

- No matching routes.

`queue`:

- No matching routes.

## Confirmation

- `/lesions` 仍只有 `GET|HEAD`。
- `/api/lesions` 仍只有 `GET|HEAD`。
- 不存在 `POST` / `PATCH` / `DELETE` lesion route。
- 不存在 gateway / agent / ingestion / validation / queue runtime route。
- 不存在 Phase 10A.6 新增 SMART production runtime route。
- 不存在 CDS Hooks runtime route。
