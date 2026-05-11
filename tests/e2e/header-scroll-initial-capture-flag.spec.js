// @ts-check
/**
 * PR #1337 e2e tests
 *
 * 関連 issue: #1333
 * 関連 PR  : #1332 (remove_header 内の再登録分の修正), #1335 (PR #1332 用 e2e)
 *
 * 本テストは _g2/assets/_js/_master.js の
 * 「`if(lightningOpt.header_scroll)` ブロック末尾で行われる scroll の **初期登録**」
 * が capture: false で登録されるよう修正されたことを検証する。
 *
 * 修正前は初期登録のみ第3引数 `true`（capture フェーズ）で登録されていた:
 *
 *   window.addEventListener('scroll', header_scroll_func, true)  // ← 修正前
 *
 * 一方、`remove_header` 内の removeEventListener は第3引数なし (capture: false 相当) で
 * 呼ばれるため、ペアにならず初期登録のリスナーが解除できなかった。
 * 修正後は第3引数 `false` に揃え、removeEventListener と整合する。
 *
 * 既存テスト（tests/e2e/header-scroll-capture-flag.spec.js）は PR #1332 のスコープ
 * （`remove_header` 内の 2 秒後の addEventListener 再登録分）を対象としており、
 * 「`if(lightningOpt.header_scroll)` ブロック末尾の初期登録」は検証対象外だった。
 * 本テストでその抜けを補う。
 *
 * 検証戦略:
 *   1. ソース静的検証: `_g2/assets/_js/_master.js` の初期登録 (header_scroll ブロック
 *      末尾の `window.addEventListener('scroll', header_scroll_func, ...)`) が
 *      capture: false で書かれていること。`_g3` 側は元から false なので同様であること。
 *   2. ブラウザ上での再現検証: 初期登録 → removeEventListener の流れで、
 *      capture: false 統一なら正しく解除され、true/false 不一致なら残る、という対比。
 *   3. シミュレーション: 「初期登録 (false) → クリック由来 remove → 2秒後 reAdd (false)」
 *      のサイクルを複数回回しても scroll リスナーが重複しない（呼び出しが 1 回のみ）。
 */

import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

const G2_MASTER = path.resolve(__dirname, '../../_g2/assets/_js/_master.js');
const G3_MASTER = path.resolve(__dirname, '../../_g3/assets/_js/_master.js');

/**
 * `_master.js` から `if(lightningOpt.header_scroll){ ... }` ブロックを抽出する。
 *
 * 構造:
 *   if(lightningOpt.header_scroll){
 *       let body_class_timer = false;
 *       ...
 *       window.addEventListener('scroll', header_scroll_func, false) // ← 初期登録（PR #1337 修正対象）
 *       window.addEventListener('DOMContentLoaded', header_scroll_func, false)
 *   }
 *
 * 内側に `remove_header` のアロー関数本体やコールバックの `{ ... }` がネストしているため、
 * 正規表現の lazy マッチでは終端 `}` を正しく拾えない。代わりにブレースカウンタで
 * 対応する閉じ括弧を探す。
 *
 * @param {string} code _master.js のソース全体
 * @returns {string | null} 抽出した `{ ... }` ブロック（中括弧含む）、見つからなければ null
 */
function extractHeaderScrollBlock(code) {
	// G2 では `if(lightningOpt.header_scroll){`、G3 では `if( lightningOpt.header_scroll && siteHeader ){`
	// のように条件部の書き方が違うので、`if (...) {` の括弧開始位置を正規表現で見つける。
	const headRe = /if\s*\(\s*lightningOpt\.header_scroll\b[^)]*\)\s*\{/;
	const headMatch = headRe.exec(code);
	if (!headMatch) return null;
	const openIdx = headMatch.index + headMatch[0].lastIndexOf('{');

	let depth = 0;
	for (let i = openIdx; i < code.length; i++) {
		const ch = code[i];
		if (ch === '{') depth++;
		else if (ch === '}') {
			depth--;
			if (depth === 0) {
				return code.slice(openIdx, i + 1);
			}
		}
	}
	return null;
}

/**
 * 「初期登録」部分（header_scroll ブロックの末尾にある
 * `window.addEventListener('scroll', header_scroll_func, ...)`）を取り出すヘルパー。
 * remove_header 内の同名呼び出しと区別するため、ブロック内の `remove_header` 定義以降を
 * 取り除いてから検索する。
 *
 * @param {string} blockSrc header_scroll ブロックのソース
 */
function extractInitialScrollAddEventListener(blockSrc) {
	// `let remove_header = (e) => { ... }` のブロックを取り除く（ネストした setTimeout も
	// 含めるため lazy マッチで `}, 2000);` まで吸う）
	const withoutRemoveHeader = blockSrc.replace(
		/\b(?:let|const)\s+remove_header\b[\s\S]*?\},\s*2000\);/,
		''
	);
	// 残った中で scroll を対象にした addEventListener を全部拾う
	const matches = withoutRemoveHeader.match(
		/window\.addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*(true|false)\s*\)/g
	);
	return matches || [];
}

test.describe('PR #1337: header_scroll_func 初期登録の capture フラグ修正', () => {
	test.describe('静的検証: ソースコード（初期登録部分）', () => {
		test('G2 _master.js の header_scroll ブロック末尾の初期 scroll 登録は capture: false', async () => {
			const code = fs.readFileSync(G2_MASTER, 'utf-8');
			const block = extractHeaderScrollBlock(code);
			expect(block, 'if(lightningOpt.header_scroll){...} ブロックが見つかること').toBeTruthy();

			const initialAdds = extractInitialScrollAddEventListener(block);
			// 初期登録は 1 件のみ（DOMContentLoaded リスナーは別カウント）
			expect(initialAdds.length, '初期 scroll addEventListener は 1 件のみ').toBe(1);
			// 第3引数が false であること（PR #1337 の本丸）
			expect(initialAdds[0]).toMatch(/,\s*false\s*\)\s*$/);
			expect(initialAdds[0]).not.toMatch(/,\s*true\s*\)\s*$/);
		});

		test('G3 _master.js の初期 scroll 登録も capture: false（リグレッション防止）', async () => {
			// G3 は元から false だが、将来 G2 に揃えて誤って true に書き換わらないよう
			// 同じ条件でテストしておく。
			const code = fs.readFileSync(G3_MASTER, 'utf-8');
			const block = extractHeaderScrollBlock(code);
			expect(block).toBeTruthy();

			const initialAdds = extractInitialScrollAddEventListener(block);
			expect(initialAdds.length).toBeGreaterThanOrEqual(1);
			for (const m of initialAdds) {
				expect(m).toMatch(/,\s*false\s*\)\s*$/);
				expect(m).not.toMatch(/,\s*true\s*\)\s*$/);
			}
		});

		test('G2: 初期登録 と remove_header 内の add/remove で capture フラグが揃っている', async () => {
			// PR #1337 の本質: 「初期登録の capture フラグ」が「removeEventListener の
			// capture (= false 相当)」と整合し、なおかつ「2 秒後の reAdd」とも整合する。
			const code = fs.readFileSync(G2_MASTER, 'utf-8');

			const block = extractHeaderScrollBlock(code);
			expect(block).toBeTruthy();

			// (a) 初期登録: false
			const initialAdds = extractInitialScrollAddEventListener(block);
			expect(initialAdds.length).toBe(1);
			expect(initialAdds[0]).toMatch(/,\s*false\s*\)\s*$/);

			// (b) remove_header 内 removeEventListener: 第3引数なし = false 相当
			expect(block).toMatch(
				/window\.removeEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*\)/
			);

			// (c) remove_header 内 setTimeout の addEventListener: false（PR #1332 で修正済み）
			const removeHeaderBlock = block.match(
				/\b(?:let|const)\s+remove_header\b[\s\S]*?\},\s*2000\);/
			);
			expect(removeHeaderBlock).toBeTruthy();
			expect(removeHeaderBlock[0]).toMatch(
				/addEventListener\(\s*['"]scroll['"]\s*,\s*header_scroll_func\s*,\s*false\s*\)/
			);
		});
	});

	test.describe('挙動検証: 初期登録パターンの再現テスト', () => {
		/**
		 * PR #1337 修正前の挙動を再現する HTML。
		 * 「初期登録は capture: true、remove は捕捉フラグなし（=false 相当）」の組み合わせ。
		 * PR #1332 と組み合わさった状態（remove_header 内の reAdd は false）を反映する。
		 */
		const beforeFixHtml = `<!doctype html>
			<html><head><meta charset="utf-8"><title>before-1337</title>
			<style>body { height: 5000px; }</style></head>
			<body>
			<script>
				window.__callCount = 0;
				let handler = () => { window.__callCount++; };
				// PR #1337 修正前と等価: 初期登録は capture: true
				window.addEventListener('scroll', handler, true);
				window.__handler = handler;
				// remove_header 相当: 第3引数なし → capture: false 相当
				window.__removeIt = () => { window.removeEventListener('scroll', handler); };
				// PR #1332 で修正済みの 2 秒後 reAdd: capture: false
				window.__reAddIt = () => { window.addEventListener('scroll', handler, false); };
			</script>
			</body></html>`;

		/**
		 * PR #1337 修正後の挙動を再現する HTML。
		 * 「初期登録 false / remove なし(false 相当) / reAdd false」で完全に揃っている。
		 */
		const afterFixHtml = `<!doctype html>
			<html><head><meta charset="utf-8"><title>after-1337</title>
			<style>body { height: 5000px; }</style></head>
			<body>
			<script>
				window.__callCount = 0;
				let handler = () => { window.__callCount++; };
				// PR #1337 修正後と等価: 初期登録は capture: false
				window.addEventListener('scroll', handler, false);
				window.__handler = handler;
				window.__removeIt = () => { window.removeEventListener('scroll', handler); };
				window.__reAddIt = () => { window.addEventListener('scroll', handler, false); };
			</script>
			</body></html>`;

		test('修正前: 初期登録 capture: true のリスナーは removeEventListener で消えず残り続ける', async ({ page }) => {
			await page.setContent(beforeFixHtml);

			// スクロールで初期リスナーが呼ばれることを確認
			await page.evaluate(() => window.scrollTo(0, 100));
			await page.waitForTimeout(50);
			expect(await page.evaluate(() => window.__callCount)).toBeGreaterThan(0);

			// remove_header 相当の removeEventListener を呼ぶ
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール
			const countAfterRemove = await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 200);
				return new Promise((resolve) => setTimeout(() => resolve(window.__callCount), 100));
			});

			// 修正前: capture フラグ不一致で remove が無音失敗し、初期リスナーは残る
			expect(countAfterRemove).toBeGreaterThan(0);
		});

		test('修正前: 2 秒後 reAdd と合わせるとリスナーが重複登録される', async ({ page }) => {
			// 「初期登録 (true) が残ったまま reAdd (false) でもう 1 件足される」を再現
			await page.setContent(beforeFixHtml);

			await page.evaluate(() => window.__removeIt()); // 失敗するが PR #1332 で残るのと同じ
			await page.evaluate(() => window.__reAddIt()); // capture: false でもう 1 件登録

			// 1 回スクロールすると重複した分だけ呼ばれる
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 100);
			});
			await page.waitForTimeout(50);

			// 修正前: 2 件登録されているので 2 回呼ばれる
			expect(await page.evaluate(() => window.__callCount)).toBe(2);
		});

		test('修正後: 初期登録 capture: false なら removeEventListener で正しく消える', async ({ page }) => {
			await page.setContent(afterFixHtml);

			// スクロールで初期リスナーが呼ばれること
			await page.evaluate(() => window.scrollTo(0, 100));
			await page.waitForTimeout(50);
			expect(await page.evaluate(() => window.__callCount)).toBeGreaterThan(0);

			// remove
			await page.evaluate(() => window.__removeIt());

			// もう一度スクロール → 呼ばれない
			const countAfterRemove = await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 200);
				return new Promise((resolve) => setTimeout(() => resolve(window.__callCount), 100));
			});
			expect(countAfterRemove).toBe(0);
		});

		test('修正後: 初期登録 → remove → reAdd を 3 回繰り返しても呼び出しは 1 回のまま（重複しない）', async ({ page }) => {
			// PR #1337 修正後の本番想定: クリック → 2 秒ロック → クリック … を繰り返した
			// ときに scroll リスナーが何件も重なって handler が増える、ということが起きない。
			await page.setContent(afterFixHtml);

			for (let i = 0; i < 3; i++) {
				await page.evaluate(() => window.__removeIt());
				await page.evaluate(() => window.__reAddIt());
			}

			// 最後に 1 回スクロール
			await page.evaluate(() => {
				window.__callCount = 0;
				window.scrollTo(0, 100);
			});
			await page.waitForTimeout(50);

			// 重複なし: 1 回だけ
			expect(await page.evaluate(() => window.__callCount)).toBe(1);
		});
	});
});
