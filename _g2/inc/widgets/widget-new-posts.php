<?php
/**
 * Lightning
 *
 * @package vektor-inc/lightning
 */

/*
  Side Post list widget
/*-------------------------------------------*/
class WP_Widget_ltg_post_list extends WP_Widget {

	public $taxonomies = array( 'category' );

	function __construct() {

		$widget_name = lightning_get_prefix() . __( 'Content Area Posts Widget', 'lightning' );

		parent::__construct(
			'ltg_post_list',
			$widget_name,
			array( 'description' => __( 'Displays a list of your most recent posts.', 'lightning' ) )
		);
	}

	/*
	 More Link
	/*-------------------------------------------*/
	public static function more_link_html( $instance ) {
		if ( ! empty( $instance['more_text'] ) && ! empty( $instance['more_url'] ) ) {
			$more_link_html  = '<div class="text-right" style="margin-top:1em;">';
			$more_link_html .= '<a href="' . esc_url( $instance['more_url'] ) . '" class="btn btn-default btn-xs">' . wp_kses_post( $instance['more_text'] ) . '</a>';
			$more_link_html .= '</div>';
		} else {
			$more_link_html = '';
		}
		return $more_link_html;
	}

	function widget( $args, $instance ) {
		global $is_contentsarea_posts_widget;
		$is_contentsarea_posts_widget = true;
		if ( ! isset( $instance['format'] ) ) {
			$instance['format'] = 0; }

		echo $args['before_widget'];
		echo '<div class="pt_' . $instance['format'] . '">';
		if ( ! empty( $instance['label'] ) ) {
			echo $args['before_title'];
			echo $instance['label'];
			echo $args['after_title'];
		} elseif ( ! isset( $instance['label'] ) ) {
			echo $args['before_title'];
			echo __( 'Recent Posts', 'lightning' );
			echo $args['after_title'];
		}

		$count     = ( isset( $instance['count'] ) && $instance['count'] ) ? $instance['count'] : 10;
		$post_type = ( isset( $instance['post_type'] ) && $instance['post_type'] ) ? $instance['post_type'] : 'post';

		if ( $instance['format'] ) {
			$this->_taxonomy_init( $post_type );
		}

		$p_args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $count,
			'paged'          => 1,
		);

		if ( isset( $instance['terms'] ) && $instance['terms'] ) {
			$taxonomies          = get_taxonomies( array() );
			$p_args['tax_query'] = array(
				'relation' => 'OR',
			);
			$terms_array         = explode( ',', $instance['terms'] );
			foreach ( $taxonomies as $taxonomy ) {
				$p_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $terms_array,
				);
			}
		}
		global $wp_query;
		$wp_query = new WP_Query( $p_args );

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				if ( ! $instance['format'] ) {
					/**
					 * Dealing with old files
					 * Actually, it's ok to only use get_template_part().
					 * It is measure for before version 7.0 that loaded module_loop_***.php.
					 */
					$templates    = array();
					$templates[]  = 'module_loop_' . $post_type . '.php';
					$require_once = false;
					if ( locate_template( $templates, false, $require_once ) ) {
						locate_template( $templates, true, $require_once );
					} else {
						get_template_part( 'template-parts/post/loop', $post_type );
					}
				} elseif ( $instance['format'] == 1 ) {
					get_template_part( 'template-parts/post/article' );
				}
			endwhile;
		endif;

		echo $this->more_link_html( $instance );

		echo '</div>';
		echo $args['after_widget'];

		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)


	function _taxonomy_init( $post_type ) {
		if ( $post_type == 'post' ) {
			return; }
		$this->taxonomies = get_object_taxonomies( $post_type );
	}

	function taxonomy_list( $post_id = 0, $before = ' ', $sep = ',', $after = '' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID(); }

		$taxo_catelist = array();

		foreach ( $this->taxonomies as $taxonomy ) {
			$terms = get_the_term_list( $post_id, $taxonomy, $before, $sep, $after );
			if ( $terms ) {
				$taxo_catelist[] = $terms; }
		}

		if ( count( $taxo_catelist ) ) {
			return join( $taxo_catelist, $sep ); }
		return '';
	}

	function form( $instance ) {
		$defaults = array(
			'count'     => 10,
			'label'     => __( 'Recent Posts', 'lightning' ),
			'post_type' => 'post',
			'terms'     => '',
			'format'    => '0',
			'more_url'  => '',
			'more_text' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<br/>
		<?php echo _e( 'Display Format', 'lightning' ); ?>:<br/>
		<ul>
			<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="0"
						<?php
						if ( $instance['format'] == 0 ) {
							echo 'checked'; }
						?>
					/><?php echo __( 'Thumbnail', 'lightning' ) . '/' . __( 'Date', 'lightning' ) . '/' . __( 'Category', 'lightning' ) . '/' . __( 'Title', 'lightning' ) . '/' . __( 'Excerpt', 'lightning' ); ?></label>
			</li>
			<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' ); ?>" value="1"
						<?php
						if ( $instance['format'] == 1 ) {
							echo 'checked'; }
						?>
					/><?php echo __( 'Date', 'lightning' ) . '/' . __( 'Category', 'lightning' ) . '/' . __( 'Title', 'lightning' ) . '/' . __( 'Content', 'lightning' ); ?></label>
			</li>
		</ul>
		<br/>
		<?php // Title ?>
		<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Title:', 'lightning' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
		<br/><br />

		<?php // Post Cpunt ?>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Display count', 'lightning' ); ?>:</label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" />
		<br /><br />

		<?php // Post Type ?>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Slug for the custom type you want to display', 'lightning' ); ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" value="<?php echo esc_attr( $instance['post_type'] ); ?>" />
		<br/><br/>

		<?php // Terms ?>
		<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'taxonomy ID', 'lightning' ); ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo esc_attr( $instance['terms'] ); ?>" /><br />
		<?php
		_e( 'if you need filtering by term, add the term ID separate by ",".', 'lightning' );
		echo '<br/>';
		_e( 'if empty this area, I will do not filtering.', 'lightning' );
		echo '<br/><br/>';
		?>

		<?php // Read more ?>
		<label for="<?php echo $this->get_field_id( 'more_url' ); ?>"><?php _e( 'Destination URL:', 'lightning' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo esc_attr( $instance['more_url'] ); ?>" />
		<br /><br />
		<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'Notation text:', 'lightning' ); ?></label><br/>
		<input type="text" placeholder="<?php _e( 'Latest post list', 'lightning' ); ?>" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		<br /><br />
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['format']    = $new_instance['format'];
		$instance['count']     = $new_instance['count'];
		$instance['label']     = $new_instance['label'];
		$instance['post_type'] = ! empty( $new_instance['post_type'] ) ? strip_tags( $new_instance['post_type'] ) : 'post';
		$instance['terms']     = preg_replace( '/([^0-9,]+)/', '', $new_instance['terms'] );
		$instance['more_url']  = $new_instance['more_url'];
		$instance['more_text'] = $new_instance['more_text'];
		return $instance;
	}
}

add_action( 'widgets_init', 'lightning_unit_widget_register_post_list' );
function lightning_unit_widget_register_post_list() {
	return register_widget( 'WP_Widget_ltg_post_list' );
}
