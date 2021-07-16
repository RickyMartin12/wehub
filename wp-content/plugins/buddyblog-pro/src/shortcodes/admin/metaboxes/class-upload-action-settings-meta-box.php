<?php
/**
 * Shortcodes Metabox for Create action settings.
 *
 * @package    BuddyBlog_Pro
 * @subpackage Shortcodes\Admin\Metaboxes
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */
namespace BuddyBlog_Pro\Shortcodes\Admin\Metaboxes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Create Action settings metabox.
 */
class Upload_Action_Settings_Meta_Box extends Shortcode_Meta_Box {

	/**
	 * Saves Meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		update_post_meta( $post_id, '_bbl_action_upload_enabled', $this->input( '_bbl_action_upload_enabled', 1 ) );
		update_post_meta( $post_id, '_bbl_action_upload_available_roles', $this->input( '_bbl_action_upload_available_roles', 0 ) );

	}

	/**
	 * Renders Meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {
		$post_id = $post->ID;
		$upload_enabled  = (int) $this->get_meta($post_id, '_bbl_action_upload_enabled', 1 );
		$available_roles = (array) $this->get_meta( $post_id, '_bbl_action_upload_available_roles', array() );

		?>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?php _e( 'Allow uploading media?', 'buddyblog-pro' ) ?></th>
				<td>
					<label>
						<input type="radio" name="_bbl_action_upload_enabled" value="1" <?php checked( $upload_enabled, 1 ); ?>>
						<?php _e( 'Yes', 'buddyblog-pro' ); ?>
					</label><br>
					<label>
						<input type="radio" name="_bbl_action_upload_enabled" value="0" <?php checked( $upload_enabled, 0 ); ?>>
						<?php _e( 'No', 'buddyblog-pro' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Enable upload for', 'buddyblog-pro' ) ?></th>
				<td>
					<?php foreach ( $this->get_roles() as $role => $label ) : ?>
						<label>
							<input type="checkbox" name="_bbl_action_upload_available_roles[]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $available_roles ), true ); ?>>
							<?php echo esc_html( $label ); ?>
						</label><br>
					<?php endforeach; ?>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

}