<?php
/**
 * BuddyBlog Pro Post approval meta box.
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin/Metaboxes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin\Metaboxes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Post approval meta box
 */
class Post_Approval_Meta_Box extends BBL_Meta_Box {
	/**
	 * Saves settings meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {
		$post_id = $post->ID;

		$state = $this->input( 'bbl-post-state', 'approved' );

		if ( empty( $state ) ) {
			return;
		}

		$previous = get_post_meta( $post_id, '_bbl_post_state', true );

		if ( $previous === $state ) {
			return;
		}

		if ( 'approved' === $state ) {
			do_action( 'bblpro_post_approved', $post_id );
		} else {
			$state = 'rejected';
			do_action( 'bblpro_post_rejected', $post_id );
		}

		update_post_meta( $post_id, '_bbl_post_state', $state );
	}

	/**
	 * Renders Settings meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post = get_post( $post );

		$form_id = bblpro_post_get_form_id( $post->ID );

		if ( 'pending' !== bblpro_form_get_post_status( $form_id ) ) {
			return;
		}

		$state = get_post_meta( $post->ID, '_bbl_post_state', true );
		?>
		<div class="bbl-post-state-options">
			<table>
				<tbody>
				<tr>
					<td><?php _e( 'Set post status to:', 'buddyblog-pro' ); ?></td>
					<td>
						<label>
							<input type="radio" name="bbl-input-bbl-post-state" value="approved" <?php checked( $state, 'approved' ); ?> />
							<?php _e( 'Approved', 'buddyblog-pro' ); ?>
						</label>
						<label>
							<input type="radio" name="bbl-input-bbl-post-state" value="rejected" <?php checked( $state, 'rejected' ); ?> />
							<?php _e( 'Rejected', 'buddyblog-pro' ); ?>
						</label>
						<label>
							<input type="radio" name="bbl-input-bbl-post-state" \value="" <?php checked( $state, '' ); ?> />
							<?php _e( "Don't change", 'buddyblog-pro' ); ?>
						</label>

					</td>
				</tr>
				</tbody>
			</table>

			<?php wp_nonce_field( 'buddyblog-pro-post-state-nonce', '_buddyblog-pro-post-state-nonce' ); ?>
		</div>
		<?php
	}
}
