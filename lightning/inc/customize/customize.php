<?php



/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register' );
function lightning_customize_register( $wp_customize ) {

	/*	Add text control description
	/*-------------------------------------------*/
	class Custom_Text_Control extends WP_Customize_Control {
		public $type         = 'customtext';
		public $description  = ''; // we add this for the extra description
		public $input_before = '';
		public $input_after  = '';
		public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php $style = ( $this->input_before || $this->input_after ) ? ' style="width:50%"' : ''; ?>
			<div>
			<?php echo wp_kses_post( $this->input_before ); ?>
			<input type="text" value="<?php echo esc_attr( $this->value() ); ?>"<?php echo $style; ?> <?php $this->link(); ?> />
			<?php echo wp_kses_post( $this->input_after ); ?>
			</div>
			<div><?php echo $this->description; ?></div>
		</label>
		<?php
		} // public function render_content() {
	} // class Custom_Text_Control extends WP_Customize_Control

	/*-------------------------------------------*/
	/*	Lightning Panel
	/*-------------------------------------------*/
	// $wp_customize->add_panel( 'lightning_setting', array(
	//    	'priority'       => 25,
	//    	'capability'     => 'edit_theme_options',
	//    	'theme_supports' => '',
	//    	'title'          => __( 'Lightning settings', 'lightning' ),
	// ));
}
