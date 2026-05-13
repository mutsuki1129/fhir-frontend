# Authorization State

授權狀態：

- `EXECUTE_CHECKPOINT=true`
- `ALLOW_GIT_ADD=true`
- `ALLOW_GIT_COMMIT=true`
- `ALLOW_BRANCH_CREATE=false`
- `ALLOW_PATCH_EXPORT=false`

執行規則：

- 允許逐檔 `git add`。
- 允許建立 selective checkpoint commits。
- 禁止 `git add .`。
- 禁止 `git add -A`。
- 禁止 wildcard staging。
- 禁止 `git reset`。
- 禁止 `git clean`。
- 禁止 `git checkout`。
- 禁止 `git push`。
- 禁止 `commit --amend`。

預期 branch：

- `checkpoint/fhir-readonly-lesion-viewer-phase-1-to-10a`

開始前檢查結果：

- 目前 branch 符合預期。
- 未發現 unmerged paths。
- 開始前 cached diff 為空。
- working tree 原本已有大量 unrelated dirty / untracked files，本階段不清理、不 stage。
