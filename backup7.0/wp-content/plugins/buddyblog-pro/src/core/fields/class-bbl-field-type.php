<?php
/**
 *  Custom Field Type Base class
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
 * Base Field Type.
 */
abstract class BBL_Field_Type {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Field label
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Field type description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Supported features
	 *
	 * @var array
	 */
	public $supports = array();

	/**
	 * Supported Option in case checkbox,radio and select type
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Field type category
	 *
	 * @var string
	 */
	public $category = '';

	/**
	 * BBLPRO_Field constructor.
	 *
	 * @param array $args {
	 *      @type string $type            Field type.
	 *      @type string $label           Field label.
	 *      @type string $description     Field label.
	 *      @type array  $supports Does field type support options.
	 *      @type array  $settings         Field options.
	 *      @type string $category        Field category.
	 * } Field args.
	 */
	protected function __construct( $args ) {

		$default = array(
			'type'        => '',
			'label'       => '',
			'description' => '',
			'supports'    => array(),
			'settings'    => array(),
		);

		$args = wp_parse_args( $args, $default );

		$this->type        = $args['type'];
		$this->label       = $args['label'];
		$this->description = $args['description'];
		$this->supports    = is_array( $args['supports'] ) ? $args['supports'] : array();
		$this->settings    = $args['settings'];
	}


	/**
	 * Check if supports a feature.
	 *
	 * @param string $feature feature name.
	 *
	 * @return mixed|null
	 */
	public function supports( $feature ) {
		$supported_feature = isset( $this->supports[ $feature ] ) ? $this->supports[ $feature ] : null;

		return apply_filters( 'bblpro_field_type_supported_feature', $supported_feature, $feature, $this );
	}

	// admin field management.

	/**
	 * Sanitizes field settings.
	 *
	 * @param int   $form_id form id.
	 * @param array $args field settings.
	 *
	 * @return array sanitized array.
	 */
	public function sanitize_settings( $form_id, $args ) {
		return wp_unslash( $args );
	}

	/**
	 * Validate field settings.
	 *
	 * @param int   $form_id form id.
	 * @param array $args field settings.
	 *
	 * @return \WP_Error|array
	 */
	public function validate_settings( $form_id, $args ) {
		return $args;
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
	abstract public function prepare_settings( $form_id, $key, $settings );

	/**
	 * Get post meta.
	 *
	 * @param int    $post_id post id.
	 * @param string $key meta key.
	 *
	 * @return mixed
	 */
	public function get_field_data_raw( $post_id, $key ) {

		if ( metadata_exists( 'post', $post_id, $key ) ) {
			$data = get_post_meta( $post_id, $key, empty( $this->supports['multiple'] ) );
		} else {
			$data = null; // this will enforce default value to be used.
		}

		return $data;
	}

	/**
	 * Get meta value for display.
	 *
	 * @param int    $post_id post id.
	 * @param string $key meta key.
	 *
	 * @return mixed
	 */
	public function get_field_data( $post_id, $key ) {
		return $this->format_for_display( $this->get_field_data_raw( $post_id, $key ), $key, $post_id );
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
	abstract public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 );

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
	abstract public function validate_value( $meta_value, $field_settings, $form_id, $post_id = 0 );

	/**
	 * Save data for a field.
	 *
	 * @param int    $post_id post id.
	 * @param string $meta_key meta key.
	 * @param array  $meta_value value.
	 * @param array  $field_settings field settings.
	 */
	public function save_value( $post_id, $meta_key, $meta_value, $field_settings ) {

		if ( ! empty( $this->supports['multiple'] ) ) {
			$this->save_multi_valued_field_data( $post_id, $meta_key, $meta_value );
		} else {
			$this->save_single_valued_field_data( $post_id, $meta_key, $meta_value );
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

		if ( null == $value ) {
			$value = '';
		}

		return $value;
	}

	/**
	 * Markup for the post create/edit form field.
	 *
	 * @param array $args args.
	 */
	abstract public function edit_field_markup( $args );

	/**
	 * Markup for the admin form manage page, add field options.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {
	}

	/**
	 * Markup for the forem fields list view in the admin.
	 *
	 * @param array $field field settings.
	 */
	public function admin_fields_list_field_markup( $field ) {
		$this->print_common_field_settings( $field );
	}

	/**
	 * Get default value from field settings.
	 *
	 * @param array $field_settings field settings.
	 *
	 * @return mixed|null
	 */
	protected function get_default_field_value( $field_settings ) {

		if ( isset( $field_settings['default'] ) && '' !== $field_settings['default'] ) {
			$default = $field_settings['default'];
		} else {
			$default = null;
		}

		return $default;

	}

	/**
	 * Save data for single valued field(such as text, Number etc)
	 *
	 * @param int    $post_id post id.
	 * @param string $key meta key.
	 * @param array  $value value.
	 */
	protected function save_single_valued_field_data( $post_id, $key, $value ) {
		return update_post_meta( $post_id, $key, $value );
	}

	/**
	 * Save data for multi valued field(such as Checkbox, Multi select etc)
	 *
	 * @param int    $post_id post id.
	 * @param string $key meta key.
	 * @param array  $value value.
	 */
	protected function save_multi_valued_field_data( $post_id, $key, $value ) {

		$value = (array) $value;
		// for multi valued field, delete all old values before adding.
		if ( $post_id ) {
			delete_post_meta( $post_id, $key );
		}

		foreach ( $value as $val ) {
			add_post_meta( $post_id, $key, $val );
		}
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
	protected function prepare_common_field_settings( $form_id, $key, $settings ) {

		$common_settings = array(
			'type'        => $this->type,
			'key'         => $key,
			'label'       => isset( $settings['label'] ) ? esc_html( wp_unslash( $settings['label'] ) ) : '',
			'is_required' => empty( $settings['is_required'] ) ? 0 : 1,
			'default'     => isset( $settings['default_value'] ) ? wp_unslash( $settings['default_value'] ) : '',
			'placeholder' => isset( $settings['placeholder'] ) ? wp_strip_all_tags( wp_unslash( $settings['placeholder'] ) ) : '',
		);

		return $common_settings;
	}

	/**
	 * Prepare options for saving in the field details of multi field(radio, checkbox,select, multiselect).
	 *
	 * @param int    $form_id form id.
	 * @param string $key field key.
	 * @param array  $settings field settings.
	 *
	 * @return \WP_Error|array
	 */
	protected function prepare_common_multivalued_field_options_settings( $form_id, $key, $settings ) {

		$option_key       = 'bbl-input-cf-field-' . $this->type . '-options';
		$option_value_key = 'bbl-input-cf-field-' . $this->type . '-labels';

		$data = array();
		// @todo add Sanitization for single/multi value in future.
		$data['field-options-keys']   = isset( $_POST[ $option_key ] ) ? wp_unslash( $_POST[ $option_key ] ) : '';
		$data['field-options-labels'] = isset( $_POST[ $option_value_key ] ) ? wp_unslash( $_POST[ $option_value_key ] ) : '';

		return $data;
	}

	/**
	 * Prints common settings for the field.
	 *
	 * @param array $field field settings.
	 */
	protected function print_common_field_settings( $field ) {
		// print common settings.
		?>
        <p><?php _e( 'Type: ', 'buddyblog-pro' ); ?><?php echo esc_html( $this->label ); ?></p>
        <p><?php _e( 'Label: ', 'buddyblog-pro' ); ?><?php echo esc_html( $field['label'] ); ?></p>
        <p><?php _e( 'Key: ', 'buddyblog-pro' ); ?><?php echo esc_html( $field['key'] ); ?></p>
        <p>
			<?php if ( ! empty( $field['is_required'] ) ) : ?>
				<?php _e( 'Required: Yes', 'buddyblog-pro' ); ?>
			<?php else : ?>
				<?php _e( 'Required: No', 'buddyblog-pro' ); ?>
			<?php endif; ?>
        </p>
		<?php if ( ! empty( $field['placeholder'] ) && $this->supports( 'placeholder' ) ): ?>
            <p><?php _e( 'Placeholder:', 'buddyblog-pro' ); ?><?php echo wp_kses_data( $field['placeholder'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Print the field options for the multi value fields.
	 *
	 * Used in the admin Fields list screen.
	 *
	 * @param array $field Field settings.
	 */
	protected function print_multi_value_field_options( $field ) {
		?>
        <p>
			<?php _e( 'Options->Keys: ', 'buddyblog-pro' ); ?>
			<?php echo isset( $field['field-options-keys'] ) ? $field['field-options-keys'] : ''; ?>
        </p>
        <p>
			<?php _e( 'Options->Labels: ', 'buddyblog-pro' ); ?>
			<?php echo isset( $field['field-options-labels'] ) ? $field['field-options-labels'] : ''; ?>
        </p>
		<?php
	}

	/**
	 * Get html element attributes as string.
	 *
	 * @param array $attributes attributes.
	 *
	 * @return string
	 */
	protected function get_html_attributes( $attributes ) {

		$retval = '';
		foreach ( $attributes as $attr => $value ) {
			// Numeric keyed array.
			if ( is_numeric( $attr ) ) {
				$retval .= sprintf( ' %s', esc_attr( $value ) );

				// Associative keyed array.
			} else {
				$retval .= sprintf( ' %s="%s"', sanitize_key( $attr ), esc_attr( $value ) );
			}
		}

		return $retval;
	}

	/**
     * Show field description.
     *
	 * @param string $description description.
	 */
	protected function show_description( $description = '' ) {

		if ( empty( $description ) ) {
			return;
		}
		?>
        <div class="bbl-field-description bbl-field-<?php echo esc_attr( $this->type ); ?>-description">
			<?php echo wpautop( wp_kses_data( $description ) ); ?>
        </div>
		<?php
	}

}

