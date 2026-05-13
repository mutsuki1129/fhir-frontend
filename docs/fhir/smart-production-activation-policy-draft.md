# SMART Production Activation Policy Draft

## Purpose

本文件定義未來 SMART production activation 的前置條件。Phase 9C 不啟用 SMART production。Phase 9C 不新增 SMART launch runtime。Phase 9C 不新增 production OAuth flow。Phase 9C 不新增 production scopes。

## Future Activation Criteria

1. 明確 production environment，並與 dev / test environment 隔離。
2. OAuth2 / OIDC 設定完成，包含 issuer、client registration、token audience 與 discovery metadata。
3. SMART scopes 採最小權限原則。
4. read-only scope 與 write scope 分離。
5. write scope 預設禁用，且 Read-only Lesion Viewer 不需要 production write scope。
6. redirect URI allowlist 完成並經安全審查。
7. token storage policy 完成，禁止把 token、secret、refresh token 暴露到前端或 log。
8. audit logging policy 完成，能追蹤 actor、role、patient context、scope 與 access decision。
9. PHI / privacy review 完成。
10. security review 完成。
11. clinical workflow review 完成。
12. explicit production approval 完成，且 approval 記錄需指明範圍、環境與有效期限。

## Safety Boundaries

SMART activation 不代表可寫 FHIR Server。SMART activation 不代表 CDS runtime 啟用。SMART activation 不代表 clinical advice 啟用。SMART launch enabled 的未來狀態也不得自動推導為 FHIR write pipeline enabled。

Phase 9C 不新增 production gateway、不新增 SMART production runtime、不新增 production ingestion、不授予 production approval。

## Prohibition Criteria

未通過 security review、privacy review、scope review、token storage review、redirect URI allowlist review 或 explicit production approval 時，必須維持 blocked 或 deferred。任何 read-only viewer 不得因 SMART production review 而自動取得 write scope。
