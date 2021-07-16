<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Get the visibility setting
 *
 * @param int    $user_id numeric user id.
 * @param string $key key name.
 *
 * @return mixed
 */
function bp_profile_visibility_get_settings( $user_id, $key ) {

	$default_settings = bp_profile_visibility_get_default_settings();
	/*apply_filters( 'bp_profile_visibility_default_settings', array(
		'bp_profile_visibility'         => 'public',//everyone
		'bp_hide_last_active'           => 'no',
		'bp_exclude_in_search'          => 'no',
		'bp_exclude_in_dir'             => 'no',
		'bp_allow_friendship_request'   => 'yes',
		'bp_friends_list_visibility'    => 'public',
	) )  ;
	*/
	$settings = bp_get_user_meta( $user_id, $key, true );

	if ( empty( $settings ) ) {
		$settings = empty( $default_settings[ $key ] ) ? '' : $default_settings[ $key ];
	}

	// for each key, we need to have some default value?
	return apply_filters( 'bp_profile_visibility_get_settings', $settings, $key );
}

/**
 * Get option.
 *
 * @param string $key key.
 * @param mixed  $default default.
 *
 * @return mixed
 */
function bp_profile_visibility_get_option( $key, $default = null ) {
	$settings = bp_profile_visibility_get_default_settings();

	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Get default settings
 *
 * @return mixed
 */
function bp_profile_visibility_get_default_settings() {
	return bp_get_option( 'bp_profile_visibility_settings' );
}

/**
 * Can the user update settings?
 *
 * @param int $user_id user id.
 *
 * @return bool
 */
function bp_profile_visibility_user_can_update_settings( $user_id = 0 ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	return apply_filters( 'bp_profile_visibility_user_can_update_settings', bp_is_my_profile() || bp_profile_visibility_is_admin_user() );

}

/**
 * Should we show the adminbar menu?
 *
 * @return bool
 */
function bp_profile_visibility_show_adminbar_menu() {
	return apply_filters( 'bp_profile_visibility_show_adminbar_menu', true );
}

/**
 * Can the visitor see friends tab of the displayed user.
 *
 * @param int $user_id displayed user id.
 * @param int $visitor_id visitor id.
 *
 * @return bool
 */
function bp_profile_visibility_can_see_friends_list( $user_id, $visitor_id ) {
	// by default assume hidden.
	$show    = false;
	$privacy = bp_profile_visibility_get_settings( $user_id, 'bp_friends_list_visibility' );

	if ( empty( $privacy ) || 'public' === $privacy ) {
		$show = true;
	} elseif ( 'loggedin' === $privacy && is_user_logged_in() ) {
		$show = true;
	} elseif ( 'friends' === $privacy && function_exists( 'friends_check_friendship' ) && friends_check_friendship( $user_id, $visitor_id ) ) {
		$show = true;
	} elseif ( 'groups' === $privacy && bp_profile_visibility_has_common_group( $user_id, $visitor_id ) ) {
		$show = true;
	}

	if ( $user_id == $visitor_id ) {
		$show = true;
	}

	return $show;
}

/**
 * Check if the visitor can see user profile
 *
 * @param int $user_id displayed user id.
 * @param int $visitor_id visitor id.
 *
 * @return bool
 */
function bp_profile_visibility_is_visible_profile( $user_id, $visitor_id ) {
	$can_see = false;

	if ( ! function_exists( 'buddypress' ) || ! $user_id ) {
		return false;
	}

	$privacy = bp_profile_visibility_get_settings( $user_id, 'bp_profile_visibility' );
	// public or super admin, always true.
	if ( 'public' == $privacy || bp_profile_visibility_is_admin_user( $visitor_id ) ) {
		return true;
	}

	// if we are here, the user must be logged in.
	if ( ! $visitor_id ) {
		return false;
	}

	if ( 'loggedin' === $privacy ) {
		$can_see = true;
	} elseif ( bp_is_my_profile() ) {
		$can_see = true;
	} elseif ( 'friends' === $privacy && function_exists( 'friends_check_friendship' ) && friends_check_friendship( $user_id, $visitor_id ) ) {
		$can_see = true;
	} elseif ( 'groups' === $privacy && bp_profile_visibility_has_common_group( $visitor_id, $user_id ) ) {
		$can_see = true;
	}

	return apply_filters( 'bp_profile_visibility_is_visible_profile', $can_see, $user_id, $visitor_id );
}

/**
 * Do the users have common group?
 *
 * @param int $user_id displayed user id.
 * @param int $other_id visitor id.
 *
 * @return bool
 */
function bp_profile_visibility_has_common_group( $user_id, $other_id ) {

	$has_common = false;

	if ( ! bp_is_active( 'groups' ) ) {
		return false;
	}

	if ( empty( $user_id ) || empty( $other_id ) ) {
		return false;
	}
	$common_groups = bp_profile_visibility_get_common_groups( $user_id, $other_id );

	if ( ! empty( $common_groups ) ) {
		$has_common = true;
	}

	return $has_common;

}


/**
 * Get all the common groups between these two users
 *
 * @param int $user_id displayed user id.
 * @param int $other_id visitor id.
 *
 * @return mixed array of group ids
 */
function bp_profile_visibility_get_common_groups( $user_id, $other_id ) {

	global $wpdb;

	// members table.
	$table = buddypress()->groups->table_name_members;

	$query_user_groups       = $wpdb->prepare( "SELECT group_id FROM {$table} WHERE user_id = %d AND is_confirmed = %d ", $user_id, 1 );
	$query_other_user_groups = $wpdb->prepare( "SELECT group_id FROM {$table} WHERE user_id = %d AND is_confirmed = %d ", $other_id, 1 );

	// should we put a LIMIT clause too, I am not putting as someone may find this function useful for other purposes.
	$commpon_groups = $wpdb->get_col( "{$query_user_groups} AND group_id IN ({$query_other_user_groups})" );

	return $commpon_groups;
}

/**
 * Load template.
 *
 * @param string $template template file.
 * @param bool   $load load or return path.
 *
 * @return string
 */
function bp_profile_visibility_load_template( $template, $load = true ) {

	$found = locate_template( array( 'bp-profile-visibility-manager/' . $template ), false );
	if ( ! $found ) {
		$found = bp_profile_visibility_manager()->get_path() . 'templates/bp-profile-visibility-manager/' . $template;
	}

	if ( $load ) {
		require $found;
	} else {
		return $found;
	}
}


/**
 * Get the allowed keys that is stored per user.
 *
 * @return array
 */
function bp_profile_visibility_get_allowed_keys() {

	return array(
		'enable_friends_list_visibility',
		// 'protected_profile_redirection_policy',
		'bp_profile_visibility',
		'bp_friends_list_visibility',
		'bp_profile_group_tab_visibility',
		'bp_hide_last_active',
		'bp_exclude_in_search',
		'bp_exclude_in_dir',
		'bp_allow_friendship_request',
		'bp_allow_follow_request',
	);
}

/**
 * Check if user is admin.
 *
 * @return mixed|void
 */
function bp_profile_visibility_is_admin_user() {
	return apply_filters( 'bp_profile_visibility_is_admin_user', is_super_admin() );
}