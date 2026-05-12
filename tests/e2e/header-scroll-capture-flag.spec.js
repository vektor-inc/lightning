// @ts-check
/**
 * PR #1332 e2e tests
 *
 * このテストは _g2/assets/_js/_master.js / _g3/assets/_js/_master.js の
 * remove_header 関数内の addEventListener が capture: false で再登録されるよう
 * 修正されたことを検証する。
 *
 * 修正前は capture: true で再登録されていたため、removeEventListener
 * (capture: false) と不一致になり、scroll リスナーが意図せず残り続ける
 * バグがあった。
 *
 * 検証戦略:
 *  1. ソースファイルの静的検証
 *     - PR の変更ポイント（remove_header 内の setTimeout）で capture: false
 *       になっていることを確認する。
 *  2. ブラウザ上での挙動検証
 *     - addEventListener / removeEventListener の capture 不一致が
 *       実際にリスナー残留を引き起こすことを再現テストで確認し、
 *     - 一致させた場合（PR の修正後と等価）に正しく remove されることを確認する。
 *
 * 注:
 *  - Lightning テーマの全機能を起動するのは複雑なので、
 *    PR の本質である "addEventListener / removeEventListener の capture フラグ
 *    の一致による正しいリスナー解除挙動" を独立した HTML で再現検証する。
 */

import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

const G2_MASTER = path.resolve(__dirname, '../../_g2/assets/_js/_master.js');
const G3_MASTER = path.resolve(__dirname, '../../_g3/assets/_js/_master.js');

test.describe('PR #1332: header_scroll_func capture フラグ修正', () => {
	test.describe('静的検証: ソースコード', () => {
		// remove_header 関数のブロックを抽出する正規表現
		// - 宣言種別は `let` / `const` 両方を許容（将来のリファクタ耐性）
		// - `}, 2000);` までを lazy マッチして setTimeout のネスト分も含める
		const REMOVE_HEADER_BLOCK_RE = /\b(?:let|const)\s+remove_header\b[\s\S]*?\},\s*2000\);/;

		test('G2 _master.js の remove_header 内 setTimeout で capture: false で再登録されている', async () => {
			const code = fs.readFileSync(G2_MASTER, 'utf-8');

			// 修正前は addEventListener('scroll', header_scroll_func, true) だったのを false に変更している
			const removeHeaderBlock = code.match(REMOVE_HEADER_BLOCK_RE);
			expect(removeHeaderBlock).toBeTruthy();
			// 空白差分・クォート差分を許容しつつ capture: false で再登録されていることを確認
			expect(removeHeaderBlock[0]).toMatch(
				/addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*false\s*\)/
			);
			expect(removeHeaderBlock[0]).not.toMatch(
				/addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*true\s*\)/
			);
		});

		test('G3 _master.js の remove_header 内 setTimeout で capture: false で再登録されている', async () => {
			const code = fs.readFileSync(G3_MASTER, 'utf-8');

			const removeHeaderBlock = code.match(REMOVE_HEADER_BLOCK_RE);
			expect(removeHeaderBlock).toBeTruthy();
			expect(removeHeaderBlock[0]).toMatch(
				/addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*false\s*\)/
			);
			expect(removeHeaderBlock[0]).not.toMatch(
				/addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*true\s*\)/
			);
		});

		test('G2 / G3 の remove_header 内 removeEventListener も capture フラグなし（=false 相当）になっている', async () => {
			// removeEventListener の第3引数なし or false なら capture: false 相当
			// addEventListener の capture が false と一致するか確認
			const g2 = fs.readFileSync(G2_MASTER, 'utf-8');
			const g3 = fs.readFileSync(G3_MASTER, 'utf-8');

			// remove_header ブロック内に限定して removeEventListener の呼び出しを検証
			// （ファイル全体に対するマッチだと remove_header 外の同名呼び出しでも PASS してしまうため）
			const g2Block = g2.match(REMOVE_HEADER_BLOCK_RE);
			const g3Block = g3.match(REMOVE_HEADER_BLOCK_RE);
			expect(g2Block).toBeTruthy();
			expect(g3Block).toBeTruthy();

			// 第3引数なしの removeEventListener が remove_header 内にあること（=暗黙の false）
			expect(g2Block[0]).toMatch(/window\.removeEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*\)/);
			expect(g3Block[0]).toMatch(/window\.removeEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*\)/);
		});
	});

	test.describe('挙動検証: capture フラグ不一致 vs 一致でのリスナー解除挙動', () => {
		/**
		 * 共通ヘルパー: requestAnimationFrame を 2 フレーム待ってから window.__callCount を返す。
		 *
		 * - 「scroll しても増えないこと」を確認するケースでは、ポーリングだけでは
		 *   「まだ未到達」と「本当に増えない」が区別できないので、scroll イベントの
		 *   dispatch / 反映が確実に終わるタイミングまで 2 RAF 待ってから値を読む必要がある。
		 * - scrollY を指定した場合: __callCount をリセットして window.scrollTo(0, scrollY) を発火し、
		 *   2 RAF 後の __callCount を返す。
		 * - scrollY を省略した場合: scroll は発火せず、その時点から 2 RAF 待った後の __callCount を返す
		 *   （事前に scroll + waitForFunction で 1 回呼ばれていることを確認しておいた値を flush するための用途）。
		 *
		 * @param {import('@playwright/test').Page} page Playwright の page インスタンス
		 * @param {number|null} scrollY スクロール先 Y 座標。null を渡すと scroll は発火しない
		 * @returns {Promise<number>} 2 RAF 待機後の window.__callCount
		 */
		async function getCallCountAfterTwoRaf(page, scrollY = null) {
			return page.evaluate((y) => {
				// scrollY が指定されていればカウンタをリセットして scroll を発火する
				if (y !== null) {
					window.__callCount = 0;
					window.scrollTo(0, y);
				}
				// 2 RAF 待ってから __callCount を返す（scroll イベントの処理が確実に終わるまで待機）
				return new Promise((resolve) => {
					requestAnimationFrame(() => {
						requestAnimationFrame(() => resolve(window.__callCount));
					});
				});
			}, scrollY);
		}

		/**
		 * 修正前の挙動を再現する HTML（PR 修正前と同じ捕捉フラグ不一致）
		 * addEventListener('scroll', fn, true) ＋ removeEventListener('scroll', fn) の組み合わせ
		 * → removeEventListener は capture: false を見るので、対象が無く無音失敗する。
		 * → リスナーが残り、スクロール時に handler が呼ばれ続ける。
		 */
		const beforeFixHtml = `<!doctype html>
			<html><head><meta charset="utf-8"><title>before-fix</title>
			<style>body { height: 5000px; }</style></head>
			<body>
			<script>
				window.__callCount = 0;
				let handler = () => { window.__callCount++; };
				// 修正前と等価: capture: true で登録
				window.addEventListener('scroll', handler, true);
				// remove 側は capture: false（第3引数なし）→ ペアにならず無音失敗
				window.__removeIt = () => { window.removeEventListener('scroll', handler); };
				// 2 秒後に capture: true で再登録（修正前）
				window.__reAddIt = () => { window.addEventListener('scroll', handler, true); };
			</script>
			</body></html>`;

		/**
		 * 修正後の挙動を再現する HTML（PR 修正後と同じ capture: false 統一）
		 * addEventListener('scroll', fn, false) ＋ removeEventListener('scroll', fn) のペア
		 * → removeEventListener が成功して listener が消える。
		 */
		const afterFixHtml = `<!doctype html>
			<html><head><meta charset="utf-8"><title>after-fix</title>
			<style>body { height: 5000px; }</style></head>
			<body>
			<script>
				window.__callCount = 0;
				let handler = () => { window.__callCount++; };
				// 修正後と等価: capture: false で登録
				window.addEventListener('scroll', handler, false);
				window.__removeIt = () => { window.removeEventListener('scroll', handler); };
				// PR 修正後: capture: false で再登録
				window.__reAddIt = () => { window.addEventListener('scroll', handler, false); };
			</script>
			</body></html>`;

		test('修正前の挙動: capture フラグ不一致だと removeEventListener が無音失敗してリスナーが残る', async ({ page }) => {
			// 修正前と等価な HTML を data URL で開く
			await page.setContent(beforeFixHtml);

			// 一度スクロールを発生させて handler が呼ばれる事を確認
			await page.evaluate(() => window.scrollTo(0, 100));
			// 固定 sleep ではなく __callCount が増えるまでポーリング待機する（フレーク耐性向上）
			await page.waitForFunction(() => window.__callCount > 0);
			const countBeforeRemove = await page.evaluate(() => window.__callCount);
			expect(countBeforeRemove).toBeGreaterThan(0);

			// removeEventListener を呼ぶ（capture: false 側）→ 修正前は無音失敗
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール（カウンタはリセットしてから発火）
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 200);
			});

			// 修正前の挙動: remove に失敗してリスナーが残っているはずなので、
			// __callCount が増えるのを固定 sleep ではなくポーリングで待つ。
			// もしリスナーが残っていなければ waitForFunction がタイムアウトしてテスト失敗となる。
			await page.waitForFunction(() => window.__callCount > 0);
			const callCountAfterRemove = await page.evaluate(() => window.__callCount);
			expect(callCountAfterRemove).toBeGreaterThan(0);
		});

		test('修正後の挙動: capture フラグ一致だと removeEventListener が成功してリスナーが消える', async ({ page }) => {
			// 修正後と等価な HTML を開く
			await page.setContent(afterFixHtml);

			// スクロールで handler が呼ばれる事を確認
			await page.evaluate(() => window.scrollTo(0, 100));
			// 固定 sleep ではなく __callCount が増えるまでポーリング待機する
			await page.waitForFunction(() => window.__callCount > 0);
			const countBeforeRemove = await page.evaluate(() => window.__callCount);
			expect(countBeforeRemove).toBeGreaterThan(0);

			// removeEventListener を呼ぶ（capture: false 側、登録側も capture: false）→ 成功
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール（カウンタはリセットしてから発火）。
			// 「増えない」ことを確認するケースは、ポーリングだけでは「まだ未到達」と区別が付かないため、
			// ヘルパー経由で scroll → 2 RAF 待機 → __callCount を取得する。
			const callCountAfterRemove = await getCallCountAfterTwoRaf(page, 200);

			// 修正後の挙動: remove に成功してリスナーが消えるので呼ばれない
			expect(callCountAfterRemove).toBe(0);
		});

		test('PR 修正後: 再登録（capture: false）→ scroll で再び呼ばれる（=2秒ロック後の挙動）', async ({ page }) => {
			await page.setContent(afterFixHtml);

			// 一度 remove
			await page.evaluate(() => window.__removeIt());

			// scroll しても呼ばれないことを確認。
			// 「増えない」ことの確認は固定 sleep ではなく 2 RAF 待ちで scroll イベント処理を flush する。
			const countAfterRemove = await getCallCountAfterTwoRaf(page, 100);
			expect(countAfterRemove).toBe(0);

			// PR 修正後と等価な capture: false 再登録
			await page.evaluate(() => window.__reAddIt());

			// scroll すると再び呼ばれる
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 200);
			});
			// 固定 sleep ではなく __callCount が増えるまでポーリング待機する
			await page.waitForFunction(() => window.__callCount > 0);
			expect(await page.evaluate(() => window.__callCount)).toBeGreaterThan(0);
		});

		test('リスナーが重複登録されていない: remove → reAdd 後の handler 呼び出し回数は 1 イベントにつき 1 回のみ', async ({ page }) => {
			await page.setContent(afterFixHtml);

			// PR 修正後と等価な「remove → reAdd」サイクルを実施
			await page.evaluate(() => window.__removeIt());
			await page.evaluate(() => window.__reAddIt());

			// 1 回 scroll を発生させる
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 100);
			});

			// 固定 sleep ではなく __callCount が 1 以上になるまでポーリング待機する。
			// 重複登録があれば 2 以上に増えてしまうため、その後 2 RAF 待って
			// scroll イベントが完全に処理されたタイミングで最終値を評価する。
			// scroll はすでに発火済みなのでヘルパーには scrollY を渡さず、
			// 2 RAF 待ちのみ実行して flush 後の __callCount を取得する。
			await page.waitForFunction(() => window.__callCount >= 1);
			const count = await getCallCountAfterTwoRaf(page);

			// 重複登録されていれば 2 以上になるが、PR 修正後は 1 のみ
			expect(count).toBe(1);
		});
	});
});
