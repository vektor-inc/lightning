// @ts-check
import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost:1122/wp-login.php');
  await page.getByLabel('Username or Email Address').click();
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.getByLabel('Username or Email Address').press('Tab');
  await page.getByLabel('Password').fill('password');
  await page.getByRole('button', { name: 'Log In' }).click();
  await page.getByText('Themes 0').click();
  await expect(page).toHaveURL('http://localhost:1122/wp-admin/themes.php');
});
