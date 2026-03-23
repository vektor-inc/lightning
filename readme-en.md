![Build Check](https://github.com/vektor-inc/Lightning/workflows/Build%20Check/badge.svg)

# WordPress Theme "Lightning"

[English](./readme-en.md) | [日本語](./readme.md)

Lightning is a very simple & easy to customize theme based on Bootstrap. It is highly compatible with custom post types and custom taxonomies. When you add a new one, breadcrumbs are automatically adjusted and posts look beautiful without needing to edit or add template files.

* [Official Website](http://lightning.vektor-inc.co.jp/)
* [WordPress.org](https://wordpress.org/themes/lightning/)
* [GitHub](https://github.com/vektor-inc/lightning)

---

## Commands

### Sass Watch

```bash
composer install
npm install
npx gulp
```

### Build

Builds all JS, SCSS, and text domains.
```bash
$ npm run build
```

#### JS Build
```bash
$ npm run build:script
```

#### Text Domain Rewrite
```bash
$ npm run build:text-domain
```

### Development Mode

#### JS
```bash
$ npm run watch:script
```

### Create Distribution (dist)

```bash
$ npm run dist
```

A zip file for importing via the dashboard and a theme directory for transfer will be created in the `dist/` folder.

## Lightning G3 ./_g3/

G3 has separate documentation. Please refer to it here:
[Please check ./_g3/readme.md](https://github.com/vektor-inc/lightning/blob/master/_g3/readme.md)

---

## Unit Test

This theme includes PHP Unit Tests. Run the following commands to execute them.

*Note: For Mac users, using [docker-sync](https://github.com/EugenMayer/docker-sync) is recommended.*

```shell
$ docker-compose run wp
```

### Unit Test on wp-env
1. Ensure Docker is installed.
2. Install npm scripts:
   `npm install`
   `npm install -g @wordpress/env`
3. Start wp-env:
   `wp-env start`
4. Install Composer dependencies:
   - **Windows:** `npm run composer:install:win`
   - **Mac:** `npm run composer:install:mac`
5. Start the Unit Test:
   - **Windows:** `npm run phpunit:win`
   - **Mac:** `npm run phpunit`

---

## E2E Testing

### Running Tests

To run all tests:
If your test URL is something other than `localhost:8889`, please update the target URL in `./tests/e2e/` to match your environment.

```bash
npm install
wp-env start
npx playwright test
```

To run only on Chromium:
```bash
npx playwright test --project=chromium
```

To see screenshots of operations (trace mode):
```bash
npx playwright test --project=chromium --trace on
```

### Viewing Reports

```bash
npx playwright show-report
```

### Adding Tests

For example, to create a test for the WordPress login screen:
```bash
npx playwright codegen "http://localhost:8889/wp-login.php"
```
Copy the relevant parts of the generated code into `tests/e2e/spec.js`.

---

## Pull Request Guidelines

#### Do not include multiple changes
If you send a single pull request containing unrelated changes (e.g., a bug fix combined with a new action hook), it becomes very difficult to review and merge. Please submit separate PRs for different purposes.

#### Provide a brief summary
* What is the purpose of the change and what was modified?
* Please mention if any commands used during development have changed.

#### Include testing steps
Provide a brief outline of how to verify the change (e.g., "Go to this screen, configure these settings, and check if it behaves like this").

#### Did you write test code?
Because there are many combinations of settings, it is easy for testers (and even the author) to forget the intended behavior over time. Please write PHPUnit tests whenever possible.

#### Is the UI user-friendly?
* Put yourself in the user's shoes: Is this the best UI?
* Try to make the interface self-explanatory so users don't need to read documentation.
* Maintain UI consistency for similar input types.

#### Does it affect existing users?
* Be careful not to break the display of existing sites after an update. Include compatibility handling where necessary.

#### Is there reusable code?
Check for duplicate code. If parts of the logic are similar, refactor them into a common class or function using arguments.

#### Are function and variable names appropriate?
Aim for names that make it easy for a third party to understand the logic and content.

#### Are HTML class names appropriate?
Follow the naming conventions of the theme/plugin. If not explicitly documented, refer to existing classes to ensure consistency in meaning and context.

#### Fix library files at the source
Libraries are typically copied to projects and then text-domains are replaced. If you fix a library file within the Lightning project directly, it is difficult to sync back.
Please run the gulp watch in the "parent" library, commit there first, and then sync to the project. Otherwise, your changes may be overwritten during the next library update.

---

## Customize panel priority

```php
$wp_customize->add_section(
```

* 400 | License key
* 450 | Function Settings
* 501 | Design Settings
* 502 | Font Settings
* 510 | Header Upper Settings
* 511 | Header Settings
* 513 | Campaign Text Settings
* 520 | Homepage Slideshow Settings
* 521 | Homepage PR Block Settings
* 530 | Page Header Settings
* 532 | Layout Settings
* 535 | Archive Page Settings
* 536 | Archive Page Layout
* 538 | Single Page Settings
* 540 | Footer Settings
* 543 | Copyright Settings
* 550 | Mobile Navigation
* 551 | Mobile Fixed Navigation
* 555 | Widget Area Settings (Planned to merge with Footer)
* 556 | Google Tag Manager
* 560 | Font Awesome

---

### Design Skins

Lightning allows you to switch design skins externally. If you wish to create a custom skin, please refer to this sample:

https://github.com/vektor-inc/lightning-g3-skin-sample

---

### CSS Loading Order (Reference Memo)

| Loading Point | Priority | File | Notes |
| ---- | ---- | ---- | ---- |
| wp_enqueue_scripts | | vkExUnit_common_style-css | |
| wp_enqueue_scripts | | vkExUnit_common_style-inline-css | |
| wp_enqueue_scripts | | Bootstrap | |
| wp_enqueue_scripts | | lightning-common-style | Common to all skins |
| wp_enqueue_scripts | | lightning-design-style | Design Skin |
| wp_enqueue_scripts | | lightning-design-style wp_add_inline_style | Design Skin |
| wp_enqueue_scripts | | lightning-theme-style | Must be late for child theme overrides |
| wp_enqueue_scripts | | vk-font-awesome-css | |
| wp_head | 50 | HeaderColorManager colors | Set via Customizer |
| wp_head | 200 | ExUnit CSS Customization (Common) | |
| wp_head | 201 | ExUnit CSS Customization (Posts) | |