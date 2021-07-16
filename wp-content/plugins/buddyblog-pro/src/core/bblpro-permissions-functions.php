<?php
/**
 * Permissions API
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
 * Checks if the given user can manage forms in admin.
 *
 * @param int $user_id user id.
 *
 * @return bool
 */
function bblpro_user_can_manage_forms( $user_id ) {
	return apply_filters( 'bblpro_user_can_manage_forms', is_super_admin( $user_id ) );
}
/**
 * Checks if the given user can manage forms in admin.
 *
 * @param int $user_id user id.
 *
 * @return bool
 */
function bblpro_user_can_moderate_posts( $user_id ) {
	return apply_filters( 'bblpro_user_can_moderate_posts', user_can( $user_id, 'edit_others_posts' ) ); // editor.
}

/**
 * Can user create content of the given post type?
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_create_post( $user_id, $post_type, $shortcode_id = 0 ) {
	return bblpro_is_action_enabled( $user_id, $post_type, 'create', $shortcode_id ) && bblpro_is_action_available( $user_id, $post_type, 'create', $shortcode_id );
}

/**
 * Can user edit this post?
 *
 * @param int $user_id user id.
 * @param int $post_id post id.
 * @param int $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_edit_post( $user_id, $post_id, int $shortcode_id = 0 ) {

	$post = get_post( $post_id );

	// allow moderators.
	$can = false;
	if ( ! $user_id || ! $post ) {
		$can = false;
	} elseif ( bblpro_is_user_moderator( get_current_user_id(), $post->post_type ) ) {
		$can = true;
	} elseif ( $post->post_author == $user_id && bblpro_is_post_editing_enabled( $user_id, $post->post_type, $shortcode_id ) ) {
		$can = true;
	}

	return apply_filters( 'bblpro_user_can_edit_post', $can, $post_id, $user_id, $shortcode_id );
}

/**
 * Can user save this post as draft?
 *
 * @param int $user_id user id.
 * @param int $post_id post id.
 * @param int $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_draft_post( $user_id, $post_id, $shortcode_id = 0 ) {

	$post = get_post( $post_id );

	// allow moderators.
	$can = false;
	if ( ! $user_id || ! $post ) {
		$can = false;
	} elseif ( bblpro_is_user_moderator( get_current_user_id(), $post->post_type ) ) {
		$can = true;
	} elseif ( $post->post_author == $user_id && bblpro_is_post_drafting_enabled( $user_id, $post->post_type, $shortcode_id ) ) {
		$can = true;
	}

	return apply_filters( 'bblpro_user_can_draft_post', $can, $post_id, $user_id, $shortcode_id );
}

/**
 * Can user delete the given post?
 *
 * @param int $user_id user id.
 * @param int $post_id post id.
 * @param int $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_delete_post( $user_id, $post_id, $shortcode_id = 0 ) {

	$post = get_post( $post_id );

	$can = false;
	if ( ! $user_id || ! $post ) {
		$can = false;
	} elseif ( bblpro_is_user_moderator( get_current_user_id(), $post->post_type ) ) {
		$can = true;
	} elseif ( $post->post_author == $user_id && bblpro_is_post_deletion_enabled( $user_id, $post->post_type, $shortcode_id ) ) {
		$can = true;
	}

	return apply_filters( 'bblpro_user_can_delete_post', $can, $post_id, $user_id, $shortcode_id );
}

/**
 * Checks if user can upload to post type.
 *
 * @param int    $author_id post author id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_upload( $author_id, $post_type, $shortcode_id = 0 ) {
	return bblpro_is_action_enabled( $author_id, $post_type, 'upload', $shortcode_id ) && ( bblpro_is_user_moderator( $author_id, $post_type ) || bblpro_is_action_available($author_id, $post_type, 'upload', $shortcode_id ) );
}

/**
 * Can user view the given post?
 *
 * It is used when single post on profile is enabled.
 *
 * @param int $user_id visiting user id.
 * @param int $post_id post id.
 * @param int $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_user_can_view_post( $user_id, $post_id, $shortcode_id = 0 ) {

	$post = get_post( $post_id );

	if ( ! $user_id || ! $post ) {
		$can = false;
	} elseif ( bblpro_is_user_moderator( get_current_user_id(), $post->post_type ) ) {
		$can = true;
	} elseif ( $post->post_author == $user_id ) {
		$can = true;
	} else {
		$can = bblpro_can_user_view_post_type( $post->post_author, $user_id, $post->post_type, $shortcode_id );
	}

	return (bool) apply_filters( 'bblpro_user_can_view_post', $can, $post_id, $post->post_author, $user_id, $shortcode_id );
}

/**
 * Checks if user is moderator.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_user_moderator( $user_id, $post_type = '' ) {
	return (bool) apply_filters( 'bblpro_is_user_moderator', is_super_admin( $user_id ), $post_type );
}

/**
 * Checks if user can view post.
 *
 * @param int    $author_id post author id.
 * @param int    $visitor_id user id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_can_user_view_post_type( $author_id, $visitor_id, $post_type, $shortcode_id = 0 ) {

	if ( $shortcode_id ) {
		$allowed_roles = array( 'self' );
	} else {
		$allowed_roles = bblpro_get_option( $post_type . '_tab_visible_roles' );
	}

	return $author_id == $visitor_id || is_super_admin() || bblpro_visitor_has_role_in( $author_id, $visitor_id, $allowed_roles );
}

/**
 * Checks if deletion is enabled for the given post type.
 *
 * @param int    $author_id post owner user id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_is_post_deletion_enabled( $author_id, $post_type, int $shortcode_id = 0 ) {
	return (bool) bblpro_is_action_enabled( $author_id, $post_type, 'delete', $shortcode_id ) && bblpro_is_action_available( $author_id, $post_type, 'delete', $shortcode_id );
}

/**
 * Checks if editing setting is enabled for the given post type.
 *
 * @param int    $author_id user id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @return bool
 */
function bblpro_is_post_editing_enabled( $author_id, $post_type, int $shortcode_id = 0 ) {
	return (bool) bblpro_is_action_enabled( $author_id, $post_type, 'edit', $shortcode_id ) && bblpro_is_action_available( $author_id, $post_type, 'edit', $shortcode_id );
}

/**
 * Checks if editing setting is enabled for the given post type.
 *
 * @param int    $author_id user id.
 * @param string $post_type post type.
 * @param int    $shortcode_id shortcode identifier. required if not using BuddyPress/BuddyBoss.
 *
 * @todo add admin option.
 *
 * @return bool
 */
function bblpro_is_post_drafting_enabled( $author_id, $post_type, $shortcode_id = 0 ) {
	return true;// (bool) bblpro_is_action_enabled( $author_id, $post_type, 'edit', $shortcode_id ) && bblpro_is_action_available( $author_id, $post_type, 'edit', $shortcode_id );
}

/**
 * Does the visitor has any of the required roles.
 *
 * @param int   $user_id profile owner user id.
 * @param int   $visitor_id visitor id.
 * @param array $required_roles required roles to allow access.
 *
 * @return bool
 */
function bblpro_visitor_has_role_in( $user_id, $visitor_id, $required_roles ) {

	// Ensure that required roles is available as array.
	if ( ! is_array( $required_roles ) ) {
		$required_roles = (array) $required_roles;
	}

	// public access.
	if ( in_array( 'all', $required_roles, true ) ) {
		return true;
	}

	// all other roles need logged user.
	if ( ! is_user_logged_in() ) {
		return false;
	}

	// if we are here, user is logged in.
	if ( in_array( 'logged_in', $required_roles, true ) ) {
		$has = true;
	} elseif ( $user_id == $visitor_id && in_array( 'self', $required_roles, true ) ) {
		$has = true; // is the post owner.
	} elseif ( bp_is_active( 'friends' ) && in_array( 'friends', $required_roles, true ) && friends_check_friendship( $user_id, $visitor_id ) ) {
		$has = true; // is friend of post owner.
	} else {
		$has = bblpro_user_has_role_in( $visitor_id, $required_roles );
	}

	return $has;
}

/**
 * Checks if user has one of the given roles.
 *
 * @param int   $user_id user id.
 * @param array $roles roles.
 *
 * @return bool
 */
function bblpro_user_has_role_in( $user_id, $roles ) {

	// ensure that roles is an array.
	if ( ! is_array( $roles ) ) {
		$roles = (array) $roles;
	}

	if ( in_array( 'all', $roles, true ) ) {
		return true;
	}

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return false;
	}

	return (bool) array_intersect( $roles, $user->roles );
}
