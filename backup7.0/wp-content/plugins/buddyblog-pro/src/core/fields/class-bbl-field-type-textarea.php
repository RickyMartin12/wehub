<?php
/**
 * Multi Line text area field type
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
 * Multiline text area field type.
 */
class BBL_Field_Type_Textarea extends BBL_Field_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'type'        => 'textarea',
				'label'       => _x( 'Text Area', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Multiline text field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => false ),
			)
		);
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
			$sanitized = sanitize_textarea_field( $meta_value );
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

		<label for='<?php echo $atts["id"]; ?>' class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $atts["type"] ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
		</label>
		<textarea  <?php echo $this->get_html_attributes( $atts ); ?> ><?php echo esc_textarea( $args['value'] ); ?></textarea>

		<?php
	}

	/**
	 * Field settings Args.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {
	}

	/**
	 * Prepare Field settings for saving in the form management page.
	 *
	 * @param int    $form_id form id.
	 * @param string $key field key.
	 * @param array  $settings field settings.
	 *
	 * @return \WP_Error|array
	 */
	public function prepare_settings( $form_id, $key, $settings ) {
		return $this->prepare_common_field_settings( $form_id, $key, $settings );
	}
}
