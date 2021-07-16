<?php
/**
 * BuddyBlog Pro Form Post Meta display control meta box
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin/Metaboxes
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin\Metaboxes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Workflow meta box helper.
 */
class Meta_Display_Meta_Box extends BBL_Meta_Box {

	/**
	 * Saves meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		// admin notification on submission.
		update_post_meta( $post_id, '_buddyblog_enable_post_meta_display_excerpt', $this->input( 'enable-post-meta-display-excerpt', 0 ) );
		update_post_meta( $post_id, '_buddyblog_post_meta_before_excerpt_entry', $this->input( 'post-meta-before-excerpt-entry', '' ) );
		update_post_meta( $post_id, '_buddyblog_post_meta_after_excerpt_entry', $this->input( 'post-meta-after-excerpt-entry', '' ) );

		update_post_meta( $post_id, '_buddyblog_enable_post_meta_display_single', $this->input( 'enable-post-meta-display-single', 0 ) );
		update_post_meta( $post_id, '_buddyblog_post_meta_before_single_entry', $this->input( 'post-meta-before-single-entry', '' ) );
		update_post_meta( $post_id, '_buddyblog_post_meta_after_single_entry', $this->input( 'post-meta-after-single-entry', '' ) );
		}

	/**
	 * Renders meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post    = get_post( $post );
		$form_id = $post->ID;

		$display_excerpt = get_post_meta( $form_id, '_buddyblog_enable_post_meta_display_excerpt', true );

		if ( empty( $display_excerpt ) ) {
			$display_excerpt = 0;
		}

		$before_excerpt = get_post_meta( $form_id, '_buddyblog_post_meta_before_excerpt_entry', true );
		$after_excerpt  = get_post_meta( $form_id, '_buddyblog_post_meta_after_excerpt_entry', true );

		$doc_link             = '#';

		$display_single = get_post_meta( $form_id, '_buddyblog_enable_post_meta_display_single', true );

		if ( empty( $display_single ) ) {
			$display_single = 0;
		}

		$before_single_entry = get_post_meta( $form_id, '_buddyblog_post_meta_before_single_entry', true );
		$after_single_entry  = get_post_meta( $form_id, '_buddyblog_post_meta_after_single_entry', true );

		$doc_link_single_post = '#';
		?>

		<div class="bblpro-section-post-meta-display-settings bblpro-meta-box-settings">

			<div class="bbl-form-post-meta-display-excerpt-fields">
				<fieldset class="bbl-section-sub bbl-section-post-meta-display-excerpt">
					<legend><?php _e( 'With Post Excerpt', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-setting-help">
                        <p>
                            <?php _e( 'This section allows you to control the display of your custom meta data in the posts list.', 'buddyblog-pro');?>
                            <?php _e( 'Please add the meta shortcodes from custom fields section here.', 'buddyblog-pro');?>
                            <?php // printf( __( 'For more details, Please view <a href="%s" target="_blank">documentation</a>.', 'buddyblog-pro'), esc_url( $doc_link ) );?>

                        </p>
                    </div>
                    <div class="bbl-row bbl-row-post-meta-display-excerpt-settings">

						<label class="bbl-label bbl-col-left bbl-label-post-meta-display-excerpt-settings">
							<?php _e( 'Enable Extra meta display', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<label>
								<input type="radio" name="bbl-input-enable-post-meta-display-excerpt" value="1" <?php checked( 1, $display_excerpt ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
							</label>
							<label>
								<input type="radio" name="bbl-input-enable-post-meta-display-excerpt" value="0" <?php checked( 0, $display_excerpt ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
							</label>

						</div>
					</div> <!-- end row -->

					<div class="bbl-row">
						<label class="bbl-label bbl-col-left bbl-label-meta-before-excerpt">
							<?php _e( 'Before Excerpt', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<textarea rows="10" cols="80" name="bbl-input-post-meta-before-excerpt-entry"><?php echo esc_textarea( $before_excerpt ); ?></textarea>
						</div>
					</div> <!-- end row -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'After Excerpt', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <textarea rows="10" cols="80" name="bbl-input-post-meta-after-excerpt-entry"><?php echo esc_textarea( $after_excerpt ); ?></textarea>
                        </div>
                    </div> <!-- end row -->


                </fieldset><!-- end .bbl-section-meta-display-excerpt -->

                <fieldset class="bbl-section-sub bbl-section-post-meta-display-single">
                    <legend><?php _e( 'With Single Post', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-setting-help">
                        <p>
							<?php _e( 'This section allows you to control the display of your custom meta data on your single post/article entry page.', 'buddyblog-pro');?>
							<?php _e( 'Please add the meta shortcodes from custom fields section here.', 'buddyblog-pro');?>
							<?php //printf( __( 'For more details, Please view <a href="%s" target="_blank">documentation</a>.', 'buddyblog-pro'), esc_url( $doc_link_single_post ) );?>

                        </p>
                    </div>
                    <div class="bbl-row bbl-row-post-meta-display-single-settings">

                        <label class="bbl-label bbl-col-left bbl-label-post-meta-display-single-settings">
							<?php _e( 'Enable Extra meta display', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <label>
                                <input type="radio" name="bbl-input-enable-post-meta-display-single" value="1" <?php checked( 1, $display_single ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="bbl-input-enable-post-meta-display-single" value="0" <?php checked( 0, $display_single ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>

                        </div>
                    </div> <!-- end row -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-meta-before-single">
							<?php _e( 'Before Entry', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <textarea rows="10" cols="80" name="bbl-input-post-meta-before-single-entry"><?php echo esc_textarea( $before_single_entry ); ?></textarea>
                        </div>
                    </div> <!-- end row -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'After Entry', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <textarea rows="10" cols="80" name="bbl-input-post-meta-after-single-entry"><?php echo esc_textarea( $after_single_entry ); ?></textarea>
                        </div>
                    </div> <!-- end row -->


                </fieldset><!-- end .bbl-section-meta-display-single -->

            </div><!-- end of post meta display fields -->

		</div><!-- end section -->
		<?php
	}

}
