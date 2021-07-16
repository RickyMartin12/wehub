<?php
/**
 * Image Helper Field Type Helper
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
 * Image Field implementation
 */
class BBL_Field_Type_Image extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'image',
				'label'       => _x( 'Image', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Image type field.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array( 'settings' => false ),
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
			'bbl-field-type-image',
			buddyblog_pro()->url . 'src/core/fields/image-field-helper.js',
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

		$image = wp_get_attachment_image( $meta_value );

		if ( $image ) {
			return true;
		} else {
			return new \WP_Error( 'invalid_image', __( 'Please select a valid image.', 'buddyblog-pro' ) );
		}
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

		$image_id = empty( $args['value'] ) ? '' : absint( $args['value'] );
		?>

        <label for='<?php echo $atts["id"]; ?>' class='bbl-field-label bbl-field-label-type-<?php echo esc_attr( $this->type ); ?> bbl-field-label-field-<?php echo esc_attr( $atts["id"] ); ?>'>
			<?php echo $args['label']; ?>
			<?php echo $args['required']; ?>
        </label>

        <div class="bbl-field-type-<?php echo esc_attr( $this->type ); ?>-wrap">

            <div class="bbl-field-type-<?php echo esc_attr( $this->type ); ?>-preview">
		        <?php if ( $image_id ) : ?>
			        <?php echo wp_get_attachment_image( $image_id, 'full' ); ?>
                    <a href="#" class="bbl-field-type-image-delete-btn" title="<?php esc_attr_e( 'Remove', 'buddyblog-pro' ); ?>">X</a>
		        <?php endif; ?>
            </div>

            <button data-uploader-title="<?php _e( 'Select Image', 'buddyblog-pro' ); ?>" data-btn-title="<?php _e( 'Select', 'buddyblog-pro' ); ?>" class="bbl-field-label-type-<?php echo esc_attr( $this->type ); ?>-upload-btn"><?php _e( 'Select Image', 'buddyblog-pro' ); ?></button>

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

		$image_html = wp_get_attachment_image( $value, 'full' );
		if ( empty( $image_html ) ) {
			return '';
		}

		return '<span class="bbl-field-type-image-data">' . $image_html . '</span>';
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
		return $this->prepare_common_field_settings( $form_id, $key, $settings );
	}
}
