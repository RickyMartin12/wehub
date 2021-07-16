<?php
/**
 * Rich text editpo field type
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core\Fields
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Core\Fields;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Editor Field Type
 */
class BBL_Field_Type_Editor extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'editor',
				'label'       => _x( 'Rich Text Editor', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Multiline text field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => true ),
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Sanitizes value.
	 *
	 * @param mixed $meta_value meta value.
	 * @param array $field_settings meta key.
	 * @param int   $form_id form id.
	 * @param int   $post_id post id.
	 *
	 * @return mixed
	 */
	public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {

		if ( is_null( $meta_value ) ) {
			$sanitized = $this->get_default_field_value( $field_settings );
		} else {
			$sanitized = wp_kses_post( $meta_value );
		}

		return $sanitized;
	}

	/**
	 * Validates value.
	 *
	 * @param mixed $meta_value meta value.
	 * @param array $field_settings meta key.
	 * @param int   $form_id form id.
	 * @param int   $post_id post id.
	 *
	 * @return bool|\WP_Error
	 */
	public function validate_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {

		if ( empty( $meta_value ) || is_scalar( $meta_value ) ) {
			return true;
		} else {
			return new \WP_Error( 'invalid_value', __( 'Please provide some textual input.', 'buddyblog-pro' ) );
		}
	}
	/**
	 * Prepare settings for save.
	 *
	 * Should be overridden in the child classe.
	 *
	 * @param int    $form_id form id.
	 * @param string $key field key.
	 * @param array  $settings field settings.
	 *
	 * @return \WP_Error|array
	 */
	public function prepare_settings( $form_id, $key, $settings ) {
		$common                 = $this->prepare_common_field_settings( $form_id, $key, $settings );
		$common['quicktags']    = empty( $_POST['bbl-input-cf-field-editor-show-quicktags'] ) ? 0 : 1;
		$common['allow_upload'] = empty( $_POST['bbl-input-cf-field-editor-allow-upload'] ) ? 0 : 1;

		return $common;
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {

		$atts = array(
			'type'        => $this->type,
			'name'        => $args['name'],
			'id'          => $args['id'],
			'placeholder' => $args['placeholder'],
			'rows'        => 5,
		);
		?>

        <label for='<?php echo $atts["id"]; ?>'
               class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $atts["type"] ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
        </label>
		<?php
		wp_editor(
			isset( $args['value'] ) ? $args['value'] : '',
			$atts['id'],
			array(
				'media_buttons' => isset( $args['allow_upload'] ) && $args['allow_upload'] ? true : false,
				'quicktags'     => isset( $args['quicktags'] ) && $args['quicktags'] ? true : false,
				'textarea_rows' => 8,
			)
		);
	}

	/**
	 * Field settings Args.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {
		$show  = isset( $args['quicktags'] ) ? (int) $args['quicktags'] : 1;
		$allow = isset( $args['allow_upload'] ) ? (int) $args['allow_upload'] : 1;
		?>
		<div class="bbl-row bbl-row-cf bbl-row-cf-field-editor-quicktags">
			<label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Show Quicktags:', 'buddyblog-pro' ); ?>
			</label>
			<div class="bbl-col-right bbl-col-cf-field-editor-options">
				<label>
					<input type="radio" name="bbl-input-cf-field-editor-show-quicktags" value="1" <?php checked( 1, $show );?>/>
					<?php _e( 'Yes', 'buddyblog-pro' ); ?>
				</label>
				<label>
					<input type="radio" name="bbl-input-cf-field-editor-show-quicktags" value="0" <?php checked( 0, $show );?>/>
					<?php _e( 'No', 'buddyblog-pro' ); ?>
				</label>
			</div>
		</div><!-- end of row -->

        <div class="bbl-row bbl-row-cf bbl-row-cf-field-editor-allow-upload">
			<label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Allow Upload:', 'buddyblog-pro' ); ?>
			</label>
			<div class="bbl-col-right bbl-col-cf-field-editor-options">
				<label>
					<input type="radio" name="bbl-input-cf-field-editor-allow-upload" value="1" <?php checked( 1, $allow);?>/>
					<?php _e( 'Yes', 'buddyblog-pro' ); ?>
				</label>
				<label>
					<input type="radio" name="bbl-input-cf-field-editor-allow-upload" value="0" <?php checked( 0, $allow );?>/>
					<?php _e( 'No', 'buddyblog-pro' ); ?>
				</label>
			</div>
		</div><!-- end of row -->
		<?php
	}

	/**
	 * Markup for the forem fields list view in the admin.
	 *
	 * @param array $field field settings.
	 */
	public function admin_fields_list_field_markup( $field ) {
		$this->print_common_field_settings( $field );
		?>
        <p>
			<?php _e( 'Quicktags enabled: ', 'buddyblog-pro' ); ?>
			<?php echo empty( $field['quicktags'] ) ? __( 'No', 'buddyblog-pro' ) : __( 'Yes', 'buddyblog-pro' ); ?>
        </p>
        <p>
			<?php _e( 'Upload allowed: ', 'buddyblog-pro' ); ?>
			<?php echo empty( $field['allow_upload'] ) ? __( 'No', 'buddyblog-pro' ) : __( 'Yes', 'buddyblog-pro' ); ?>
        </p>
		<?php
	}
}
