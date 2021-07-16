<?php
/**
 * Filed type functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

use BuddyBlog_Pro\Core\Fields\BBL_Field_Type;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Checkbox;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Date;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Editor;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_File;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Hidden;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Number;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Oembed;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Radio;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Select;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Select_Multi;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Text;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Textarea;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Url;
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Image;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get available core fields.
 *
 * @return array
 */
function bblpro_get_core_field_types() {

	return apply_filters(
		'bblpro_core_field_types',
		array(
			'post_title'   => _x( 'Post Title', 'Core post field label', 'buddyblog-pro' ),
			'post_content' => _x( 'Post Content', 'Core post field label', 'buddyblog-pro' ),
			'post_excerpt' => __( 'Post Excerpt', 'buddyblog-pro' ),
			'thumbnail'    => _x( 'Featured Image', 'Core post field label', 'buddyblog-pro' ),
		)
	);
}

/**
 * Get available custom fields.
 *
 * @return array
 */
function bblpro_get_custom_field_types() {

	$registered_types = bbl_get_registered_field_type_classes();

	foreach ( $registered_types as $type => $class ) {
		$object = bblpro_get_field_type_object( $type );

		if ( ! $type ) {
			continue;
		}

		// we should most probably check if this field is available for use?
		$types[ $type ] = $object->label;
	}

	return apply_filters( 'bblpro_custom_field_types', $types );
}

/**
 * Get a list of registered field types.
 *
 * @return BBL_Field_Type[]
 *
 * @todo  implement
 */
function bbl_get_registered_field_type_classes() {

	// field type class map.
	$types = array(
		'text'          => BBL_Field_Type_Text::class,
		'textarea'      => BBL_Field_Type_Textarea::class,
		'editor'        => BBL_Field_Type_Editor::class,
		'number'        => BBL_Field_Type_Number::class,
		'url'           => BBL_Field_Type_Url::class,
		'select'        => BBL_Field_Type_Select::class,
		'multiselect'   => BBL_Field_Type_Select_Multi::class,
		'radio'         => BBL_Field_Type_Radio::class,
		'checkbox'      => BBL_Field_Type_Checkbox::class,
		'oembed'        => BBL_Field_Type_Oembed::class,
		'hidden'        => BBL_Field_Type_Hidden::class,
		'image'         => BBL_Field_Type_Image::class,
		'file'          => BBL_Field_Type_File::class,
		'date'          => BBL_Field_Type_Date::class,
	);

	return (array) apply_filters( 'bblpro_registered_field_type_classes', $types );
}

/**
 * Get field type object.
 *
 * @param string $field_type field type.
 *
 * @return BBL_Field_Type
 */
function bblpro_get_field_type_object( $field_type ) {

	if ( empty( $field_type ) ) {
		return null;
	}

	static $field_type_objects = array();

	// check in cached type objects.
	if ( isset( $field_type_objects[ $field_type ] ) && is_a( $field_type_objects[ $field_type ], BBL_Field_Type::class ) ) {
		return $field_type_objects[ $field_type ];
	}

	$registered_types = bbl_get_registered_field_type_classes();

	if ( isset( $registered_types[ $field_type ] ) ) {
		$class_name  = $registered_types[ $field_type ];
		$type_object = new $class_name();
	} else {
		$type_object = null;
	}

	$field_type_objects[ $field_type ] = $type_object;

	return $type_object;
}

/**
 * Get the label for the core post field.
 *
 * @param string $filed_type type.
 *
 * @return string
 */
function bblpro_get_core_field_label( $filed_type ) {

	$fields = bblpro_get_core_field_types();

	return isset( $fields[ $filed_type ] ) ? $fields[ $filed_type ] : _x( 'Invalid Type.', 'Field type label', 'buddyblog-pro' );
}

/**
 * Get th label for the custom post field.
 *
 * @param string $field_type type.
 *
 * @return string
 */
function bblpro_get_custom_field_label( $field_type ) {

	$fields = bblpro_get_custom_field_types();

	return isset( $fields[ $field_type ] ) ? $fields[ $field_type ] : _x( 'Invalid Type.', 'Field type label', 'buddyblog-pro' );
}

/**
 * Prepare options as key/value pair.
 *
 * @param array $field_settings settings array.
 *
 * @return array
 */
function bblpro_prepare_meta_options( $field_settings ) {

	if ( empty( $field_settings['field-options-keys'] ) || empty( $field_settings['field-options-labels'] ) ) {
		return array();
	}

	$values  = explode( ',', $field_settings['field-options-keys'] );
	$labels  = explode( ',', $field_settings['field-options-labels'] );
	$options = array();
	$count   = count( $values ) < count( $labels ) ? count( $values ) : count( $labels );

	for ( $i = 0; $i < $count; $i ++ ) {
		$options[] = array(
			'value' => trim( $values[ $i ] ),
			'label' => trim( $labels[ $i ] ),
		);
	}

	return $options;
}

/**
 * Sanitize value.
 *
 * @param string $field_type field type.
 * @param mixed  $value value.
 * @param array  $field_settings field settings.
 * @param int    $form_id form id.
 * @param int    $post_id post id.
 *
 * @return mixed|null
 */
function bblpro_get_sanitized_value( $value, $field_settings, $form_id, $post_id = 0 ) {

	$field_type_object = bblpro_get_field_type_object( $field_settings['type'] );

	// Not a valid type, return null.
	if ( ! $field_type_object ) {
		$sanitized = null;
	} else {
		$sanitized = $field_type_object->sanitize_value( $value, $field_settings, $form_id, $post_id );
	}

	return apply_filters( 'bblpro_sanitized_value', $sanitized, $value, $field_settings, $field_type_object );
}
