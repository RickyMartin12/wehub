<?php
/**
 * Invalid field type placeholder
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
 * Represents invalid field type.
 */
class BBL_Field_Type_Invalid extends BBL_Field_Type {

	/**
	 * Constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'invalid',
				'label'       => _x( 'Invalid', 'Field type label', 'buddyblog-pro' ),
				'description' => _x( 'Used to represent un-registerd/invalid types.', 'Field type label', 'buddyblog-pro' ),
				'supports'    => array(
					'multiple' => false,
					'settings' => false,
				),
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
	 * @return array
	 */
	public function sanitize_value( $meta_value, $field_settings, $form_id, $post_id = 0 ) {
		return $meta_value;
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
		return new \WP_Error( 'invalid_type', __( 'Invalid Filed Type.', 'buddyblog-pro' ) );
	}

	/**
	 * Edit field markup for front end forms.
	 *
	 * @param array $args args.
	 */
	public function edit_field_markup( $args ) {
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
		return new \WP_Error( 'invalid_type', __( 'Invalid Filed Type.', 'buddyblog-pro' ) );
	}
}
