/**
 * クリップボードにURLをコピーする
 */
function vk_wp_oembed_blog_card_copy_url(a) {
  var url = a;
  navigator.clipboard.writeText(url);
}