<?php
/**
 * BuddyBlog Pro Form cloning helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh, Ravi Sharma
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Form_Admin_Clone_Helper
 */
class Form_Admin_Clone_Helper {

	/**
	 * Singleton.
	 *
	 * @var Form_Admin_Clone_Helper
	 */
	private static $instance = null;

	/**
	 * Setup the bootstrapper
	 *
	 * @return Form_Admin_Clone_Helper
	 */
	public static function boot() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup callbacks
	 */
	private function setup() {
		add_filter( 'post_row_actions', array( $this, 'add_clone_link' ), 10, 2 );
		add_action( 'post_action_bbl_clone_form', array( $this, 'clone_form' ) );
	}

	/**
	 * Adds clone link as row action in forms list
	 *
	 * @param string[] $actions Actions.
	 * @param \WP_Post $post    Post object.
	 *
	 * @return array
	 */
	public function add_clone_link( $actions, $post ) {

		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return $actions;
		}

		if ( bblpro_get_form_post_type() !== $post->post_type ) {
			return $actions;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! $post_type_object ) {
			return $actions;
		}

		$action = 'bbl_clone_form';

		// Not sure.
		$clone_link = add_query_arg( 'action', $action, admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

		$actions['bbl_clone_form'] = sprintf(
			'<a href="%s" rel="bookmark">%s</a>',
			wp_nonce_url( $clone_link, "$action-post_{$post->ID}" ),
			__( 'Clone', 'buddyblog-pro' )
		);

		return $actions;
	}

	/**
	 * Clones form as a new post.
	 *
	 * @param int $post_id Post id.
	 */
	public function clone_form( $post_id ) {

		if ( ! is_super_admin() ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'bbl_clone_form-post_' . $post_id ) ) {
			return;
		}

		$form_data = bblpro_get_exportable_form_data( $post_id );

		if ( ! $form_data ) {
			return;
		}

		if ( ! empty( $form_data['post_title'] ) ) {
			$form_data['post_title'] = $form_data['post_title'] . '-' . apply_filters( 'bblpro_cloned_post_title_suffix', __( 'Duplicate', 'buddyblog-pro' ) );
		}

		$meta_values = array();

		if ( isset( $form_data['meta'] ) ) {
			$meta_values = $form_data['meta'];

			unset( $form_data['meta'] );
		}

		$post_id = wp_insert_post( $form_data );

		if ( ! $post_id ) {
			return;
		}

		foreach ( $meta_values as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
		exit;
	}
}
