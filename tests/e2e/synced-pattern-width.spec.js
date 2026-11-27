// @ts-check
const { test, expect } = require('@playwright/test');

test('G3 keeps synced full and wide blocks inside the editor canvas', async ({ page }) => {
	// 管理者としてログインし、ブロック API を使える編集画面を開く。
	await page.goto('/wp-login.php');
	await page.getByLabel('Username or Email Address').fill('admin');
	await page.getByLabel('Password', { exact: true }).fill('password');
	await page.getByRole('button', { name: 'Log In' }).click();
	await page.goto('/wp-admin/post-new.php?post_type=page');
	await page.waitForFunction(() => window.wp?.apiFetch && window.wp?.blocks);

	// 同期パターンと通常ブロックを同じ固定ページへ配置する。
	const postId = await page.evaluate(async () => {
		const { createBlock, serialize } = window.wp.blocks;
		const paragraph = (text) => createBlock('core/paragraph', { content: text });
		const group = (align, text, children = [paragraph(text)]) => createBlock('core/group', { align, backgroundColor: 'vivid-cyan-blue' }, children);
		const savePattern = async (title, blocks) => {
			const pattern = await window.wp.apiFetch({ path: '/wp/v2/blocks', method: 'POST', data: { title, content: serialize(blocks), status: 'publish' } });
			return createBlock('core/block', { ref: pattern.id });
		};
		const inner = await savePattern('Nested inner', [paragraph('Nested inner paragraph')]);
		const constrainedFull = await savePattern('Constrained full', [
			group('full', 'Constrained synced full group', [paragraph('Constrained synced full group'), inner]),
			paragraph('Constrained synced ordinary paragraph'),
			createBlock('core/image', { url: 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=', alt: 'Constrained synced ordinary image' }),
		]);
		const constrainedWide = await savePattern('Constrained wide', [group('wide', 'Constrained synced wide group')]);
		const blocks = [
			paragraph('Reference ordinary paragraph'),
			group('full', 'Reference full group'),
			group('wide', 'Reference wide group'),
			await savePattern('Full only', [group('full', 'Synced full group')]),
			await savePattern('Wide only', [group('wide', 'Synced wide group')]),
			await savePattern('Ordinary only', [paragraph('Synced ordinary paragraph'), createBlock('core/image', { url: 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=', alt: 'Synced ordinary image' })]),
			await savePattern('Mixed', [group('full', 'Mixed full group'), paragraph('Mixed ordinary paragraph')]),
			await savePattern('Nested in full', [group('full', 'Nested full group', [inner])]),
			await savePattern('Nested direct', [inner]),
			createBlock('core/group', { layout: { type: 'constrained' } }, [constrainedFull]),
			createBlock('core/group', { layout: { type: 'constrained' } }, [constrainedWide]),
		];
		const post = await window.wp.apiFetch({ path: '/wp/v2/pages', method: 'POST', data: { title: 'Synced width regression', content: serialize(blocks), status: 'draft' } });
		return post.id;
	});
	await page.goto(`/wp-admin/post.php?post=${postId}&action=edit`);
	const frame = page.frameLocator('iframe[name="editor-canvas"]');
	await expect(frame.locator('[data-type="core/block"]')).toHaveCount(11);
	await expect(frame.locator('[data-type="core/block"] [data-type="core/group"]')).toHaveCount(6);

	// iframe 内の実寸とクラスを記録し、通常幅への影響も確認する。
	const measurements = await frame.locator('.editor-styles-wrapper').evaluate((wrapper) => {
		const rect = (element) => {
			const { left, right, width } = element.getBoundingClientRect();
			return { left, right, width };
		};
		const root = wrapper.querySelector('.is-root-container');
		const patterns = [...root.children].filter((child) => child.dataset.type === 'core/block');
		return {
			canvas: { left: 0, right: window.innerWidth, width: window.innerWidth },
			ordinary: rect(wrapper.querySelector('.is-root-container > [data-type="core/paragraph"]')),
			ordinaryFull: rect(wrapper.querySelector('.is-root-container > [data-align="full"]')),
			ordinaryWide: rect(wrapper.querySelector('.is-root-container > [data-align="wide"]')),
			patterns: patterns.map((outer) => ({
				className: outer.className,
				outer: rect(outer),
				children: [...outer.querySelectorAll('[data-type="core/group"], [data-type="core/paragraph"], [data-type="core/image"]')].map((child) => ({ type: child.dataset.type, align: child.parentElement?.dataset.align || '', rect: rect(child.parentElement?.hasAttribute('data-align') ? child.parentElement : child) })),
			})),
			constrained: (() => {
				const outer = [...wrapper.querySelectorAll('[data-type="core/block"]')].find((element) => element.textContent?.includes('Constrained synced full group'));
				return outer && {
					className: outer.className,
					outer: rect(outer),
					child: rect(outer.querySelector('[data-align="full"]')),
					paragraph: rect([...outer.querySelectorAll('[data-type="core/paragraph"]')].find((element) => element.textContent?.includes('Constrained synced ordinary paragraph'))),
					image: rect(outer.querySelector('[data-type="core/image"]')),
					nested: rect([...outer.querySelectorAll('[data-type="core/block"]')].find((element) => element.textContent?.includes('Nested inner paragraph'))),
				};
			})(),
			constrainedWide: (() => {
				const outer = [...wrapper.querySelectorAll('[data-type="core/block"]')].find((element) => element.textContent?.includes('Constrained synced wide group'));
				return outer && { className: outer.className, outer: rect(outer), child: rect(outer.querySelector('[data-align="wide"]')) };
			})(),
		};
	});
	const [full, wide, ordinary, mixed, nestedFull, nestedDirect] = measurements.patterns;
	const close = (actual, expected) => expect(Math.abs(actual - expected)).toBeLessThanOrEqual(1);
	const centered = (rect) => close(rect.left + rect.right, measurements.canvas.left + measurements.canvas.right);
	for (const pattern of measurements.patterns) {
		expect(pattern.className).not.toContain('alignfull');
	}
	for (const pattern of [measurements.constrained, measurements.constrainedWide]) {
		expect(pattern.className).toContain('alignfull');
		expect(pattern.className).toContain('is-layout-constrained');
	}

	close(full.children[0].rect.left, measurements.canvas.left);
	close(full.children[0].rect.right, measurements.canvas.right);
	close(mixed.children[0].rect.left, measurements.canvas.left);
	close(mixed.children[0].rect.right, measurements.canvas.right);
	close(nestedFull.children[0].rect.left, measurements.canvas.left);
	close(nestedFull.children[0].rect.right, measurements.canvas.right);
	expect(wide.children[0].rect.width).toBeGreaterThan(measurements.ordinary.width);
	centered(wide.children[0].rect);
	for (const item of [full.children[1], wide.children[1], ordinary.children[0], ordinary.children[1], mixed.children[1], mixed.children[2]]) {
		close(item.rect.left, measurements.ordinary.left);
		close(item.rect.width, measurements.ordinary.width);
	}
	close(nestedDirect.outer.left, measurements.ordinary.left);
	close(nestedDirect.outer.width, measurements.ordinary.width);
	close(measurements.constrained.child.left, measurements.canvas.left);
	close(measurements.constrained.child.right, measurements.canvas.right);
	for (const item of [measurements.constrained.paragraph, measurements.constrained.image, measurements.constrained.nested]) {
		close(item.left, measurements.ordinary.left);
		close(item.width, measurements.ordinary.width);
	}
	expect(measurements.constrainedWide.child.width).toBeGreaterThan(measurements.ordinary.width);
	centered(measurements.constrainedWide.child);
	close(measurements.ordinaryFull.left, measurements.canvas.left);
	centered(measurements.ordinaryWide);
});
