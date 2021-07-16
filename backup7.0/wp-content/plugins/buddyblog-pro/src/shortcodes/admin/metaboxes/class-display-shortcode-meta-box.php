<?php
/**
 * Shortcodes Metabox for post lists settings.
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
 * Post lists settings meta box.
 */
class Display_Shortcode_Meta_Box extends Shortcode_Meta_Box {

	/**
	 * Saves Meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		update_post_meta( $post_id, '_bbl_action_list_tab_enabled', $this->input( '_bbl_action_list_tab_enabled', 1 ) );
		update_post_meta( $post_id, '_bbl_action_list_tab_position', $this->input( '_bbl_action_list_tab_position', 10 ) );
		update_post_meta( $post_id, '_bbl_action_list_tab_available_roles', $this->input( '_bbl_action_list_tab_available_roles', array() ) );
	}

	/**
	 * Renders Meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {
		?>
		<div class="shortcode-meta-contents">
			<div class="bbl-shortcode-code">
				<code>[bbl-post-handler id="<?php echo $post->ID; ?>"]</code>
			</div>
            <?php wp_nonce_field('buddyblog-pro-shortcode-admin-nonce', '_buddyblog-pro-shortcode-admin-nonce');?>
			<div class="bbl-shortcode-help">
				<?php if ( 'publish' == $post->post_status ): ?>
					<p><?php _e( 'Please copy the above shortcode and put it in a page to allow users start posting', 'buddyblog-pro' ); ?></p>
				<?php else: ?>
					<p><?php _e( 'Please publish the shortcode and then you can use the above shortcode in any of your page', 'buddyblog-pro' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

}