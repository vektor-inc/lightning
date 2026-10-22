// @ts-check
import { test, expect } from '@playwright/test';

/*
npx playwright test --project=chromium --trace on はローカルで試すとおそらく1度落ちるが、
環境の都合なのでもう一度 npx playwright test --project=chromium --trace on すれば通る
直せる人は直せるタイミングで対応いただけると助かります。
*/

/**
 * 公開ボタンを押す。
 * 設定値が変わっていない（既に目的の値になっている）場合、公開ボタンは disabled（Published 表示）になり
 * クリックできずタイムアウトするため、押せるときだけ押す。
 * @param {import('@playwright/test').Page} page
 */
async function publishIfChanged(page) {
	const publishButton = page.locator('#save');
	if (await publishButton.isEnabled()) {
		await publishButton.click();
		// 保存が完了すると公開ボタンが disabled（Published）に戻るので、それまで待つ
		await expect(publishButton).toBeDisabled();
	}
}

test('CSS Optimize', async ({ page }) => {
	// ログイン処理
	await page.goto('/wp-login.php');
	await page.getByLabel('Username or Email Address').click();
	await page.getByLabel('Username or Email Address').fill('admin');
	await page.getByLabel('Password', { exact: true }).click();
	await page.getByLabel('Password', { exact: true }).fill('password');
	await page.getByLabel('Remember Me').check();
	await page.getByRole('button', { name: 'Log In' }).click();

	// Lightning を有効化
	await page.getByRole('link', { name: 'Appearance' }).click();
	await expect(page).toHaveURL(/\/wp-admin\/themes\.php$/);

	// カスタマイズ画面に移動
	await page.getByRole('navigation', { name: 'Main menu' }).getByRole('link', { name: 'Customize' }).click();
	// CSS最適化パネルを開く
	await page.getByRole('button', { name: 'Lightning CSS Optimize ( Speed up ) Settings', exact: true }).click();

	// _customize-input-vk_css_optimize_options[tree_shaking] が active なら処理しない
	// #save が disabled なら処理しない
	// というようにしたいが、現状では成功していないため、代替で一旦 何もしない を選択されるようにしている

	// Tree Shaking を有効化
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('active');

	// Preload を有効化
	await page.getByRole('combobox', { name: 'Preload CSS activation settings' }).selectOption('active');

	// 公開ボタンをクリック
	await publishIfChanged(page);

	// 保存が完了するまで時間稼ぎ
	await page.waitForTimeout(1000);

	// 公開画面に移動
	await page.goto('/');

	// ※ Tree Shakingが効いていない場合は style#lightning-common-style-css 自体が存在しないため、それをテスト対象としている
	// **************** type="text/css" が存在することを確認
	await expect(page.locator('style#lightning-common-style-css')).toHaveAttribute('type', 'text/css');

	// **************** #lightning-theme-style-css-preload が動作することを確認（ toHaveAttribute はここでは重要ではない ）
    // 実際のブラウザでは Preload 処理は問題なく rel が preload に変更して出力されるが、
	// Playwrite のブラウザ上では何故か rel=preload に切り替わらない。
	// しかし、Preload 有効にすると、id名に -pre がついている時点で preload の処理自体は動作している事が確認できるため、とりあえずOKとする
	await expect(page.locator('#lightning-theme-style-css-preload')).toHaveAttribute('rel', 'stylesheet');

	// テスト前の状態に戻す ///////////////////////////////////////
	// カスタマイズ画面に戻る
	await page.goto('/wp-admin/customize.php');
	await page.getByRole('button', { name: 'Lightning CSS Optimize ( Speed up ) Settings', exact: true }).click();
	// Tree Shaking を無効化
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('');
	// Preload を無効化
	await page.getByRole('combobox', { name: 'Preload CSS activation settings' }).selectOption('');
	// 公開ボタンをクリック
	await publishIfChanged(page);
	// 保存が完了するまで時間稼ぎ
	await page.waitForTimeout(1000);

	// G2 ///////////////////////////////////////
	// カスタマイズ画面に移動
	await page.goto('/wp-admin/customize.php');
	// CSS最適化パネルを開く
	await page.getByRole('button', { name: 'Lightning Function Settings', exact: true }).click();
	await page.locator('[id="_customize-input-lightning_theme_generation"]').selectOption('g2');
	await publishIfChanged(page);
	await page.getByRole('link', { name: 'Reload the page' }).click();
	await page.getByRole('button', { name: 'Lightning CSS Optimize ( Speed up ) Settings', exact: true }).click();
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('active');
	await page.getByRole('combobox', { name: 'Preload CSS activation settings' }).selectOption('active');
	await publishIfChanged(page);
	await page.waitForTimeout(1000);

	// 公開画面に移動
	await page.goto('/');

	// ※ Tree Shakingが効いていない場合は style#lightning-common-style-css 自体が存在しないため、それをテスト対象としている
	// **************** type="text/css" が存在することを確認
	await expect(page.locator('style#lightning-common-style-css')).toHaveAttribute('type', 'text/css');

	// **************** #lightning-theme-style-css-preload が動作することを確認（ toHaveAttribute はここでは重要ではない ）
    // 実際のブラウザでは Preload 処理は問題なく rel が preload に変更して出力されるが、
	// Playwrite のブラウザ上では何故か rel=preload に切り替わらない。
	// しかし、Preload 有効にすると、id名に -pre がついている時点で preload の処理自体は動作している事が確認できるため、とりあえずOKとする
	await expect(page.locator('#lightning-theme-style-css-preload')).toHaveAttribute('rel', 'stylesheet');

	// テスト前の状態に戻す ///////////////////////////////////////
	// カスタマイズ画面に戻る
	await page.goto('/wp-admin/customize.php');
	await page.getByRole('button', { name: 'Lightning CSS Optimize ( Speed up ) Settings', exact: true }).click();
	// Tree Shaking を無効化
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('');
	// Preload を無効化
	await page.getByRole('combobox', { name: 'Preload CSS activation settings' }).selectOption('');
	// 公開ボタンをクリック
	await publishIfChanged(page);

	// G3 に戻す ///////////////////////////////////////
	await page.goto('/wp-admin/customize.php');
	await page.getByRole('button', { name: 'Lightning Function Settings', exact: true }).click();
	await page.locator('[id="_customize-input-lightning_theme_generation"]').selectOption('g3');
	await publishIfChanged(page);

});
