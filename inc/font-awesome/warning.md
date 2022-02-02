.editor-styles-wrapper{} がないと 5.9 のブロックパターン挿入プレビューやタブレットで読み込まれない不具合がある(2022.2.1現在)ので応急対応で css ファイルに .editor-styles-wrapper{} が追加してある

```
/* .editor-styles-wrapper{} がないと 5.9 のブロックパターン挿入プレビューやタブレットで読み込まれない不具合がある(2022.2.1現在)ので応急対応 */
.editor-styles-wrapper{}
```