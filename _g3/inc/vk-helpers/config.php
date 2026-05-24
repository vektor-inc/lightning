<?php
/**
 * Backward-compat stub.
 *
 * The vk-helpers package has been migrated to the composer-managed
 * `vektor-inc/vk-helpers` (loaded via `vendor/autoload.php` in
 * `functions.php`). The previous bootstrapper at this path (config.php
 * which required `package/class-vk-helpers.php`) is no longer needed.
 * This stub file remains so that legacy user code that `require`s this
 * exact path does not produce a fatal error.
 *
 * vk-helpers パッケージは composer 管理の vektor-inc/vk-helpers に移行済み
 * （functions.php の vendor/autoload.php で読み込まれる）。旧 bootstrapper
 * （class-vk-helpers.php を require していた config.php）は不要になったが、
 * ユーザー独自コードがこのパスを直接 require している場合に Fatal Error と
 * ならないように、空スタブとしてファイル自体は残す。
 *
 * @package Lightning
 * @see https://github.com/vektor-inc/lightning/issues/1343
 */
