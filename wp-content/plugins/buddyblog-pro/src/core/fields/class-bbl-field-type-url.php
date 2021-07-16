<?php
/**
 * Web URL Field Type Helper
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
 * Web URl Field implementation
 */
class BBL_Field_Type_Url extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'url',
				'label'       => _x( 'Web URL', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Web url field.', 'Field type label', 'buddyblog-pro' ),
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
			return new \WP_Error( 'invalid_value', __( 'Please provide a valid url.', 'buddyblog-pro' ) );
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
		?>
        <div class="bbl-row bbl-row-cf bbl-row-cf-field-url-link-type">
            <label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Link type:', 'buddyblog-pro' ); ?>
            </label>
            <div class="bbl-col-right bbl-col-cf-field-url-link-type">
                <label>
                    <input type="radio" name="bbl-input-cf-field-url-link-type" value="text">
					<?php _e( 'Show as text', 'buddyblog-pro' ); ?>
                </label>
                <label>
                    <input type="radio" name="bbl-input-cf-field-url-link-type" value="link" checked>
					<?php _e( 'Show as link', 'buddyblog-pro' ); ?>
                </label>
            </div>
        </div><!-- end of row -->
        <div class="bbl-row bbl-row-cf bbl-row-cf-field-url-target-option">
            <label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'On link click:', 'buddyblog-pro' ); ?>
            </label>
            <div class="bbl-col-right bbl-col-cf-field-url-target-option">
				<label>
                    <input type="radio" name="bbl-input-cf-field-url-target-option" value="_self">
                    <?php _e( 'Open in same Tab/Window', 'buddyblog-pro' ); ?>
                </label>
                <label>
                    <input type="radio" name="bbl-input-cf-field-url-target-option" value="_blank" checked>
		            <?php _e( 'Open in new Tab/Window', 'buddyblog-pro' ); ?>
                </label>
            </div>
        </div><!-- end of row -->
		<?php
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
		$common_settings = $this->prepare_common_field_settings( $form_id, $key, $settings );

		if (
			isset( $settings['bbl-input-cf-field-url-link-type'] )
			&& in_array(
				$settings['bbl-input-cf-field-url-link-type'],
				array(
					'link',
					'text',
				),
				true
			)
		) {
			$common_settings['link_type'] = $settings['bbl-input-cf-field-url-link-type'];
		}

		if (
			isset( $settings['bbl-input-cf-field-url-target-option'] )
			&& in_array(
				$settings['bbl-input-cf-field-url-target-option'],
				array(
					'_self',
					'_blank',
				),
				true
			)
		) {
			$common_settings['target'] = $settings['bbl-input-cf-field-url-target-option'];
		}

		return $common_settings;
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

		if ( empty( $value ) ) {
			return '';
		}

		$form_id        = bblpro_post_get_form_id( $post_id );
		$field_settings = bblpro_form_get_custom_field( $form_id, $key );

		$link_type = empty( $field_settings['link_type'] ) ? 'text' : $field_settings['link_type'];

		if ( 'text' === $link_type ) {
			return $value;
		}

		$target = empty( $field_settings['target'] ) ? '' : $field_settings['target'];

		return sprintf( '<a href="%s" target="%s">%s</a>', esc_url( $value ), esc_attr( $target ), esc_html( $value ) );
	}
}
