<?php
/**
 * BuddyBlog Pro Form Post fields metabox helper
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
 * Post fields meta box helper.
 */
class Post_Fields_Meta_Box extends BBL_Meta_Box {

	/**
	 * Saves Post fields settings.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

	}

	/**
	 * Renders Post fields(core fields) meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post    = get_post( $post );
		$post_id = $post->ID;

		$allowed_fields = bblpro_get_core_field_types();

		?>
		<div id="bbl-post-fields-wrapper" class="bbl-form-fields bbl-post-fields-wrapper" data-form-id="<?php echo esc_attr( $post_id );?>">

			<div class="bbl-field-section bbl-field-section-post-fields" id="bbl-field-section-post-fields">

				<div class="bbl-row bbl-row-post-fields">
					<label class="bbl-label bbl-label-post-fields bbl-col-left">
						<?php _e( 'Field Type:', 'buddyblog-pro');?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<select name="bbl-post-field-type" id="bbl-post-field-type">
							<option value=""><?php _e( '...', 'buddyblog-pro');?></option>
							<?php foreach ( $allowed_fields as $type => $label ): ?>
								<option value="<?php echo esc_attr( $type );?>"><?php echo $label;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div><!-- end of row -->

				<div class="bbl-row bbl-row-post-fields-is-required-row">
					<label class="bbl-label bbl-col-left">
						<?php _e( 'Is Required:', 'buddyblog-pro');?>
					</label>
					<div class="bbl-col-right bbl-col-post-field-is-required-options">
						<label><input type="radio" value="1" checked="checked" name="bbl-is-field-required" /><?php _e('Yes', 'buddyblog-pro');?></label>
						<label><input  type="radio" value="0" name="bbl-is-field-required" /><?php _e('No', 'buddyblog-pro');?></label>
					</div>

				</div><!-- end of row -->
				<?php $default_editor = 'editor'; ?>
				<div class="post-field-type-options post-field-type-options-post_content">

					<div class="bbl-row bbl-row-post-fields-is-required-row">
						<label class="bbl-label bbl-col-left">
							<?php _e( 'Use editor:', 'buddyblog-pro' ); ?>
						</label>
						<div class="bbl-col-right bbl-col-post-field-is-required-options">
							<label>
								<input type="radio" value="" name="bbl-content-use-editor" id="bbl-content-content-use-editor" /><?php _e( 'No', 'buddyblog-pro' ); ?>
							</label>
							<?php $editor_types = bblpro_get_registered_editors(); ?>
							<?php foreach ( $editor_types as $editor_type => $editor_name ) : ?>
                                <label>
                                    <input type="radio" value="<?php echo esc_attr( $editor_type ); ?>" name="bbl-content-use-editor" id="bbl-content-content-use-editor-<?php echo esc_attr( $editor_type ); ?>" <?php checked( $editor_type, $default_editor ); ?> />
									<?php echo esc_html( $editor_name ); ?>
                                </label>
							<?php endforeach; ?>

						</div>

					</div><!-- end of row -->

				</div><!-- end of options -->

                <div class="post-field-type-options post-field-type-options-post_excerpt">

                    <div class="bbl-row bbl-row-post-fields-is-required-row">
                        <label class="bbl-label bbl-col-left">
							<?php _e( 'Use editor:', 'buddyblog-pro' ); ?>
                        </label>
                        <div class="bbl-col-right bbl-col-post-field-is-required-options">
                            <label>
                                <input type="radio" value="" name="bbl-content-use-editor" id="bbl-content-excerpt-use-editor"/><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>
							<?php $editor_types = bblpro_get_registered_editors(); ?>
							<?php foreach ( $editor_types as $editor_type => $editor_name ) : ?>
                                <label>
                                    <input type="radio" value="<?php echo esc_attr( $editor_type ); ?>" name="bbl-content-use-editor" <?php checked( $editor_type, $default_editor ); ?> id="bbl-content-excerpt-use-editor-<?php echo esc_attr( $editor_type ); ?>" />
									<?php echo esc_html( $editor_name ); ?>
                                </label>
							<?php endforeach; ?>

                        </div>

                    </div><!-- end of row -->

                </div><!-- end of excerpt options -->
                <div class="bbl-row bbl-row-post-fields-placeholder-row">
                    <label class="bbl-label bbl-col-left">
						<?php _e( 'Placeholder:', 'buddyblog-pro');?>
                    </label>
                    <div class="bbl-col-right bbl-col-post-field-placeholder">
                        <input type="text" value="" placeholder="<?php echo esc_attr( __( 'Placeholder content', 'buddyblog-pro' ) );?>" id="bbl-post-field-placeholder" name="bbl-post-field-placeholder"/>
                    </div>

                </div><!-- end of row -->

				<div class="bbl-row bbl-row-post-fields-add-field-button">
					<div class="bbl-col-left bbl-submit-button">
						<button type="button" id="bbl-add-post-field" class="button button-primary button-large"><?php _e( '+Add', 'buddyblog-pro' );?></button>
					</div>
				</div><!-- end of row -->

			</div>

			<!-- already selected types -->
			<div id="bbl-posts-fields-list-section">
				<?php bblpro_print_core_fields_table( $post_id ); ?>
			</div>
		</div>
		<?php

	}

}
