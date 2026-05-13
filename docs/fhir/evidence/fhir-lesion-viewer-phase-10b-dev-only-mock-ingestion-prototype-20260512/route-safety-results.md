# Route Safety Results

## 執行日期

2026-05-13

## 已執行 route:list

- `php artisan route:list --path=dev/fhir/mock-ingestion`
- `php artisan route:list --path=gateway`
- `php artisan route:list --path=ingestion`
- `php artisan route:list --path=agent`
- `php artisan route:list --path=validate`
- `php artisan route:list --path=validation`
- `php artisan route:list --path=lesions`
- `php artisan route:list --path=api/lesions`

## 允許的 Phase 10B dev-only routes

`php artisan route:list --path=dev/fhir/mock-ingestion` 顯示：

- `GET|HEAD dev/fhir/mock-ingestion`
- `POST dev/fhir/mock-ingestion/preview`

這兩條 route 均掛在 `fhir.controlled_ingestion_prototype` middleware 後方，只允許 local/testing、feature flag enabled、mode=mock、allow_fhir_write=false。

## 禁止 route 檢查

- `php artisan route:list --path=gateway` 只顯示既有 `GET|HEAD fhir/gateway`，沒有 `POST /gateway`。
- `php artisan route:list --path=agent` 無匹配 route。
- `php artisan route:list --path=validate` 無匹配 route。
- `php artisan route:list --path=validation` 無匹配 route。
- `php artisan route:list --path=ingestion` 只因字串包含 `ingestion` 而匹配到 `/dev/fhir/mock-ingestion/*`，沒有 root-level `POST /ingestion` 或 `/api/ingestion`。

確認未新增：

- `POST /gateway`
- `POST /ingestion`
- `POST /agent`
- `POST /api/gateway`
- `POST /api/ingestion`
- `POST /api/agent`
- `POST /lesions`
- `PATCH /lesions`
- `DELETE /lesions`

## Lesion route read-only confirmation

`php artisan route:list --path=lesions` 顯示：

- `GET|HEAD api/lesions`
- `GET|HEAD api/lesions/{lesion}`
- `GET|HEAD lesions`
- `GET|HEAD lesions/{lesion}`

`php artisan route:list --path=api/lesions` 顯示：

- `GET|HEAD api/lesions`
- `GET|HEAD api/lesions/{lesion}`

Lesion Viewer 仍維持 read-only route posture，沒有新增 lesion write route。
