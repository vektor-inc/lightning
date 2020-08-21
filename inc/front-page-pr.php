<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_front_pr_blocks_customize_register' );
function lightning_front_pr_blocks_customize_register( $wp_customize ) {

	/*-------------------------------------------*/
	/*	Front PR
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'lightning_front_pr', array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Front Page PR Block', 'lightning' ),
			'priority' => 521,
		// 'panel'				=> 'lightning_setting',
		)
	);

	$wp_customize->add_setting(
		'lightning_theme_options[front_pr_display]', array(
			'default'           => true,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);

	// Add control
	$wp_customize->add_control(
		'front_pr_display', array(
			'label'       => __( 'Display Front Page PR Block', 'lightning' ),
			'section'     => 'lightning_front_pr',
			'settings'    => 'lightning_theme_options[front_pr_display]',
			'type'        => 'checkbox',
			'priority'    => 1,
			'description' => __( '* If you want to use the more advanced features and set a PR Block anywhere, please Install the WordPress official directory registration plug-in "VK All in One Expansion Unit (Free)" and use the "VK PR Blocks widgets". ', 'lightning' ),
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[front_pr_display]', array(
			'selector'        => '.home .prBlocks-default',
			'render_callback' => '',
		)
	);

	$front_pr_default = lighting_front_pr_default_array();

	$priority = 1;
	for ( $i = 1; $i <= 3; ) {
		$wp_customize->add_setting(
			'lightning_theme_options[front_pr_icon_' . $i . ']', array(
				'default'           => $front_pr_default['icon'][ $i ],
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_setting(
			'lightning_theme_options[front_pr_title_' . $i . ']', array(
				'default'           => $front_pr_default['title'][ $i ],
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_setting(
			'lightning_theme_options[front_pr_summary_' . $i . ']', array(
				'default'           => $front_pr_default['summary'][ $i ],
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_setting(
			'lightning_theme_options[front_pr_link_' . $i . ']', array(
				'default'           => $front_pr_default['link'][ $i ],
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		// Add control
		$priority ++;

		$description = '';
		if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
			$description = Vk_Font_Awesome_Versions::ex_and_link();
		}

		$wp_customize->add_control(
			new Custom_Text_Control(
				$wp_customize,
				'front_pr_icon_' . $i,
				array(
					'label'       => __( 'Icon ', 'lightning' ) . ' ' . $i,
					'section'     => 'lightning_front_pr',
					'settings'    => 'lightning_theme_options[front_pr_icon_' . $i . ']',
					'type'        => 'text',
					'description' => $description,
					'priority'    => $priority,
				)
			)
		);

		$wp_customize->add_control(
			'front_pr_title_' . $i,
			array(
				'label'    => __( 'PR Title', 'lightning' ) . ' ' . $i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_title_' . $i . ']',
				'type'     => 'text',
				'priority' => $priority,
			)
		);

		$wp_customize->add_control(
			'front_pr_summary_' . $i,
			array(
				'label'    => __( 'PR Text', 'lightning' ) . ' ' . $i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_summary_' . $i . ']',
				'type'     => 'textarea',
				'priority' => $priority,
			)
		);

		$wp_customize->add_control(
			'front_pr_link_' . $i,
			array(
				'label'    => __( 'PR Link URL', 'lightning' ) . ' ' . $i,
				'section'  => 'lightning_front_pr',
				'settings' => 'lightning_theme_options[front_pr_link_' . $i . ']',
				'type'     => 'text',
				'priority' => $priority,
			)
		);

		$i++;
	}
}

function lightning_front_pr_blocks_styles() {

	global $lightning_theme_options;
	$options = $lightning_theme_options;
	if ( isset( $options['front_pr_display'] ) && $options['front_pr_display'] ) {
		if ( isset( $options['color_key'] ) && $options['color_key'] ) {
			$color_key = $options['color_key'];
		} else {
			$color_key = '#337ab7';
		}
		$custom_css = "
			.prBlock_icon_outer { border:1px solid {$color_key}; }
			.prBlock_icon { color:{$color_key}; }
		";
		wp_add_inline_style( 'lightning-theme-style', $custom_css );
	}
}
add_action( 'wp_enqueue_scripts', 'lightning_front_pr_blocks_styles' );

function lighting_front_pr_default_array() {
	$front_pr_default = array(
		'icon'    => array(
			1 => 'fas fa-check',
			2 => 'fas fa-cogs',
			3 => 'far fa-file-alt',
		),
		'title'   => array(
			1 => __( 'For all purposes', 'lightning' ),
			2 => __( 'Powerful features', 'lightning' ),
			3 => __( 'Surprisingly easy', 'lightning' ),
		),
		'summary' => array(
			1 => __( 'Lightning is a simple and customize easy theme. It corresponds by the outstanding versatility to the all-round also in business sites and blogs.', 'lightning' ),
			2 => __( 'By using the plug-in "VK All in One Expansion Unit (free)", you can use the various functions and rich widgets.', 'lightning' ),
			3 => __( 'Lightning is includes to a variety of ideas for making it easier to business site. Please experience the ease of use of the Lightning.', 'lightning' ),
		),
		'link'    => array(
			1 => esc_url( home_url() ),
			2 => esc_url( home_url() ),
			3 => esc_url( home_url() ),
		),
	);
	return $front_pr_default;
}


add_action( 'lightning_home_content_top_widget_area_before', 'lightning_front_pr_blocks_add' );
function lightning_front_pr_blocks_add() {
	global $lightning_theme_options;
	$options = $lightning_theme_options;
	/*
	!isset( $options['front_pr_display'] ) ... Users from conventional / New install
	$options['front_pr_display'] ... User setted hidden
	*/
	if ( isset( $options['front_pr_display'] ) && $options['front_pr_display'] ) {
		echo '<div class="widget">';
		echo '<div class="prBlocks prBlocks-default row">';

		if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
			$fa = Vk_Font_Awesome_Versions::print_fa();
		}

		for ( $i = 1; $i <= 3; ) {

			echo '<article class="prBlock prBlock_lighnt col-sm-4">';

			/* isset */
			$front_pr_default = lighting_front_pr_default_array();
			$options_array    = array( 'icon', 'title', 'summary', 'link' );
			foreach ( $options_array as $key => $value ) {
				if ( ! isset( $options[ 'front_pr_' . $value . '_' . $i ] ) ) {
					$options[ 'front_pr_' . $value . '_' . $i ] = $front_pr_default[ $value ][ $i ];
				}
			}

			if ( $options[ 'front_pr_link_' . $i ] ) {
				echo '<a href="' . esc_url( $options[ 'front_pr_link_' . $i ] ) . '">';
			}

			if ( $options[ 'front_pr_icon_' . $i ] ) {
				// echo '<div class="prBlock_icon" style="background-color:'.esc_attr( $options['color_key'] ).'">';
				echo '<div class="prBlock_icon_outer">';
				echo '<i class="' . $fa . $options[ 'front_pr_icon_' . $i ] . ' font_icon prBlock_icon"></i>';
				echo '</div>';
			}

			if ( $options[ 'front_pr_title_' . $i ] ) {
				echo '<h1 class="prBlock_title">' . esc_html( $options[ 'front_pr_title_' . $i ] ) . '</h1>';
			}

			if ( $options[ 'front_pr_summary_' . $i ] ) {
				echo '<p class="prBlock_summary">' . esc_html( $options[ 'front_pr_summary_' . $i ] ) . '</p>';
			}

			if ( $options[ 'front_pr_link_' . $i ] ) {
				echo '</a>';
			}

			echo '</article><!--//.prBlock -->' . "\n";

			$i++;
		}
		echo '</div>';
		echo '</div>';
	}
}
