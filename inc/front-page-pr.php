<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_top_pr_customize_register' );
function lightning_top_pr_customize_register($wp_customize) {

	/*-------------------------------------------*/
	/*	Front PR
	/*-------------------------------------------*/
	$wp_customize->add_section( 'lightning_front_pr', array(
		'title'				=> _x('Lightning Front Page PR Block', 'lightning theme-customizer', 'lightning'),
		'priority'			=> 700,
		// 'panel'				=> 'lightning_setting',
	) );

	$wp_customize->add_setting('lightning_theme_options[front_pr_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	) );

	// Add control
	$wp_customize->add_control( 'front_pr_hidden', array(
		'label'     => _x('Hide Front Page PR Block', 'lightning theme-customizer', 'lightning'),
		'section'  => 'lightning_front_pr',
		'settings' => 'lightning_theme_options[front_pr_hidden]',
		'type' => 'checkbox',
		'priority' => 1,
		'description' => __('※PR Blockを好きな場所に設定したり、より高度な機能を使いたい場合は、WordPress公式ディレクトリ登録プラグイン「VK All in One Expantion Unit（無料）」をインストール・有効化して、ウィジェット「VK PR Blocks」をご利用ください。', 'lightning'),
	) );


	$front_pr_default = array(
		'icon' => array(
			1 => '',
			2 => '',
			3 => ''
			),
		'title' => array(
			1 => '',
			2 => '',
			3 => ''
			),
		'description' => array(
			1 => '',
			2 => '',
			3 => ''
			),
		'link' => array(
			1 => '',
			2 => '',
			3 => ''
			),
		);

	$priority = 1;
	for ( $i = 1; $i <= 3; ) {
		$wp_customize->add_setting('lightning_theme_options[front_pr_icon_'.$i.']', array(
			'default'			=> $front_pr_default['icon'][$i],
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_setting('lightning_theme_options[front_pr_title_'.$i.']', array(
			'default'			=> $front_pr_default['title'][$i],
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_setting('lightning_theme_options[front_pr_description_'.$i.']', array(
			'default'			=> $front_pr_default['description'][$i],
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_setting('lightning_theme_options[front_pr_link_'.$i.']', array(
			'default'			=> $front_pr_default['link'][$i],
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		) );
		// Add control
		$priority ++;

		$wp_customize->add_control( new Custom_Text_Control( 
			$wp_customize, 
			'front_pr_icon_'.$i, 
			array(
				'label'    => _x('Icon ', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_icon_'.$i.']',
				'type' => 'text',
				'description' => 'Ex : fa-file-text-o [ <a href="http://fontawesome.io/icons/" target="_blank">Icon list</a> ]',
				'priority' => $priority,
			)
		) );

		$wp_customize->add_control(  
			'front_pr_title_'.$i, 
			array(
				'label'    => _x('Title', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_title_'.$i.']',
				'type' => 'text',
				'priority' => $priority,
			)
		);

		$wp_customize->add_control(  
			'front_pr_description_'.$i, 
			array(
				'label'    => _x('Text', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_description_'.$i.']',
				'type' => 'textarea',
				'priority' => $priority,
			)
		);

		$wp_customize->add_control(  
			'front_pr_link_'.$i, 
			array(
				'label'    => _x('Link URL', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_link_'.$i.']',
				'type' => 'text',
				'priority' => $priority,
			)
		);

		$i++;
	}
}

add_action( 'lightning_home_content_top_widget_area_before', 'lightning_add_front_pr_blocks' );
function lightning_add_front_pr_blocks(){
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
}

