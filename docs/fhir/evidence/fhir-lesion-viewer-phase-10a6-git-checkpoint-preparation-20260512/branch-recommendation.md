# Branch Recommendation

建議 branch：

```text
fhir-readonly-lesion-viewer-baseline-phase-1-to-10a
```

或：

```text
checkpoint/fhir-readonly-lesion-viewer-phase-1-to-10a
```

## Branch Creation Guidance

建立 branch 前應先確認目前所在 branch：

```bash
git branch --show-current
git status --short
```

若 repo 有大量 unrelated dirty files，需避免 checkout 造成衝突。建議人工確認目前分支、未追蹤檔、tracked deletion 與 modified files 後再建立 branch。

Codex 不應自動 checkout。
Codex 不應自動建立 branch。
Codex 不應自動 reset、clean 或 stash unrelated changes。
