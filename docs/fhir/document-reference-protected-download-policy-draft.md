# DocumentReference Protected Download Policy Draft

## Purpose

本文件定義 DocumentReference 文件 / 報告參照的保護下載政策。DocumentReference 目前只顯示 metadata / reference。不得直接暴露 binary content。不得自動下載檔案。不得把 DocumentReference 當成醫療結論。

## Current Boundary

Read-only Lesion Viewer 可顯示 DocumentReference metadata、type、status、date、reference id 或安全摘要。列表頁不得顯示敏感附件內容，不得 dereference binary URL，不得暴露 raw base64 content。

## Future Protected Download Conditions

1. 使用者已授權。
2. read-only access control 已完成。
3. 文件類型允許下載。
4. Consent / 授權狀態確認。
5. audit log 記錄。
6. 不在列表頁暴露敏感內容。
7. 不在錯誤訊息暴露檔案內容。
8. 不記錄完整 binary payload。
9. download link 有效期 / token policy 完成。
10. protected route review 完成。

## Prohibited Behaviors

禁止 auto-download。禁止 public binary URL。禁止 exposing raw base64 content。禁止 logging binary payload。禁止 showing sensitive attachment content in UI by default。禁止 DocumentReference binary exposed 的已啟用語意。
