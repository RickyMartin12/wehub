<?php
/**
 * Radio Field Type Helper
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
 * Radio Field class
 */
class BBL_Field_Type_Radio extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'radio',
				'label'       => _x( 'Radio Buttons', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Radio Buttons.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => true ),
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
	 * @return string
	 */
	public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {

		$sanitized = '';
		$options   = bblpro_prepare_meta_options( $field_settings );

		foreach ( $options as $option ) {
			if ( $option['value'] == $meta_value ) { // NOTE: the loose comaprision is intentional.
				$sanitized = $meta_value;
			}
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
			return new \WP_Error( 'invalid_value', __( 'Invalid selection.', 'buddyblog-pro' ) );
		}
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {

		$atts = array(
			'type' => $this->type,
			'name' => $args['name'],
			'id'   => $args['id'],
		);
		?>

		<label for='<?php echo $atts["id"]; ?>' class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $atts["type"] ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
		</label>
		<?php
		foreach ( $args['options'] as $option ) {
			echo "<label><input type='radio' name='" . esc_attr( $atts['name'] ) . "' " . checked( $option['value'], $args['value'], false ) . "  value='" . esc_attr( $option['value'] ) . "' /> {$option['label']}</label>";
		}
		?>

		<?php
	}
	/**
	 * Field settings Args.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {
	    ?>
		<div class="bbl-row bbl-row-cf bbl-row-cf-field-radio-options">
			<label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Options:', 'buddyblog-pro' ); ?>
			</label>
			<div class="bbl-col-right bbl-col-cf-field-radio-options">
				<input type="text" placeholder="<?php _e( 'e.g one,two,three', 'buddyblog-pro' ); ?>" name="bbl-input-cf-field-radio-options"/>
				<?php _e( 'Comma separated list of input options', 'buddyblog-pro' ); ?>
			</div>
		</div><!-- end of row -->

		<div class="bbl-row bbl-row-cf bbl-row-cf-field-radio-labels">
			<label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Option Labels:', 'buddyblog-pro' ); ?>
			</label>
			<div class="bbl-col-right bbl-col-cf-field-select-labels">
				<input type="text" placeholder="<?php _e( 'Labels', 'buddyblog-pro' ); ?>" name="bbl-input-cf-field-radio-labels"/>
				<?php _e( 'Comma separated list of labels for each option.', 'buddyblog-pro' ); ?>
			</div>
		</div><!-- end of row -->
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @param int    $form_id form id.
	 * @param string $key field key.
	 * @param array  $settings field settings.
	 *
	 * @return \WP_Error|array
	 */
	public function prepare_settings( $form_id, $key, $settings ) {
		$common_settings = $this->prepare_common_field_settings( $form_id, $key, $settings );

		$multi_field_options = $this->prepare_common_multivalued_field_options_settings( $form_id, $key, $settings );

		return array_merge( $common_settings, $multi_field_options );
	}

	/**
	 * Markup for the form fields list view in the admin.
	 *
	 * @param array $field field settings.
	 */
	public function admin_fields_list_field_markup( $field ) {
		$this->print_common_field_settings( $field );
		$this->print_multi_value_field_options( $field );
	}

}
