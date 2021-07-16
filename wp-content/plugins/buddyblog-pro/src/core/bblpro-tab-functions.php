<?php
/**
 * BuddyBlog Pro tab related functions.
 *
 * @package    BuddyBlog_Pro
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

use BuddyBlog_Pro\Handlers\View_Helper;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Is post type as the root component tab enabled?
 *
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_root_tab_enabled( $post_type ) {
	return (bool) bblpro_get_option( $post_type . '_root_tab_enable', 1 );
}

/**
 * Get tab slug for the post type.
 *
 * @param string $post_type post type.
 * @param string $default   default label.
 *
 * @return string
 */
function bblpro_get_tab_label( $post_type, $default = '' ) {

	$label = bblpro_get_option( $post_type . '_tab_label' );

	// give defaults higher priority.
	if ( ! $label && $default ) {
		$label = $default;
	}

	if ( ! $label ) {
		$label = get_post_type_object( $post_type )->labels->singular_name;
	}

	return $label;
}

/**
 * Get tab slug for the post type.
 *
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_tab_slug( $post_type ) {

	$slug = bblpro_get_option( $post_type . '_tab_slug', $post_type );

	if ( ! $slug ) {
		$slug = $post_type;
	}

	return $slug;
}

/**
 * Is Tab for the given post type available on the profile of given user.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_tab_available( $user_id, $post_type ) {
	return bblpro_user_has_role_in( $user_id, bblpro_get_option( $post_type . '_tab_available_roles' ) );
}

/**
 * Is Tab for the given post type is visible to the given user.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param int    $visitor_id visitor user id.
 *
 * @return bool
 */
function bblpro_is_tab_visible( $user_id, $post_type, $visitor_id = null ) {
	return bblpro_visitor_has_role_in( $user_id, $visitor_id, bblpro_get_option( $post_type . '_tab_visible_roles' ) );
}

/**
 * Is the give action enabled for the post type.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param string $action action.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss..
 *
 * @return bool
 */
function bblpro_is_action_enabled( $user_id, $post_type, $action = 'list', $shortcode_id = 0 ) {

	if ( $shortcode_id ) {
		$is_enabled = get_post_meta( $shortcode_id, "_bbl_action_{$action}_enabled", true );
	} else {
		$is_enabled = bblpro_get_option( "{$post_type}_action_{$action}_enabled", 1 );
	}

	return (bool) $is_enabled;
}

/**
 * Get action slug.
 *
 * @param string $post_type post type.
 * @param string $action action.
 *
 * @return string
 */
function bblpro_get_action_slug( $post_type, $action ) {
	return bblpro_get_option( "{$post_type}_{$action}_tab_slug", $action );
}

/**
 * Get tab slug for the post type.
 *
 * @param string $post_type post type.
 * @param string $action action.
 * @param string $default   default label.
 *
 * @return string
 */
function bblpro_get_action_label( $post_type, $action, $default = '' ) {

	$label = bblpro_get_option( $post_type . '_' . $action . '_tab_label' );

	// give defaults higher priority.
	if ( ! $label && $default ) {
		$label = $default;
	}

	if ( ! $label ) {
		$type_object = get_post_type_object( $post_type );

		if ( ! $type_object ) {
			return $action;
		}

		switch ( $action ) {
			case 'list':
			default:
				$label = $type_object->labels->singular_name;
				break;
			case 'create':
				$label = _x( 'Create', 'Create sub tab title', 'buddyblog-pro' );
				break;
			case 'draft':
				$label = _x( 'Drafts', 'Drafts sub tab title', 'buddyblog-pro' );
				break;
			case 'published':
				$label = _x( 'Published', 'Published sub tab title', 'buddyblog-pro' );
				break;
			case 'pending':
				$label = _x( 'Pending', 'Pending sub tab title', 'buddyblog-pro' );
				break;
		}

		return $label;
	}

	return $label;
}

/**
 * Is the give action available for the user.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param string $action action.
 * @param int    $shortcode_id Optiona, Shortcode id(required if not using BuddyPress/BuddyBoss).
 *
 * @return bool
 */
function bblpro_is_action_available( $user_id, $post_type, $action = 'list', $shortcode_id = 0 ) {

	if ( $shortcode_id ) {
		$available_roles = (array) get_post_meta( $shortcode_id, "_bbl_{$action}_tab_available_roles", true );
	} else {
		$available_roles = (array) bblpro_get_option( "{$post_type}_{$action}_tab_available_roles" );
	}

	return bblpro_user_has_role_in( $user_id, $available_roles );
}

/**
 * Is the give action visible to the visitor user.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param string $action action.
 * @param int    $visitor_id visitor id.
 * @param int    $shortcode_id Optiona, Shortcode id(required if not using BuddyPress/BuddyBoss).
 *
 * @return bool
 */
function bblpro_is_action_visible( $user_id, $post_type, $action, $visitor_id, $shortcode_id = 0 ) {

	if ( $shortcode_id ) {
		// @todo add the visibility options when we add dashboard functionality to shortcode.
		$visible_roles = array( 'self' );
	} else {
		$visible_roles = bblpro_get_option( "{$post_type}_{$action}_tab_visible_roles" );
	}

	return bblpro_visitor_has_role_in( $user_id, $visitor_id, $visible_roles );
}

/**
 * Get default sub tab slug for the given post type.
 *
 * @param string $post_type post type.
 *
 * @return string|null
 */
function bblpro_get_default_sub_tab( $post_type ) {
	$action  = 'list';
	$sub_tab = bblpro_get_option( "{$post_type}_{$action}_tab_slug", $action );
	if ( empty( $sub_tab ) ) {
		$sub_tab = $action;
	}

	return $sub_tab;
}

/**
 * Is the action top level tab.
 *
 * Currently, In the first release, It can only be a child tab.
 *
 * @param string $post_type post type.
 * @param string $action action.
 *
 * @return bool
 */
function bblpro_is_toplevel_tab( $post_type, $action ) {
	$is = false;

	/*
	switch( $action ) {

		case 'list':
		default:
			$is = true;
			break;
		case 'draft':
			break;
		case 'create':
			break;
	}
	*/

	return $is;
}

/**
 * Get current post type.
 *
 * @return string|null
 */
function bblpro_get_current_post_type() {
	return View_Helper::instance()->post_type;
}

/**
 * Get current action.
 *
 * @return string|null
 */
function bblpro_get_current_action() {
	return View_Helper::instance()->action;
}

/**
 * Get post id in context.
 *
 * @return int
 */
function bblpro_get_context_post_id() {
	return View_Helper::instance()->post_id;
}

/**
 * Is it edit page.
 *
 * @return bool
 */
function bblpro_is_create_page() {
	return bblpro_get_current_action() === 'create';
}

/**
 * Is it edit page.
 *
 * @return bool
 */
function bblpro_is_edit_page() {
	return bblpro_get_current_action() === 'edit';
}
