<?php
/**
 * General functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get form post type.
 *
 * @return string
 */
function bblpro_get_form_post_type() {
	return 'buddyblogpro_form';
}

/**
 * Get an option.
 *
 * @param string $option_name option name.
 * @param mixed  $default default value.
 *
 * @return mixed
 */
function bblpro_get_option( $option_name, $default = null ) {

	$settings = (array) get_option( 'buddyblog-pro', array() );

	if ( isset( $settings[ $option_name ] ) ) {
		return $settings[ $option_name ];
	}

	return $default;
}

/**
 * Get teh default settings for BuddyBlog.
 *
 * @return array
 */
function bblpro_get_default_settings() {

	return array(
		'enabled_post_types'                     => array(),
		'post_root_tab_enable'                   => 1,
		'post_tab_available_roles'               => array( 'all' => 'all' ),
		'post_tab_visible_roles'                 => array( 'all' => 'all' ),
		'post_list_tab_available_roles'          => array( 'all' => 'all' ),
		'post_list_tab_visible_roles'            => array( 'all' => 'all' ),
		'post_action_create_enabled'             => 1,
		'post_create_tab_available_roles'        => array( 'logged_in' => 'logged_in' ),
		'post_create_tab_visible_roles'          => array( 'self' => 'self' ),
		'post_edit_tab_available_roles'          => array( 'logged_in' => 'logged_in' ),
		'post_edit_tab_visible_roles'            => array( 'self' => 'self' ),
		'post_published_tab_available_roles'     => array( 'logged_in' => 'logged_in' ),
		'post_published_tab_visible_roles'       => array( 'self' => 'self' ),
		'post_delete_tab_available_roles'        => array( 'logged_in' => 'logged_in' ),
		'post_delete_tab_visible_roles'          => array( 'self' => 'self' ),
		'post_action_delete_enable_confirmation' => 1,
		'post_pending_tab_available_roles'       => array( 'logged_in' => 'logged_in' ),
		'post_pending_tab_visible_roles'         => array( 'self' => 'self' ),
		'post_draft_tab_available_roles'         => array( 'logged_in' => 'logged_in' ),
		'post_draft_tab_visible_roles'           => array( 'self' => 'self' ),
		'post_view_tab_available_roles'          => array( 'all', 'all' ),
	);
}

/**
 * Get an array of enabled post types.
 *
 * @return array
 */
function bblpro_get_enabled_post_types() {
	return (array) bblpro_get_option( 'enabled_post_types', array() );
}

/**
 * Is BuddyBlog Pro enabled for this post type.
 *
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_post_type_enabled( $post_type ) {
	return in_array( $post_type, bblpro_get_enabled_post_types(), true );
}

/**
 * Get available post types for use.
 *
 * @return string[]
 */
function bblpro_get_available_post_types() {
	// Let developers filter the post type list.
	$args = array(
		'_builtin' => false,
	);

	$show_private = true;

	if ( ! $show_private ) {
		$args['public'] = true;
	}

	$post_types = get_post_types( $args );
	// always include 'post'.
	$post_types = array_merge( array( 'post' ), $post_types );

	$excluded = array(
		'buddyblogpro_form',
		'bp-email',
		'product',
		'order',
		'bp-member-type',
		'forum',
		'topic',
		'reply',
		'bpptc_profile_tab',
		'bpfs_suggestion_rule',
		'buddyboss_fonts',
		'bp-invite',
	);

	$post_types = array_diff( $post_types, $excluded );

	return apply_filters( 'bblpro_available_post_types', $post_types );
}

/**
 * Check if the given comment status is valid.
 *
 * @param int    $form_id form id.
 * @param string $status status.
 *
 * @return bool
 */
function bblpro_is_valid_comment_status( $form_id, $status ) {
	return (bool) apply_filters(
		'bblpro_is_valid_comment_status',
		in_array( $status, array( 'open', 'closed' ), true ),
		$status,
		$form_id
	);
}

/**
 * Is it the add/edit Form post type screen
 *
 * @return bool
 */
function bblpro_is_form_admin() {

	if ( is_admin() && function_exists( 'get_current_screen' ) && bblpro_get_form_post_type() === get_current_screen()->post_type ) {
		return true;
	}

	return false;
}

/**
 * Get query args for home.
 *
 * @param array $args query args.
 *
 * @return array
 */
function bblpro_get_posts_query_args( $args ) {

	if ( bp_is_my_profile() || is_super_admin() ) {
		$status = 'any';
	} else {
		$status = 'publish';
	}

	$current_page = bp_action_variable( 1 );
	$current_page = $current_page ? $current_page : 1;

	$args = wp_parse_args(
		$args,
		array(
			'post_type' => '',
			'status'    => $status,
			'paged'     => $current_page,
			'user_id'   => bp_displayed_user_id(),
		)
	);

	$query_args = array(
		'author'      => $args['user_id'],
		'post_type'   => $args['post_type'],
		'post_status' => $args['status'], // any.
		'paged'       => intval( $args['paged'] ),
	);

	if ( bblpro_get_option( $args['post_type'] . '_show_front_posts_only', 0 ) ) {
		$query_args['meta_query'] = array(
			array(
				'key'     => '_is_buddyblog_post',
				'compare' => '=',
				'value'   => 1,
			),
		);
	}

	return $query_args;
}

/**
 * Generate pagination links
 *
 * @global WP_Query $wp_query
 */
function bblpro_paginate() {

	// get total number of pages.
	global $wp_query;
	$total = $wp_query->max_num_pages;

	// only bother with the rest if we have more than 1 page!
	if ( $total > 1 ) {
		// get the current page.
		$current_page = get_query_var( 'paged' );

		if ( ! $current_page ) {
			$current_page = 1;
		}

		// structure of “format” depends on whether we’re using pretty permalinks.
		$perma_struct = get_option( 'permalink_structure' );

		$format   = empty( $perma_struct ) ? '&page=%#%' : 'page/%#%/';
		$base_url = bblpro_get_post_type_tab_url( bp_displayed_user_id(), bblpro_get_current_post_type() ) . bp_current_action();
		$base     = trailingslashit( $base_url );

		echo paginate_links(
			array(
				'base'     => $base . '%_%',
				'format'   => $format,
				'current'  => $current_page,
				'total'    => $total,
				'mid_size' => 4,
				'type'     => 'list',
			)
		);
	}
}

/**
 * Get current post for editing.
 *
 * @return WP_Post|null
 */
function bblpro_get_current_editable_post() {

	$post = null;
	if ( 'edit' === bblpro_get_current_action() ) {

		$post_id = bblpro_get_context_post_id();

		if ( $post_id && is_numeric( $post_id ) ) {
			$post = get_post( $post_id );
		}
	}

	return $post;
}

/**
 * Get current post id for editing.
 *
 * @return int
 */
function bblpro_get_current_editable_post_id() {

	$post = bblpro_get_current_editable_post();

	return $post ? $post->ID : 0;
}

/**
 * Get current post being viewed.
 *
 * @return WP_Post|null
 */
function bblpro_get_queried_post() {

	$post = null;
	if ( 'view' === bblpro_get_current_action() && is_numeric( bp_action_variable( 0 ) ) ) {
		$post = get_post( bp_action_variable( 0 ) );
	}

	return $post;
}

/**
 * Get current post for editing.
 *
 * @return int
 */
function bblpro_get_queried_post_id() {

	$post = bblpro_get_queried_post();

	return $post ? $post->ID : 0;
}

/**
 * Get referer url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param string $action action.
 * @param string $screen current screen.
 *
 * @return string
 */
function bblpro_get_referrer_url( $user_id, $post_type, $action = '', $screen = '' ) {
	$redirect = '';

	switch ( $screen ) {

		case 'create':
			$redirect = bblpro_get_post_type_create_tab_url( $user_id, $post_type );
			break;
		case 'published':
			$redirect = bblpro_get_post_type_published_tab_url( $user_id, $post_type );
			break;
		case 'pending':
			$redirect = bblpro_get_post_type_pending_tab_url( $user_id, $post_type );
			break;
		case 'draft':
			$redirect = bblpro_get_post_type_draft_tab_url( $user_id, $post_type );
			break;
		case 'list':
		default:
			$redirect = bblpro_get_post_type_tab_url( bp_displayed_user_id(), $post_type );
			break;
	}

	return apply_filters( 'bblpro_referrer_url', $redirect, $user_id, $post_type, $action, $screen );
}

/**
 * Is admin bar menu enabled for the post type.
 *
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_admin_bar_enabled( $post_type ) {
	return (bool) bblpro_get_option( $post_type . '_admin_bar_enabled', 1 );
}

/**
 * Get mime type based one extension.
 *
 * @param string $extension extension.
 *
 * @return false|string
 */
function bblpro_get_mime_type( $extension = null ) {

	if ( ! $extension ) {
		return false;
	}

	$mime_types = wp_get_mime_types();
	$extensions = array_keys( $mime_types );

	foreach ( $extensions as $_extension ) {
		if ( preg_match( "/{$extension}/i", $_extension ) ) {
			return $mime_types[ $_extension ];
		}
	}

	return false;
}

/**
 * Returns a list of available Editor types.
 *
 * @return array
 */
function bblpro_get_registered_editors() {

	// due to backward compatibility, we use 'editor' for tinymce.
	$editors = array(
		'editor' => __( 'Tinymce Editor', 'buddyblog-pro' ),
	);

	return apply_filters( 'bblpro_registered_editors', $editors );
}

/**
 * Returns supported available editor type.
 *
 * @param string $type expected type.
 *
 * @return string
 */
function blpro_get_supported_editor_type( $type ) {

	$editors = bblpro_get_registered_editors();

	if ( isset( $editors[ $type ] ) ) {
		return $type;
	}

	return 'editor';// fallback to tinyce.
}

/**
 * Returns supported available editor type.
 *
 * @param string $type expected type.
 *
 * @return string
 */
function blpro_get_supported_editor_name( $type = '' ) {

	if ( ! empty( $type ) ) {
		$editors = bblpro_get_registered_editors();

		$label = isset( $editors[ $type ] ) ? $editors[ $type ] : __( 'Richtext editor' );

	} else {
		$label = "N/A";
	}

	return $label;

}