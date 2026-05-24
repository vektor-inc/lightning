<?php
/**
 * Backward-compat stub.
 *
 * The `VK_Helpers` class has been migrated to the composer-managed package
 * `vektor-inc/vk-helpers` (loaded via `vendor/autoload.php` in
 * `functions.php`). The global `VK_Helpers` name is now provided as a
 * subclass of `VektorInc\VK_Helpers\VkHelpers` declared in
 * `_g2/functions.php` / `_g3/functions.php`. This stub file remains so that
 * legacy user code that `require`s this exact path does not produce a
 * fatal error.
 *
 * VK_Helpers クラスは composer 管理の vektor-inc/vk-helpers に移行済み
 * （functions.php の vendor/autoload.php で読み込まれる）。グローバル名の
 * VK_Helpers は _g2/functions.php / _g3/functions.php で
 * VektorInc\VK_Helpers\VkHelpers の子クラスとして提供される。
 * ユーザー独自コードがこのパスを直接 require している場合に Fatal Error と
 * ならないように、空スタブとしてファイル自体は残す。
 *
 * @package Lightning
 * @see https://github.com/vektor-inc/lightning/issues/1343
 */
