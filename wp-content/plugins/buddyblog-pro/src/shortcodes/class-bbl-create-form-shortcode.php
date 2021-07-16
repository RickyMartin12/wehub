<?php
/**
 * Shortcode for creating post.
 *
 * @package    BuddyBlog_Pro
 * @subpackage Shortcodes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Shortcodes;

// Do not allow direct access over web.
use BuddyBlog_Pro\Handlers\View_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcode_Loader
 */
class BBL_Create_Form_Shortcode {

	/**
	 * Singleton instance.
	 *
	 * @var BBL_Create_Form_Shortcode
	 */
	private static $instance = null;

	private function __constructor() {

	}

	/**
	 * Boot class
	 */
	public static function boot() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	private function setup() {
		add_shortcode( 'bbl-create-by-post-type', array( $this, 'post_type_shortcode' ) );
		add_shortcode( 'bbl-create-form', array( $this, 'form_shortcode' ) );
	}

	public function post_type_shortcode( $atts = array(), $content = '' ) {
		$atts = shortcode_atts( array(
			'post_type' => '',
			'post_id'   => 0,
		),
			$atts );

		if ( empty( $atts['post_type'] ) ) {
			return __( 'Invalid form.', 'buddyblog-pro' );
		}

		$atts['form_id'] = bblpro_get_form_for_post_type( $atts['post_type'] );

		return $this->form_shortcode( $atts, $content );
	}

	public function form_shortcode( $atts = array(), $content = '' ) {

		$atts = shortcode_atts( array(
			'form_id' => 0,
			'post_id' => 0,
		),
			$atts );

		if ( empty( $atts['form_id'] ) ) {
			return '';
		}

		$form = bblpro_get_form( $atts['form_id'] );

		if ( ! empty( $atts['post_id'] ) ) {
			$post_id = absint( $atts['post_id'] );
		} elseif ( isset( $_GET['bbl_post_id'] ) ) {
			$post_id = intval( $_GET['bbl_post_id'] );
		} else {
			$post_id = 0;
		}
		$post = null;
		if ( $post_id ) {
			$post = get_post( $post_id );
		}

		if ( ! $form ) {
			return __( 'Invalid form.', 'buddyblog-pro' );
		}

		$post_type = bblpro_form_get_post_type( $form->ID );

		$user_id = get_current_user_id();
		$errors  = new \WP_Error();

		$is_editing = false;

		// Permissions check.
		if ( $post ) {
			if ( bblpro_form_get_post_type( $form->ID ) !== get_post_type( $post ) ) {
				$errors->add( 'bbl_editor_fail', __( "Editing for this content type is not enabled.", 'buddyblog-pro' ) );
			} elseif ( ! bblpro_user_can_edit_post( $user_id, $post->ID ) ) {
				$errors->add( 'bbl_auth_fail', __( "You don't have the permissions to edit this post.", 'buddyblog-pro' ) );
			} else {
				$is_editing = true;
				View_Helper::instance()->process( $post_type, 'edit', $post->ID );
			}
		} else {
			if ( ! bblpro_user_can_create_post( get_current_user_id(), $post_type ) ) {
				$errors->add( 'bbl_auth_fail', __( 'Not allowed', 'buddyblog-pro' ) );
			} else {

				View_Helper::instance()->process( $post_type, 'create' );
			}
		}

		if ( $errors->has_errors() ) {
			return $errors->get_error_message();
		}

		$type_class = $is_editing ? 'bbl-form-edit-shortcode' : 'bbl-form-create-shortcode';
		ob_start();
		echo "<div class='bbl-form-shortcode {$type_class}'>";
		if ( $is_editing ) {
			bblpro_get_template_part( $post_type, 'edit', 'shortcode' );
		} else {
			bblpro_get_template_part( $post_type, 'create', 'shortcode' );
		}
		echo '</div>';

		//bblpro_render_form( $form, $post );

		return ob_get_clean();
	}
}
