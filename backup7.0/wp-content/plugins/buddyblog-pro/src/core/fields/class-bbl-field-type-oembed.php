<?php
/**
 * Text Field Type Helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core\Fields
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Core\Fields;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Single line text field type.
 */
class BBL_Field_Type_Oembed extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'oembed',
				'label'       => _x( 'Oembed', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Video/Audio oembed field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => false ),
			)
		);
		parent::__construct( $args );
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
		return $this->prepare_common_field_settings( $form_id, $key, $settings );
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
			$sanitized = esc_url_raw( $meta_value );
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

		if ( empty( $meta_value ) || filter_var( $meta_value, FILTER_SANITIZE_URL ) ) {
			return true;
		} else {
			// in case of text, we accept everything(or should we check is_scalar).
			return new \WP_Error( 'invalid_value', __( 'Please use a valid url.', 'buddyblog-pro' ) );
		}
	}

	/**
	 * Format value for display.
	 *
	 * @param mixed  $value value.
	 * @param string $key meta key.
	 * @param int    $post_id post id.
	 *
	 * @return mixed
	 */
	public function format_for_display( $value, $key, $post_id ) {
		// global.
		global $wp_embed;

		return empty( $value ) ? '' : $wp_embed->shortcode( array(), $value );
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {

		$atts = array(
			'type'        => 'text',
			'name'        => $args['name'],
			'id'          => $args['id'],
			'value'       => $args['value'],
			'placeholder' => $args['placeholder'],
		);
		?>

		<label for='<?php echo $atts["id"]; ?>' class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $atts["type"] ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
		</label>
		<input <?php echo $this->get_html_attributes( $atts ); ?> />
		<?php
	}
}
