<?php
/**
 * Admin functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Print core fields list table.
 *
 * @param int $form_id form id.
 */
function bblpro_print_core_fields_table( $form_id ) {
	$form = get_post( $form_id ); // making form object available to the loaded file.
	require buddyblog_pro()->path . 'src/admin/templates/core-fields-list.php';
}

/**
 * Print core fields list table.
 *
 * @param int $form_id $form id.
 */
function bblpro_print_custom_fields_table( $form_id ) {
	$form = get_post( $form_id ); // making form object available to the loaded file.
	require buddyblog_pro()->path . 'src/admin/templates/custom-fields-list.php';
}

/**
 * Get post details
 *
 * @param int $form_id Post id.
 *
 * @return array|null
 */
function bblpro_get_exportable_form_data( $form_id ) {

	$post = get_post( $form_id );

	if ( ! $post ) {
		return null;
	}

	$details = array(
		'post_status' => $post->post_status,
		'post_title'  => $post->post_title,
		'post_type'   => $post->post_type,
	);

	$meta_values = get_post_custom( $form_id );

	$excluded_meta_keys = apply_filters( 'bblpro_post_details_excluded_meta_keys', array( '_edit_lock', '_edit_last' ) );

	foreach ( $meta_values as $key => $value ) {

		if ( in_array( $key, $excluded_meta_keys, true ) ) {
			continue;
		}

		// Due to post custom need to pick first value of array.
		$details['meta'][ $key ] = maybe_unserialize( current( $value ) );
	}

	return $details;
}
