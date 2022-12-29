// @ts-check
import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
	await page.goto('http://localhost:8889/wp-login.php');
	await page.getByLabel('Username or Email Address').click();
	await page.getByLabel('Username or Email Address').fill('admin');
	await page.getByLabel('Username or Email Address').press('Tab');
	await page.getByLabel('Password').fill('password');
	await page.getByRole('button', { name: 'Log In' }).click();
	await page.getByRole('link', { name: 'Appearance' }).click();
	await expect(page).toHaveURL('http://localhost:8889/wp-admin/themes.php');

	// カスタマイズ画面に移動
	await page.getByRole('navigation', { name: 'Main menu' }).getByRole('link', { name: 'Customize' }).click();
	// CSS最適化パネルを開く
	await page.getByRole('heading', { name: 'Lightning CSS Optimize ( Speed up ) Settings Press return or enter to open this section ' }).click();

	// _customize-input-vk_css_optimize_options[tree_shaking] が active なら処理しない
	// #save が disabled なら処理しない
	// というようにしたいが、現状では成功していないため、代替でいったん 何もしない を選択されるようにしている
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('');
	// Tree Shaking を有効化
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('active');
	// 公開ボタンをクリック
	await page.getByRole('button', { name: 'Publish' }).filter({ hasText: 'Publish' }).click();

	// 公開画面に移動
	await page.goto('http://localhost:8889/');

	// style#lightning-common-style-css を取得
	// ※ Tree Shakingが効いていない場合は style#lightning-common-style-css 自体が存在しないため、それをテスト対象としている
	const locator = page.locator('style#lightning-common-style-css');
	// type="text/css" が存在することを確認
	await expect(locator).toHaveAttribute('type', 'text/css');

	// Tree Shaking を無効化 ( テスト前の状態に戻す )
	await page.getByRole('link', { name: ' Customize' }).click();
	await page.getByRole('heading', { name: 'Lightning CSS Optimize ( Speed up ) Settings Press return or enter to open this section ' }).click();
	await page.getByRole('combobox', { name: 'Tree shaking activation settings' }).selectOption('');
	await page.getByRole('button', { name: 'Publish' }).filter({ hasText: 'Publish' }).click();
});
