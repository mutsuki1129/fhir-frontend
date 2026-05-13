# Route Safety Results

已執行：

```text
php artisan route:list --path=dev/fhir/mock-ingestion
php artisan route:list --path=gateway
php artisan route:list --path=ingestion
php artisan route:list --path=agent
php artisan route:list --path=validate
php artisan route:list --path=validation
php artisan route:list --path=lesions
php artisan route:list --path=api/lesions
php artisan route:list --path=queue
php artisan route:list --path=cds
php artisan route:list --path=smart
```

Allowed Phase 10B routes confirmed:

- `GET|HEAD /dev/fhir/mock-ingestion`
- `POST /dev/fhir/mock-ingestion/preview`

Forbidden ingestion / agent / validation routes:

- no `POST /gateway`
- no `POST /ingestion`
- no `POST /agent`
- no `POST /api/gateway`
- no `POST /api/ingestion`
- no `POST /api/agent`
- no live HAPI `$validate` route
- no validation runtime route added by Phase 10B / 10B.1 / 10B.2

Lesion routes:

- `GET|HEAD /api/lesions`
- `GET|HEAD /api/lesions/{lesion}`
- `GET|HEAD /lesions`
- `GET|HEAD /lesions/{lesion}`

No `POST /lesions`, `PATCH /lesions`, or `DELETE /lesions` observed.

Notes:

- `--path=ingestion` matches the dev mock ingestion routes by substring; these are explicitly dev-only and feature-flag guarded.
- Existing CDS governance/completeness routes were observed under `--path=cds`; they are pre-existing governance surfaces, not Phase 10B runtime ingestion, not CDS clinical advice runtime, and not part of this checkpoint scope.
- Existing SMART routes were observed under `--path=smart`, including logout; they are pre-existing and this phase does not activate SMART production mode.
