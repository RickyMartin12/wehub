<?php
/**
 * LearnDash Color Picker Settings Field.
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'LearnDash_Settings_Fields' ) ) && ( ! class_exists( 'LearnDash_Settings_Fields_Colorpicker' ) ) ) {

	/**
	 * Class LearnDash Color Picker Settings Field.
	 *
	 * @since 3.0.0
	 * @uses LearnDash_Settings_Fields
	 */
	class LearnDash_Settings_Fields_Colorpicker extends LearnDash_Settings_Fields {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->field_type = 'colorpicker';

			parent::__construct();
		}

		/**
		 * Function to crete the settiings field.
		 *
		 * @since 3.0.0
		 *
		 * @param array $field_args An array of field arguments used to process the ouput.
		 * @return void
		 */
		public function create_section_field( $field_args = array() ) {

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$field_args = apply_filters( 'learndash_settings_field', $field_args );

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$html = apply_filters( 'learndash_settings_field_html_before', '', $field_args );

			$html .= '<input ';
			$html .= $this->get_field_attribute_type( $field_args );
			$html .= $this->get_field_attribute_name( $field_args );
			$html .= $this->get_field_attribute_id( $field_args );
			$html .= $this->get_field_attribute_class( $field_args );
			$html .= $this->get_field_attribute_placeholder( $field_args );
			$html .= $this->get_field_attribute_misc( $field_args );
			$html .= $this->get_field_attribute_required( $field_args );

			if ( isset( $field_args['value'] ) ) {
				$html .= ' value="' . esc_attr( $field_args['value'] ) . '" ';
			} else {
				$html .= ' value="" ';
			}
			$html .= ' />';

			/** This filter is documented in includes/settings/settings-fields/class-ld-settings-fields-checkbox-switch.php */
			$html = apply_filters( 'learndash_settings_field_html_after', $html, $field_args );

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		/**
		 * Validate field
		 *
		 * @since 3.0.0
		 *
		 * @param mixed  $val Value to validate.
		 * @param string $key Key of value being validated.
		 * @param array  $args Array of field args.
		 *
		 * @return integer value.
		 */
		public function validate_section_field( $val, $key, $args = array() ) {
			return sanitize_text_field( $val );
		}

		// End of functions.
	}
}
add_action(
	'learndash_settings_sections_fields_init',
	function() {
		LearnDash_Settings_Fields_Colorpicker::add_field_instance( 'colorpicker' );
	}
);
