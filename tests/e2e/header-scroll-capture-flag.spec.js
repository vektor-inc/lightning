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
		test('G2 _master.js の remove_header 内 setTimeout で capture: false で再登録されている', async () => {
			const code = fs.readFileSync(G2_MASTER, 'utf-8');

			// remove_header 関数のブロックを取得（setTimeout のネスト分も含めるため `}, 2000);` まで読む）
			// 修正前は addEventListener('scroll', header_scroll_func, true) だったのを false に変更している
			const removeHeaderBlock = code.match(/let remove_header[\s\S]*?\}, 2000\);/);
			expect(removeHeaderBlock).toBeTruthy();
			expect(removeHeaderBlock[0]).toContain("addEventListener('scroll', header_scroll_func, false)");
			expect(removeHeaderBlock[0]).not.toContain("addEventListener('scroll', header_scroll_func, true)");
		});

		test('G3 _master.js の remove_header 内 setTimeout で capture: false で再登録されている', async () => {
			const code = fs.readFileSync(G3_MASTER, 'utf-8');

			// remove_header 関数のブロックを取得（setTimeout のネスト分も含めるため `}, 2000);` まで読む）
			const removeHeaderBlock = code.match(/let remove_header[\s\S]*?\}, 2000\);/);
			expect(removeHeaderBlock).toBeTruthy();
			expect(removeHeaderBlock[0]).toContain("addEventListener('scroll', header_scroll_func, false)");
			expect(removeHeaderBlock[0]).not.toContain("addEventListener('scroll', header_scroll_func, true)");
		});

		test('G2 / G3 の remove_header 内 removeEventListener も capture フラグなし（=false 相当）になっている', async () => {
			// removeEventListener の第3引数なし or false なら capture: false 相当
			// addEventListener の capture が false と一致するか確認
			const g2 = fs.readFileSync(G2_MASTER, 'utf-8');
			const g3 = fs.readFileSync(G3_MASTER, 'utf-8');

			// 第3引数なしの removeEventListener が remove_header 内にあること（=暗黙の false）
			expect(g2).toMatch(/window\.removeEventListener\('scroll', header_scroll_func\)/);
			expect(g3).toMatch(/window\.removeEventListener\('scroll', header_scroll_func\)/);
		});
	});

	test.describe('挙動検証: capture フラグ不一致 vs 一致でのリスナー解除挙動', () => {
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
			await page.waitForTimeout(50);
			const countBeforeRemove = await page.evaluate(() => window.__callCount);
			expect(countBeforeRemove).toBeGreaterThan(0);

			// removeEventListener を呼ぶ（capture: false 側）→ 修正前は無音失敗
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール
			const callCountAfterRemove = await page.evaluate(() => {
				window.__callCount = 0; // リセット
				window.scrollTo(0, 200);
				return new Promise((resolve) => {
					setTimeout(() => resolve(window.__callCount), 100);
				});
			});

			// 修正前の挙動: remove に失敗してリスナーが残るので呼ばれる
			expect(callCountAfterRemove).toBeGreaterThan(0);
		});

		test('修正後の挙動: capture フラグ一致だと removeEventListener が成功してリスナーが消える', async ({ page }) => {
			// 修正後と等価な HTML を開く
			await page.setContent(afterFixHtml);

			// スクロールで handler が呼ばれる事を確認
			await page.evaluate(() => window.scrollTo(0, 100));
			await page.waitForTimeout(50);
			const countBeforeRemove = await page.evaluate(() => window.__callCount);
			expect(countBeforeRemove).toBeGreaterThan(0);

			// removeEventListener を呼ぶ（capture: false 側、登録側も capture: false）→ 成功
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール
			const callCountAfterRemove = await page.evaluate(() => {
				window.__callCount = 0; // リセット
				window.scrollTo(0, 200);
				return new Promise((resolve) => {
					setTimeout(() => resolve(window.__callCount), 100);
				});
			});

			// 修正後の挙動: remove に成功してリスナーが消えるので呼ばれない
			expect(callCountAfterRemove).toBe(0);
		});

		test('PR 修正後: 再登録（capture: false）→ scroll で再び呼ばれる（=2秒ロック後の挙動）', async ({ page }) => {
			await page.setContent(afterFixHtml);

			// 一度 remove
			await page.evaluate(() => window.__removeIt());
			// scroll しても呼ばれないこと
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 100);
			});
			await page.waitForTimeout(50);
			expect(await page.evaluate(() => window.__callCount)).toBe(0);

			// PR 修正後と等価な capture: false 再登録
			await page.evaluate(() => window.__reAddIt());

			// scroll すると再び呼ばれる
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 200);
			});
			await page.waitForTimeout(50);
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
			await page.waitForTimeout(50);

			// 重複登録されていれば 2 以上になるが、PR 修正後は 1 のみ
			const count = await page.evaluate(() => window.__callCount);
			expect(count).toBe(1);
		});
	});
});
