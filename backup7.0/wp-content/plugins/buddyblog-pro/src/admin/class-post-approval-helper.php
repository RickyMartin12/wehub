<?php
/**
 * BuddyBlog Pro Form edit page helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

use BuddyBlog_Pro\Admin\Metaboxes\BBL_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Post_Approval_Meta_Box;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Form_Admin_Helper
 */
class Post_Approval_Helper {

	/**
	 * Metabox
	 *
	 * @var BBL_Meta_Box
	 */
	private $meta_box = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->meta_box = new Post_Approval_Meta_Box();
	}

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Register actions.
	 */
	private function setup() {
		// save post.
		add_action( 'save_post', array( $this, 'save_settings' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ), 10, 2 );
	}

	/**
	 * Register metaboxes.
	 *
	 * @param string $post_type post type.
	 */
	public function register_meta_boxes( $post_type, $post ) {

		$post = get_post( $post );

		$form_id = bblpro_post_get_form_id( $post->ID );

		if ( 'pending' !== bblpro_form_get_post_status( $form_id ) ) {
			return;
		}

		add_meta_box(
			'bbl-post-approval-options-meta-box',
			__( 'Post Approval', 'buddyblog-pro' ),
			array(
				$this->meta_box,
				'render',
			),
			$post_type
		);
	}

	/**
	 * Save form configurations.
	 *
	 * @param int $post_id post id.
	 */
	public function save_settings( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post = get_post( $post_id );

		$form_id = bblpro_post_get_form_id( $post_id );

		if ( 'pending' !== bblpro_form_get_post_status( $form_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_buddyblog-pro-post-state-nonce'] ) ) {
			return;
		}

		// verify nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST['_buddyblog-pro-post-state-nonce'] ), 'buddyblog-pro-post-state-nonce' ) ) {
			return;
		}

		// check if the user can update?
		if ( ! bblpro_user_can_moderate_posts( get_current_user_id() ) ) {
			return;
		}

		$this->meta_box->save( $post );
	}

}
