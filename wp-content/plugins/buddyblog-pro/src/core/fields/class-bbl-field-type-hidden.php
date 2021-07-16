<?php
/**
 * Hidden Field Type
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
 * Hidden Field Type
 */
class BBL_Field_Type_Hidden extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'hidden',
				'label'       => _x( 'Hidden', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Hidden field type.', 'Field type label', 'buddyblog-pro' ),
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
	 * @return string
	 */
	public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {
		return $field_settings['default']; // Hidden field always retains the default value specified.
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
        // Yes, the non strict comparion is intentional.
		if ( $meta_value != $field_settings['default'] ) {
			return new \WP_Error( 'invalid_value', __( 'Please provide textual input.', 'buddyblog-pro' ) );
		}

		return true;
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {

		$atts = array(
			'type'  => $this->type,
			'name'  => $args['name'],
			'id'    => $args['id'],
			'value' => $args['default'],
			'class' => 'bbl-hidden-input',
		);
		?>
        <input <?php echo $this->get_html_attributes( $atts ); ?> />
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
		return $this->prepare_common_field_settings( $form_id, $key, $settings );
	}

}
