# i18n Coverage Report (Phase X X0)

## Scope

Coverage snapshot for frontend translation usage at Phase X start.

## Keyed Coverage (Already `ui.*` or translated)

### High-confidence keyed areas

1. Navigation labels (`Dashboard`, `Medical Records`, `Doctors`, `Patients`, locale switch labels)
2. Patients list page (`/pasiens`):
   - title, add button, search label/placeholder/button
   - key headers (`no/name/email/phone`)
   - empty/error title keys
3. Doctors list page (`/dokters`):
   - title, add button, search label/placeholder/button
   - key headers (`no/name/email/phone`)
4. Medical records list (`/rekam`):
   - empty/error title keys

## Partial / Not Fully Keyed

1. Medical records detail strings (status badges and many inline labels still hardcoded English)
2. Patients/doctors table secondary labels:
   - `asc`/`desc` currently generic translation keys, not `ui.*` namespace
3. Most create/edit forms:
   - doctor create/edit
   - patient create/edit
   - rekam create/edit
4. Profile and partial components:
   - many inherited auth/profile strings not normalized to `ui.*`

## High-risk Pages for i18n Drift

1. `resources/views/admin/rekam/create.blade.php`
2. `resources/views/admin/rekam/edit.blade.php`
3. `resources/views/admin/createDokter.blade.php`
4. `resources/views/admin/editDokter.blade.php`
5. `resources/views/admin/createPasien.blade.php`
6. shared partials under `resources/views/admin/partials/*.blade.php`

## Primary Risks

1. Inconsistent language output during locale switch in mixed pages.
2. Future UI edits may add new hard strings if namespace rules are not enforced.
3. Operational copy (errors/hints) may diverge between pages without a common key taxonomy.

## Suggested X-phase Remediation Order

1. Rekam create/edit labels + validation copy
2. Dokter create/edit labels + deferred-field explanation copy
3. Pasien create/edit labels
4. Shared partial cleanup and key namespace normalization

## Baseline Conclusion

- List-level i18n baseline is usable.
- Form-level i18n baseline is incomplete and should be treated as a planned migration stream in Phase X.
