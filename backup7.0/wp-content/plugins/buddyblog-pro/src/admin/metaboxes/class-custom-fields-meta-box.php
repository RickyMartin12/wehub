<?php
/**
 * BuddyBlog Pro Form custom fields meta box helper
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
 * Custom fields meta box helper.
 */
class Custom_Fields_Meta_Box extends BBL_Meta_Box {

	/**
	 * Saves custom fields settings.
	 *
	 * @param \WP_Post $post post object.
	 *
	 * @see Form_Admin_Custom_Fields_Ajax_Handler
	 */
	public function save( $post ) {
	}

	/**
	 * Renders Custom Fields Meta box
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {
		$post_id     = $post->ID;
		$field_types = bblpro_get_custom_field_types();

		?>
	 <div id="bbl-admin-form-cf-wrapper" class="bbl-form-fields bbl-admin-form-cf-wrapper" data-form-id="<?php echo esc_attr( $post_id ); ?>">

			<div class="bbl-field-section bbl-cf-field-section bbl-cf-field-section-common" id="bbl-cf-field-section-common">

				<div class="bbl-row bbl-row-cf bbl-row-cf-field-type">
					<label class="bbl-label bbl-label-cf bbl-col-left">
						<?php _e( 'Field Type:', 'buddyblog-pro' ); ?>
						<span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right">
						<select name="bbl-input-cf-field-type" id="bbl-input-cf-field-type">
							<option value=""><?php _e( '...', 'buddyblog-pro' ); ?></option>
							<?php foreach ( $field_types as $type => $label ): ?>
								<option value="<?php echo esc_attr( $type ); ?>"><?php echo $label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div><!-- end of row -->

				<div class="bbl-row bbl-row-cf bbl-row-cf-field-label">
					<label class="bbl-label bbl-label-cf bbl-col-left">
						<?php _e( 'Label:', 'buddyblog-pro' ); ?><span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right bbl-col-cf-field-label">
						<input type="text" value="" name="bbl-input-cf-field-label" id="bbl-input-cf-field-label" />
					</div>

				</div><!-- end of row -->

				<div class="bbl-row bll-row-cf bbl-row-cf-field-key">
					<label class="bbl-label bbl-label-cf bbl-col-left">
						<?php _e( 'Meta Key:', 'buddyblog-pro' ); ?><span class="bbl-required">*</span>
					</label>
					<div class="bbl-col-right bbl-col-field-key">
						<input type="text" value="" name="bbl-input-cf-field-key" id="bbl-input-cf-field-key" />
						<p><?php _e( 'Use lowercase text. Spaces not allowed.', 'buddyblog-pro' ); ?></p>
					</div>

				</div><!-- end of row -->

				<div class="bbl-row bbl-row-cf bbl-row-cf-field-is-required">
					<label class="bbl-label bbl-label-cf bbl-col-left">
						<?php _e( 'Is Required:', 'buddyblog-pro' ); ?>
					</label>
					<div class="bbl-col-right bbl-col-cf-field-is-required">
						<label>
                            <input type="radio" value="1" checked="checked" name="bbl-input-cf-field-is-required"/><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                        </label>
						<label>
                            <input type="radio" value="0" name="bbl-input-cf-field-is-required"/><?php _e( 'No', 'buddyblog-pro' ); ?>
                        </label>
					</div>

				</div><!-- end of row -->

				<div class="bbl-row bbl-row-cf bbl-row-cf-field-default-value">
					<label class="bbl-label bbl-lable-cf bbl-col-left">
						<?php _e( 'Default value:', 'buddyblog-pro' ); ?>
					</label>
					<div class="bbl-col-right bbl-col-cf-field-default-value">
						<input type="text" placeholder="<?php _e( 'Default value', 'buddyblog-pro' ); ?>" name="bbl-input-cf-field-default-value" id="bbl-input-cf-field-default-value"/>
					</div>

				</div><!-- end of row -->

				<div class="bbl-row bbl-row-cf bbl-row-cf-field-placeholder">
					<label class="bbl-label bbl-lable-cf bbl-col-left">
						<?php _e( 'Placeholder:', 'buddyblog-pro' ); ?>
					</label>
					<div class="bbl-col-right bbl-col-cf-field-placeholder">
						<input type="text" placeholder="<?php _e( 'Placeholder content', 'buddyblog-pro' ); ?>" name="bbl-input-cf-field-placeholder" id="bbl-input-cf-field-placeholder"/>
					</div>

				</div><!-- end of row -->

				<div class="bbl-cf-fields-extra-section">
					<?php do_action( 'bblpro_custom_fields_admin_options' ); ?>
				</div>

				<div class="bbl-row bbl-row-cf bbl-cf-row-add-custom-field-button">
					<div class="bbl-col-left bbl-submit-button">
						<button type="button" id="bbl-input-cf-add-custom-field" class="button button-primary button-large"><?php _e( '+Add', 'buddyblog-pro' ); ?></button>
					</div>
				</div><!-- end of row -->

			</div>

			<!-- already selected types -->
			<div id="bbl-cf-fields-list-section" class="bbl-fields-section bbl-cf-fields-list-section">
				<?php bblpro_print_custom_fields_table( $post_id ); ?>
			</div>

		</div>
		<?php
	}
}
