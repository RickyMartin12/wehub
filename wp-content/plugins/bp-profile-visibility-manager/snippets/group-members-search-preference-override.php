<?php

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Make all group members searchable by other group members irrespective of their search preference.
 *
 * @param string $sql sql.
 *
 * @return string
 */
function bp_profile_visibility_force_enable_group_members_in_search( $sql ) {

	// Not logged, no need to alter.
	if ( ! is_user_logged_in() ) {
		return $sql;
	}

	$group_ids = groups_get_user_groups( bp_loggedin_user_id() );

	// does not have any group.
	if ( empty( $group_ids['groups'] ) ) {
		return $sql;
	}

	global $wpdb;
	$table = buddypress()->groups->table_name_members;
	$list  = join( ',', $group_ids['groups'] );

	$sql_users_with_same_group = $wpdb->prepare( "SELECT user_id FROM {$table} WHERE group_id IN ({$list} ) AND is_confirmed = %d", 1 );

	return $sql . " AND bumt.user_id NOT IN ($sql_users_with_same_group)";
}

add_filter( 'bp_profile_visibility_search_excluded_sql', 'bp_profile_visibility_force_enable_group_members_in_search' );

// Put this code in bp-custom.php to enable this snippet.
// make sure to uncomment( remove // from the code below).
// add_filter( 'bp_profile_visibility_override_members_search_by_group_members', '__return_true' );
