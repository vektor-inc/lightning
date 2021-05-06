<?php
/**
 * ブログカード
*/

/**
* WordPressで作られたサイト用
* 埋め込み用のフィルターフックoembed_dataparse
* esc_url_raw は & などがはいってもエスケープさせないため
*/
function wrap_oembed_dataparse($output) {
  if ( preg_match('/<blockquote class="wp-embedded-content".*?><a href="(.+?)"/i', $output, $match) !== 1 ) 
  return $output;
  $url = esc_url_raw($match[1]);
  $content = get_blog_card($url);
  return $content;
}
add_filter( 'oembed_dataparse', 'wrap_oembed_dataparse' );

/**
 * 外部リンク用
 * URLから自動でブログカード化外部リンク用
 * the_contentからURLを探して置き換え
 */
function external_link_embed_content($content) {
  $res = preg_match_all('/^(<p>)?(<a[^>]+?>)?https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+(<\/a>)?(?!.*<br *\/?>).*?(<\/p>)?/im', $content, $matches);
  if ( empty($res) ) {
    return $content;
  }
  foreach ($matches[0] as $match) {
    $url = esc_url_raw( strip_tags($match) );
    $content = preg_replace('{^'.preg_quote($match, '{}').'}im', get_blog_card($url), $content, 1);
  }
  return $content;
}
add_filter('the_content', 'external_link_embed_content' );

/*
wp_remote_getで外部リンク、WordPress以外のサイトでも取得出来る形にする
*/
function get_blog_card($url) {
  // $url = "https://www.vektor-inc.co.jp/";
  $response = wp_remote_get( $url );
  // URLのHTMLを$bodyに入れる
  $body = $response['body'];

  //正規表現でマッチするものを取得する 後に関数化
  if ( preg_match( '/<title>(.+?)<\/title>/is', $body, $matches ) ) {
    $title = $matches[1];
  }

  if ( preg_match( '/<meta.+?property=["\']og:image["\'][^\/>]*?content=["\']([^"\']+?)["\'].*?\/?>/is', $body, $matches ) ) {
    $og_image = $matches[1];
  } else {
    $og_image = $matches[1];
  }

  if ( preg_match( '/<meta.+?property=["\']og:description["\'][^\/>]*?content=["\']([^"\']+?)["\'].*?\/?>/is', $body, $matches ) ) {
    $og_description = $matches[1];
  }

  if ( preg_match( '/<link [^>]*?rel=["\']icon["\'][^\/>]*? href=["\']([^"\']+?)["\'][^\/>]*?\/?>/si', $body, $matches ) ) {
    $favicon = $matches[1];
  }
  
  /*
  スタイルは一旦Horizontalカード
  https://getbootstrap.jp/docs/4.2/components/card/#horizontal
  */
  $content = <<<EOF
  <div class="card mb-3">
    <a href="$url">
      <div class="row no-gutters">
        <div class="col-md-4">
          <img class="bd-placeholder-img" width="486" height="290" src="$og_image" alt="">
        </div>
        <div class="col-md-8">
          <div class="card-body">
            <h5 class="card-title">$title</h5>
            <p class="card-text">$og_description</p>
          </div>
        </div>
      </div>
    </a>
  </div>
EOF;
  return $content;
}
