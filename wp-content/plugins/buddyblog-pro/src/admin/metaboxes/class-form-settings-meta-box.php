<?php
/**
 * BuddyBlog Pro Form general settings meta box helper
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
 * Form general settings helper.
 */
class Form_Settings_Meta_Box extends BBL_Meta_Box {
	/**
	 * Saves settings meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		$post_type = $this->input( 'post-type', 'post' );
		update_post_meta( $post_id, '_buddyblog_post_type', $post_type );

		update_post_meta( $post_id, '_buddyblog_enable_post_visibility', $this->input( 'enable-post-visibility', 0 ) );
		update_post_meta( $post_id, '_buddyblog_post_visibility', $this->input( 'default-post-visibility', 'public' ) );
		//update_post_meta( $post_id, '_buddyblog_allow_upload', $this->input( 'allow-upload', 0 ) );
		update_post_meta( $post_id, '_buddyblog_comment_status', $this->input( 'comment-status', 0 ) );
		update_post_meta( $post_id, '_buddyblog_allow_custom_comment_status', $this->input( 'allow-custom-comment-status', 0 ) );
	}

	/**
	 * Renders Settings meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post    = get_post( $post );
		$post_id = $post->ID;

		$post_types        = bblpro_get_available_post_types();
		$post_type_options = array();

		foreach ( $post_types as $post_type ) {
			$post_type_object                = get_post_type_object( $post_type );
			$post_type_options[ $post_type ] = $post_type_object->labels->name;
		}

		$selected_post_type = get_post_meta( $post_id, '_buddyblog_post_type', true );

		if ( empty( $selected_post_type ) ) {
			$selected_post_type = 'post';
		}

		$post_statuses = array(
			'publish' => __( 'Published', 'buddyblog-pro' ),
			// 'draft'   => __( 'Draft', 'buddyblog-pro' ),
			'pending' => __( 'Needs approval', 'buddyblog-pro' ),
		);

		$post_visibilities = array(
			'public'   => __( 'Public', 'buddyblog-pro' ),
			'private'  => __( 'Private', 'buddyblog-pro' ),
			'password' => __( 'Password protected', 'buddyblog-pro' ),
		);

		$comment_statuses = array(
			'open'  => __( 'Open', 'buddyblog-pro' ),
			'close' => __( 'Closed', 'buddyblog-pro' ),
		);

		?>
		<div id="bbl-post-settings-wrapper" class="bbl-form-fields bbl-post-settings-wrapper"
			 data-form-id="<?php echo esc_attr( $post_id ); ?>">

			<div class="bbl-field-section bbl-field-section-post-settings" id="bbl-field-section-post-settings">

				<div class="bbl-row bbl-row-post-settings bbl-row-post-settings-post-type">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Post Type:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-post-type',
								'options'  => $post_type_options,
								'selected' => $selected_post_type,
							)
						);
						?>
					</div>
				</div><!-- end of row -->

				<div class="bbl-row bbl-row-post-settings bbl-row-post-settings-post-visibility">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Enable post visibility:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-enable-post-visibility',
								'options'  => array(
									1 => __( 'Yes', 'buddyblog-pro' ),
									0 => __( 'No', 'buddyblog-pro' ),
								),
								'selected' => get_post_meta( $post_id, '_buddyblog_enable_post_visibility', true ),
							)
						);
						?>
					</div>
				</div><!-- end of row -->

				<div class="bbl-row bbl-row-post-settings bbl-row-post-settings-post-visibility-default">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Default post visibility:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
					<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-default-post-visibility',
								'options'  => $post_visibilities,
								'selected' => get_post_meta( $post_id, '_buddyblog_post_visibility', true ),
							)
						);
						?>
					</div>
				</div><!-- end of row -->

				<!-- <div class="bbl-row bbl-row-post-settings bbl-row-post-settings-allow-upload">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Allow upload:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-allow-upload',
								'options'  => array(
									1 => __( 'Yes', 'buddyblog-pro' ),
									0 => __( 'No', 'buddyblog-pro' ),
								),
								'selected' => get_post_meta( $post_id, '_buddyblog_allow_upload', true ),
							)
						);
						?>
					</div>
				</div> --><!-- end of row -->

				<div class="bbl-row bbl-row-post-settings bbl-row-post-settings-comment-status">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Default Comment Status:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-comment-status',
								'options'  => array(
									'open'   => __( 'Open', 'buddyblog-pro' ),
									'closed' => __( 'Closed', 'buddyblog-pro' ),
								),
								'selected' => get_post_meta( $post_id, '_buddyblog_comment_status', true ),
							)
						);
						?>
					</div>
				</div><!-- end of row -->

				<div class="bbl-row bbl-row-post-settings bbl-row-post-settings-allow-custom-comment-status">
					<label class="bbl-label bbl-label-post-settings bbl-col-left">
						<?php _e( 'Allow Post author to control comment status:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<?php
						$this->selectbox(
							array(
								'name'     => 'bbl-input-allow-custom-comment-status',
								'options'  => array(
									1 => __( 'Yes', 'buddyblog-pro' ),
									0 => __( 'No', 'buddyblog-pro' ),
								),
								'selected' => get_post_meta( $post_id, '_buddyblog_allow_custom_comment_status', true ),
							)
						);
						?>
					</div>
				</div><!-- end of row -->
				<?php wp_nonce_field( 'buddyblog-pro-form-admin-nonce', '_buddyblog-pro-form-admin-nonce' ); ?>
			</div><!-- section end-->

		</div>
		<?php
	}
}
