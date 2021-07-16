<?php
/**
 * Post deletion handler
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
 * Post deletion helper.
 */
class Post_Deletion_Handler {

	/**
	 * Class self boot
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {
		add_action( 'bblpro_actions', array( $this, 'handle' ) );
	}

	/**
	 * Handle deletion.
	 */
	public function handle() {

		if ( bblpro_get_current_action() !== 'delete' || ! bp_action_variable( 0 ) ) {
			return;
		}

		$notices = buddyblog_pro()->notices;

		$post_id   = absint( bp_action_variable( 0 ) );
		$nonce     = isset( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '';
		$post_type = bblpro_get_current_post_type();
		$screen    = isset( $_GET['screen'] ) ? wp_unslash( $_GET['screen'] ) : '';

		$redirect = bblpro_get_referrer_url( bp_displayed_user_id(), $post_type, 'delete', $screen );

		if ( ! $redirect ) {
			$redirect = bblpro_get_post_type_tab_url( bp_displayed_user_id(), $post_type );
		}

		// is it valid?
		if ( ! $post_id || ! $nonce || ! wp_verify_nonce( $nonce, 'bblpro_delete_' . $post_id ) ) {
			bp_core_add_message(  __( 'Invalid action.', 'buddyblog-pro' ), 'error' );

			if ( $redirect ) {
				bp_core_redirect( $redirect );
			}

			return; // in case redirect was not set.
		}

		// can the user do it?
		if ( ! bblpro_user_can_delete_post( get_current_user_id(), $post_id ) ) {
			bp_core_add_message(  __( 'Access denied.', 'buddyblog-pro' ), 'error' );

			if ( $redirect ) {
				bp_core_redirect( $redirect );
			}

			return; // in case redirect was not set.
		}

		// if we are here, the user is authorized to delete.
		// delete post and redirect to posts list.
		if ( bblpro_get_option( $post_type . '_on_delete_keep_in_trash', 1 ) ) {
			wp_trash_post( $post_id );
		} else {
			wp_delete_post( $post_id, true );
		}

		// we have removed the post. Redirect to posts list.
		bp_core_add_message(  __( 'Post deleted.', 'buddyblog-pro' ), 'success' );
		bp_core_redirect( $redirect );
	}
}
