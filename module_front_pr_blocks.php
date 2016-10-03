<?php 
global $lightning_theme_options;
$options = $lightning_theme_options;
if ( !isset( $options['front_pr_hidden'] ) || !$options['front_pr_hidden'] ){
	echo '<section class="widget">';
	echo '<div class="veu_prBlocks row">';
	for ( $i = 1; $i <= 3; ) {
		echo '<article class="prArea col-sm-4">';

		$options_array = array('icon','title','description','link');
		foreach ($options_array as $key => $value) {
			if ( !isset( $options['front_pr_'.$value.'_'.$i] ) ){
				$options['front_pr_'.$value.'_'.$i] = '';
			}
		}

		if ( $options['front_pr_link_'.$i] ) echo '<a href="'.esc_url( $options['front_pr_link_'.$i] ).'">';

		if ( $options['front_pr_icon_'.$i] ) {
			echo '<div class="circle_icon" style="background-color:'.esc_attr( $options['color_key'] ).'">';
			echo '<i class="fa '.$options['front_pr_icon_'.$i].' font_icon"></i>';
			echo '</div>';
		}
		
		echo '<h1 class="prBox_title">'.esc_html( $options['front_pr_title_'.$i] ).'</h1>';
		echo '<p class="summary">'.esc_html( $options['front_pr_description_'.$i] ).'</p>';

		if ( $options['front_pr_link_'.$i] ) echo '</a>';
		echo '</article><!--//.prArea -->'."\n";

		$i++;
	}
	echo '</div>';
	echo '</section>';
}