<?php
/**
 * Number Field Type
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
 * Number Field implementation
 */
class BBL_Field_Type_Number extends BBL_Field_Type_Text {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'number',
				'label'       => _x( 'Number', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Numeric text field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => false ),
			)
		);
		parent::__construct( $args );
	}

	/**
	 * Sanitizes value.
	 *
	 * @param mixed $meta_value meta value.
	 * @param array $field_settings field settings.
	 * @param int   $form_id form id.
	 * @param int   $post_id post id.
	 *
	 * @return numeric
	 */
	public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {

		if ( is_null( $meta_value ) ) {
			return $this->get_default_field_value( $field_settings );
		}

		return filter_var( $meta_value, FILTER_SANITIZE_NUMBER_FLOAT );
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

		if ( empty( $meta_value ) || is_numeric( $meta_value ) ) {
			return true;
		} else {
			return new \WP_Error( 'invalid_value', __( 'Please provide a valid number.', 'buddyblog-pro' ) );
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

	/**
	 * Field settings Args.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {

	}
}
