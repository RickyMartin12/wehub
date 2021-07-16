<?php
/**
 * Shortcodes Metabox for pending post lists settings.
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
 * Pending post lists settings meta box.
 */
class Pending_Post_List_Settings_Meta_Box extends Shortcode_Meta_Box {

	/**
	 * Saves Meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		update_post_meta( $post_id, '_bbl_action_pending_tab_enabled', $this->input( '_bbl_action_published_tab_enabled', 1 ) );
		update_post_meta( $post_id, '_bbl_action_pending_tab_position', $this->input( '_bbl_action_pending_tab_position', 40 ) );
		update_post_meta( $post_id, '_bbl_action_pending_tab_available_roles', $this->input( '_bbl_action_pending_tab_available_roles', array() ) );
	}

	/**
	 * Renders Meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {
		$post_id         = $post->ID;
		$pending_enabled = (int) $this->get_meta( $post_id, '_bbl_action_pending_tab_enabled', 0 );
		$tab_label       = $this->get_meta( $post_id, '_bbl_action_pending_tab_label', __( 'Pending', 'buddyblog-pro' ) );

		$tab_position    = (int) $this->get_meta( $post_id, '_bbl_action_pending_tab_position', 40 );
		$available_roles = (array) $this->get_meta( $post_id, '_bbl_action_pending_tab_available_roles', array() );

		?>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?php _e( 'Add pending tab?', 'buddyblog-pro' ) ?></th>
				<td>
					<label>
						<input type="radio" name="_bbl_action_pending_tab_enabled" value="1" <?php checked( $pending_enabled, 1 ); ?>>
						<?php _e( 'Yes', 'buddyblog-pro' ); ?>
					</label><br>
					<label>
						<input type="radio" name="_bbl_action_pending_tab_enabled" value="0" <?php checked( $pending_enabled, 0 ); ?>>
						<?php _e( 'No', 'buddyblog-pro' ); ?>
					</label>
				</td>
			</tr>
			<!--
            <tr>
                <th scope="row"><?php _e( 'Tab Label', 'buddyblog-pro' ) ?></th>
                <td>
                    <input type="text" name="post_pending_tab_label" value="<?php echo esc_attr( $tab_label ); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Tab order', 'buddyblog-pro' ) ?></th>
                <td>
                    <input type="text" name="post_pending_tab_position" value="<?php echo esc_attr( $tab_position ); ?>" />
                </td>
            </tr>
            -->
			<tr>
				<th scope="row"><?php _e( 'Enable for', 'buddyblog-pro' ) ?></th>
				<td>
					<?php foreach ( $this->get_roles() as $role => $label ) : ?>
						<label>
							<input type="checkbox" name="_bbl_action_pending_tab_available_roles[]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $available_roles ), true ); ?>>
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