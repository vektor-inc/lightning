<?php
/**
 * Sidebar for page
 *
 * This file is sidebar fot page.
 * But, if the widget or block is placed in the sitebar widget area (page),
 * This file will not be read.
 *
 * 投稿タイプ page 用のサイドバーです。
 * しかし、サイトバーウィジェットエリア（固定ページ）にウィジェットかブロックが配置されている場合、
 * このファイルは読み込まれなくなります。
 *
 * @package vektor-inc/lightning
 */

 if ( function_exists( 'Lightning_customize_widget_area_alert' )) {
	Lightning_customize_widget_area_alert( 'page' );
 }

if ( $post->ancestors ) {
	foreach ( $post->ancestors as $post_anc_id ) {
		$ancestor_post_id = $post_anc_id;
	}
} else {
	$ancestor_post_id = $post->ID;
}

if ( $ancestor_post_id ) {
	$children = wp_list_pages( 'title_li=&child_of=' . $ancestor_post_id . '&echo=0' );
	if ( $children ) { ?>
			<aside class="widget widget_link_list">
			<h4 class="sub-section-title"><a href="<?php echo esc_url( get_permalink( $ancestor_post_id ) ); ?>"><?php echo get_the_title( $ancestor_post_id ); ?></a></h4>
			<ul>
			<?php echo $children; ?>
			</ul>
			</aside>
		<?php
	}
}
