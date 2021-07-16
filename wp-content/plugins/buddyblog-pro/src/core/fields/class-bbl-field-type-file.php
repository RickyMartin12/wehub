<?php
/**
 * File Field Type Helper
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
 * File Field implementation
 */
class BBL_Field_Type_File extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'file',
				'label'       => _x( 'File', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'File type field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => true ),
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Enqueue script.
	 */
	private function enqueue() {
		wp_enqueue_media();

		wp_enqueue_script(
			'bbl-field-type-file',
			buddyblog_pro()->url . 'src/core/fields/file-field-helper.js',
			array( 'jquery', 'bblogpro' ),
			buddyblog_pro()->version,
			true
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
			$sanitized = absint( $meta_value );
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

		if ( empty( $meta_value ) ) {
			return true;
		}

		$file_url = wp_get_attachment_url( $meta_value );

		if ( empty( $file_url ) ) {
			return new \WP_Error( 'invalid_file', __( 'Please select a valid file.', 'buddyblog-pro' ) );
		}

		$allowed_types = $field_settings['allowed_types'] ? explode( ',', $field_settings['allowed_types'] ) : array();

		$matched = false;
		foreach ( $allowed_types as $extension ) {
			$extension = trim( $extension );
			if ( preg_match( '!\.' . $extension . '$!i', $file_url ) ) {
				$matched = true;
				break;
			}
		}

		if ( ! $matched ) {
			return new \WP_Error( 'invalid_file', __( 'Please select a valid file type.', 'buddyblog-pro' ) );
		}

		return $matched;
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {

		$atts = array(
			'type'  => 'hidden',
			'name'  => $args['name'],
			'id'    => $args['id'],
			'value' => $args['value'],
		);

		$allowed_file_types = isset( $args['allowed_types'] ) ? explode( ',', $args['allowed_types'] ) : array();
		$allowed_types      = array();

		foreach ( $allowed_file_types as $extension ) {
			$mime = bblpro_get_mime_type( trim( $extension ) );
			if ( ! $mime ) {
				continue;
			}
			$allowed_types[] = $mime;
		}

		$allowed_types = join( ',', $allowed_types );

		$media_id = empty( $args['value'] ) ? '' : absint( $args['value'] );
		$file_url = $media_id ? wp_get_attachment_url( $media_id ) : '';
		?>

		<label for='<?php echo $atts["id"]; ?>' class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $this->type ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
		</label>

		<div class="bbl-field-type-<?php echo esc_attr( $this->type ); ?>-wrap">

			<div class="bbl-field-type-<?php echo esc_attr( $this->type ); ?>-preview">
				<?php if ( $media_id && $file_url ) : ?>
					<div class="bbl-field-edit-file-link">
						<?php printf( "<a href='%s' target='_new'>%s</a>", esc_url( $file_url ), basename( $file_url ) ); ?>
					</div>
					<a href="#" class="bbl-field-type-file-delete-btn" title="<?php esc_attr_e( 'Remove', 'buddyblog-pro' ); ?>">X</a>
				<?php endif; ?>
			</div>

			<button data-allowed-types="<?php echo esc_attr( $allowed_types );?>" data-uploader-title="<?php _e( 'Select file', 'buddyblog-pro' ); ?>" data-btn-title="<?php _e( 'Select', 'buddyblog-pro' ); ?>" class="bbl-field-label-type-<?php echo esc_attr( $this->type ); ?>-upload-btn"><?php _e( 'Select File', 'buddyblog-pro' ); ?></button>

			<input <?php echo $this->get_html_attributes( $atts ); ?> />
		</div>
		<?php if ( ! empty( $args['placeholder'] ) ) : ?>
			<?php $this->show_description( $args['placeholder'] ); ?>
		<?php endif; ?>

		<?php $this->enqueue(); ?>
		<?php
	}

	/**
	 * Format for display
	 *
	 * @param mixed  $value   Value.
	 * @param string $key     Key.
	 * @param int    $post_id Post id.
	 *
	 * @return string
	 */
	public function format_for_display( $value, $key, $post_id ) {

		if ( empty( $value ) ) {
			return '';
		}

		$file_url = wp_get_attachment_url( $value );
		if ( empty( $file_url ) ) {
			return '';
		}

		$file_html = sprintf( "<a href='%s' target='_new'>%s</a>", esc_url( $file_url ), basename( $file_url ) );

		return '<span class="bbl-field-type-file-data">' . $file_html . '</span>';
	}

	/**
	 * Prepare settings
	 *
	 * @param int    $form_id  Form id.
	 * @param string $key      Key.
	 * @param array  $settings Settings array.
	 *
	 * @return array|\WP_Error
	 */
	public function prepare_settings( $form_id, $key, $settings ) {
		$allowed_types = isset( $settings['bbl-input-cf-field-file-allowed-types'] ) ? trim( $settings['bbl-input-cf-field-file-allowed-types'] ) : '';

		if ( empty( $allowed_types ) ) {
			return new \WP_Error( 'allowed_type_missing', __( 'Please provide a list of allowed file types.', 'buddyblog-pro' ) );

		}
		$field_settings                  = $this->prepare_common_field_settings( $form_id, $key, $settings );
		$field_settings['allowed_types'] = $allowed_types;

		return $field_settings;
	}

	/**
	 * Field settings Args.
	 *
	 * @param array $args args.
	 */
	public function admin_field_settings_markup( $args = array() ) {
		?>
        <div class="bbl-row bbl-row-cf bbl-row-cf-field-file-options">
            <label class="bbl-label bbl-label-cf bbl-col-left">
				<?php _e( 'Allowed file types:', 'buddyblog-pro' ); ?>
            </label>
            <div class="bbl-col-right bbl-col-cf-field-select-options">
                <input type="text" placeholder="<?php _e( 'e.g png,pdf,jpeg etc', 'buddyblog-pro' ); ?>" name="bbl-input-cf-field-file-allowed-types"/>
				<?php _e( 'Comma separated list of allowed file extensions.', 'buddyblog-pro' ); ?>
            </div>
        </div><!-- end of row -->
		<?php
	}

	/**
	 * Markup for the form fields list view in the admin.
	 *
	 * @param array $field field settings.
	 */
	public function admin_fields_list_field_markup( $field ) {
		$this->print_common_field_settings( $field );

		echo '<p>' . sprintf( __( 'Allowed file types: %s', 'buddyblog-pro' ),  $field['allowed_types'] ) . '</p>';
	}
}
