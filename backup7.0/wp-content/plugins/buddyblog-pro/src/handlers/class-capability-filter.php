<?php
/**
 * Capability filter
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
 * Capability Filter
 */
class Capability_Filter {

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
		add_filter( 'user_has_cap', array( $this, 'edit_cap_filter' ), 10, 3 );
		add_filter( 'user_has_cap', array( $this, 'upload_cap_filter' ), 0, 3 );

	}

	/**
	 * Edit post capability filter
	 *
	 * We allow editing if enabled in our settings.
	 *
	 * @param array $allcaps All the capabilities of the user.
	 * @param array $cap     [0] Required capability.
	 * @param array $args    [0] Requested capability
	 *                       [1] User ID
	 *                       [2] Associated object ID.
	 *
	 * @return array
	 */
	public function edit_cap_filter( $allcaps, $cap, $args ) {

		// it is our capability?
		if ( 'edit_post' !== $args[0] ) {
			return $allcaps;
		}

		// Bail out for users who can already edit others posts.
		if ( ! empty( $allcaps['edit_others_posts'] ) ) {
			return $allcaps;
		}
		$post = get_post( $args[2] );

		// not our post type.
		if ( ! bblpro_is_post_type_enabled( $post->post_type ) ) {
			return $allcaps;
		}

		// is editing is enabled and user can edit their own post.
		if ( bblpro_user_can_edit_post( get_current_user_id(), $args[2] ) ) {
			$allcaps[ $cap[0] ] = true;
		}

		return $allcaps;

	}

	/**
	 * Add upload capability to subscribers.
	 *
	 * @param array $allcaps All the capabilities of the user.
	 * @param array $cap     [0] Required capability.
	 * @param array $args    [0] Requested capability
	 *                       [1] User ID
	 *                       [2] Associated object ID.
	 *
	 * @return array
	 */
	public function upload_cap_filter( $allcaps, $cap, $args ) {

		if ( 'upload_files' !== $args[0] ) {
			return $allcaps;
		}

		if ( ! is_user_logged_in() ) {
			return $allcaps;
		}

		$post_type = isset( $_POST['bbl_context_type'] ) ? wp_unslash( $_POST['bbl_context_type'] ) : bblpro_get_current_post_type();

		if ( ! $post_type || ! bblpro_is_post_type_enabled( $post_type ) ) {
			return $allcaps;
		}


		if ( bblpro_user_can_upload( get_current_user_id(), $post_type ) ) {
			$allcaps[ $cap[0] ] = true;
		}

		return $allcaps;
	}
}
