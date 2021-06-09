/**
 * wp-embed-shareボタンをクリックしたらクリップボードにURLをコピーする
 */
function vk_wp_oembed_blog_card_copy_url(target) {
  var url = target;
  navigator.clipboard.writeText(url);
}