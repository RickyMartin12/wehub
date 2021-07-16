<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Setup default privacy on account creation.
 *
 * @param int $user_id ser id.
 */
function bp_profile_visibility_add_initial_settings( $user_id ) {

	$default_settings = bp_profile_visibility_get_default_settings();

	if ( empty( $default_settings ) ) {
		return;
	}

	$allowed_keys = bp_profile_visibility_get_allowed_keys();

	foreach ( $default_settings as $key => $option ) {
		if ( ! in_array( $key, $allowed_keys ) ) {
			continue;
		}

		bp_update_user_meta( $user_id, $key, $option );
	}
}

add_action( 'user_register', 'bp_profile_visibility_add_initial_settings', 1 );


/**
 * Setting flag to detect friends query.
 *
 * @param array $args args.
 *
 * @return array
 */
function bp_profile_visibility_sniff_loop( $args ) {

	if ( ! empty( $args['user_id'] ) ) {

		bp_profile_visibility_loader()->set_data( 'is_visible', true );
	}

	return $args;
}
add_filter( 'bp_before_has_members_parse_args', 'bp_profile_visibility_sniff_loop' );

/**
 * Setup visibility flags before the user loop.
 */
function bp_profile_visibility_set_data() {

	if ( bp_is_active( 'friends' ) && bp_is_friends_component() ) {
		bp_profile_visibility_loader()->set_data( 'is_visible', true );
	}
}
add_filter( 'bp_before_members_loop', 'bp_profile_visibility_set_data' );
add_filter( 'bp_before_member_friend_requests_content', 'bp_profile_visibility_set_data' );


/**
 * Disable flag after the loop.
 */
function bp_profile_visibility_unset_data() {
	bp_profile_visibility_loader()->set_data( 'is_visible', false );
}
add_filter( 'bp_after_members_loop', 'bp_profile_visibility_unset_data' );
add_filter( 'bp_after_member_friend_requests_content', 'bp_profile_visibility_unset_data' );

/**
 * Setup flag for group members list.
 */
function bp_profile_visibility_set_conditions_for_group() {
	if ( ( bp_is_group_members() || bp_is_group_admin_screen( 'membership-requests' ) ) && bp_get_group_status() != 'public' ) {//only for private/hidden groups
		bp_profile_visibility_loader()->set_data( 'is_visible', true );
	}
}
add_action( 'bp_before_group_body', 'bp_profile_visibility_set_conditions_for_group' );
add_action( 'bp_before_group_membership_requests_admin', 'bp_profile_visibility_set_conditions_for_group' );

/**
 * Disable flag for group members.
 */
function bp_profile_visibility_unset_conditions_for_group() {

	if ( ( bp_is_group_members() || bp_is_group_admin_screen( 'membership-requests' ) ) && bp_get_group_status() != 'public' ) {
		bp_profile_visibility_loader()->set_data( 'is_visible', false );
	}
}
add_action( 'bp_after_group_body', 'bp_profile_visibility_unset_conditions_for_group' );
add_action( 'bp_after_group_membership_requests_admin', 'bp_profile_visibility_unset_conditions_for_group' );
add_action( 'bp_after_group_members_list', 'bp_profile_visibility_unset_conditions_for_group' );

/* for nouveau, we need to use the filter instead*/
add_filter( 'bp_after_group_has_members_parse_args', function ( $args ) {

	if ( empty( $args['group_id'] ) ) {
		return $args;
	}

	if ( bp_get_group_status( groups_get_group( $args['group_id'] ) ) != 'public' ) {
		bp_profile_visibility_loader()->set_data( 'is_visible', true );
	}

	return $args;
} );


/**
 * Hide friends widget if needed.
 *
 * @param array $sidebars widgets list.
 *
 * @return array
 */
function bp_profile_visibility_hide_friends_widget( $sidebars ) {

	// do not affect dashboard.
	if ( is_admin() ) {
		return $sidebars;
	}

	// also, let us not worry about non user pages.
	if ( ! bp_is_user() ) {
		return $sidebars;
	}

	// we are on user profile.
	if ( bp_profile_visibility_can_see_friends_list( bp_displayed_user_id(), bp_loggedin_user_id() ) ) {
		return $sidebars;
	}

	global $wp_registered_widgets, $_wp_sidebars_widgets;

	foreach ( $_wp_sidebars_widgets as $sidebar => &$widgets ) {
		if ( empty( $widgets ) || ! is_array( $widgets ) ) {
			continue;
		}

		foreach ( $widgets as $index => $widget_id ) {

			if ( ! isset( $wp_registered_widgets[ $widget_id ] ) ) {
				continue;
			}

			$widget = $wp_registered_widgets[ $widget_id ];

			$callback = $widget['callback'][0];

			if ( $callback instanceof BP_Core_Friends_Widget ) {
				unset( $_wp_sidebars_widgets[ $sidebar ][ $index ] );
			}
		}
	}

	return $sidebars;
}

add_filter( 'sidebars_widgets', 'bp_profile_visibility_hide_friends_widget' );
