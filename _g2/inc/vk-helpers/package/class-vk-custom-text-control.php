<?php
/**
 * VK Custom Text Control
 *
 * @package VK Helpers
 */

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。
修正の際は上記リポジトリのデータを修正してください。
編集権限を持っていない方で何か修正要望などありましたら
各プラグインのリポジトリにプルリクエストで結構です。
*/

if ( ! class_exists( 'VK_Custom_Text_Control' ) ) {
	/**
	 * VK Custom_Text_Control
	 */
	class VK_Custom_Text_Control extends WP_Customize_Control {
		public $type         = 'customtext';
		public $description  = ''; // we add this for the extra description
		public $input_before = '';
		public $input_after  = '';
		/**
		 * Render Content
		 */
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
	}
}


