# Feature Flag Results

新增 config:

```php
'controlled_ingestion_prototype' => [
    'enabled' => env('FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED', false),
    'mode' => env('FHIR_CONTROLLED_INGESTION_PROTOTYPE_MODE', 'mock'),
    'allow_fhir_write' => false,
],
```

`allow_fhir_write` 固定為 `false`，不得從 env 開啟。Feature flag default disabled。

Guard 條件：

- app environment 必須是 `local` 或 `testing`
- feature flag 必須 enabled
- mode 必須是 `mock`
- `allow_fhir_write` 必須嚴格等於 `false`

不符合時回 404。
