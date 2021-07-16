<?php
/**
 * Ajax Post thumbnail handler
 *
 * @package    BuddyBlog_Pro
 * @subpackage Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Handlers;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handles setting of post thumbnail.
 */
class Ajax_Post_Thumbnail_Handler {

	/**
	 * Boot itself
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {
		add_action( 'wp_ajax_get_post_thumbnail_html', array( $this, 'get_post_thumbnail' ), 5 );
	}

	/**
	 * Post thumbnail via ajax.
	 */
	public function get_post_thumbnail() {

		if ( empty( $_POST['bbl_thumbnail_context'] ) || empty( $_POST['post_id'] ) ) {
			return;// not from us. let the core handle it.
		}

		$post_id = intval( wp_unslash( $_POST['post_id'] ) );

		check_ajax_referer( "update-post_$post_id" );

		if ( ! bblpro_user_can_edit_post( get_current_user_id(), $post_id ) ) {
			wp_die( - 1 );
		}

		$thumbnail_id = isset( $_POST['thumbnail_id'] ) ? intval( wp_unslash( $_POST['thumbnail_id'] ) ) : -1;

		// For backward compatibility, -1 refers to no featured image.
		if ( - 1 === $thumbnail_id ) {
			$thumbnail_id = null;
		}

		if ( ! function_exists( '_wp_post_thumbnail_html' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$return = _wp_post_thumbnail_html( $thumbnail_id, $post_id );
		wp_send_json_success( $return );
	}
}
