# Controlled Ingestion Rollback / Disablement Plan

未來 prototype 如果真的進入 Phase 10B，必須具備 rollback / disablement plan。

## Required Future Controls

- feature flag required
- default disabled
- dev-only
- mock-only
- no production enablement
- emergency disable
- route disablement
- queue disablement
- writer remains disabled
- cache clear procedure
- audit / evidence preservation

Phase 10A 不新增 feature flag runtime。Phase 10A 只定義未來需要什麼 feature flag 與 rollback policy。

若未來 Phase 10B 發現任何 FHIR write、production route、real PHI、AI direct write、clinical advice、automatic diagnosis、treatment recommendation 或 DocumentReference binary exposure，prototype 必須立即停用並保留 evidence。

