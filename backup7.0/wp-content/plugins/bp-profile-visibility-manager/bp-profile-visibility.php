<?php
/**
 * Plugin Name: BuddyPress Profile Visibility Manager
 * Description: It allows users to hide themselves from members directory/searches and hide their profile from anyone who can see
 * Author: BuddyDev
 * Author URI: https://buddydev.com
 * Plugin URI: https://buddydev.com/buddypress/bp-profile-visibility-manager/
 * Version: 1.8.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Helper class which loads the Profile Visibility Manager components and hooks to the BP for various actions
 * Implemented as singleton instance, use BP_Profile_Visibility_Manager::get_instance() to modify any hooks you want to remove
 */
class BP_Profile_Visibility_Manager {
	/**
	 * Singleton instance.
	 *
	 * @var BP_Profile_Visibility_Manager
	 */
	private static $instance;

	/**
	 * Absolute path to the plugin.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * BP_Profile_Visibility_Manager constructor.
	 */
	private function __construct() {
		$this->path = plugin_dir_path( __FILE__ );

		$this->setup();
	}

	/**
	 * Factory method for creating singleton object
	 *
	 * @return BP_Profile_Visibility_Manager
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Everything starts here
	 */
	public function setup() {

		add_action( 'bp_loaded', array( $this, 'load' ), 0 );

		add_action( 'bp_actions', array( $this, 'init_hide_activity' ), 4 );

		add_action( 'template_redirect', array( $this, 'check_profile_access' ), 1 );

		add_filter( 'bp_get_add_friend_button', array( $this, 'filter_add_friends_button' ) );
		add_action( 'bp_actions', array( $this, 'gate_action_add_friend' ) );
		add_action( 'wp_ajax_addremove_friend', array( $this, 'check_ajax_add_friend' ), 8 );
		// add_filter( 'bp_get_group_join_button', array( $this, 'filter_group_membership_request' ) );

		// new in 1.1
		// excluding from all queries.
		// add_action( 'pre_user_query', array( $this, 'exclude' ), 200 );
		add_filter( 'bp_user_query_uid_clauses', array( $this, 'exclude_bp_query' ), 200, 2 );

		// filters the sql for member list pagination count.
		// we don't need if we filter on bp user query.
		//add_filter( 'bp_found_user_query', array( $this, 'filter_count' ), 10, 2 );

		add_filter( 'bp_get_total_member_count', array( $this, 'filter_total_members_count' ) );
		// load text domain.
		add_action( 'bp_init', array( $this, 'load_textdomain' ), 2 );

		add_filter( 'bp_get_user_last_activity', array( $this, 'filter_last_activity' ), 10, 2 );
		add_filter( 'bp_get_last_activity', array( $this, 'filter_last_activity' ), 10, 2 );

		add_filter( 'bp_members_suggestions_get_suggestions', array( $this, 'modify_suggestions' ), 10 );

		// this is what I hate about BuddyPress. too much inconsistency.
		// doing the last activity for member dir.
		add_filter( 'bp_member_last_active', array( $this, 'filter_member_last_active' ), 100, 2 );

		add_action( 'bp_enqueue_scripts', array( $this, 'deregister_livetimestamp' ), 100 );

		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );

		// for message.
		add_action( 'wp_ajax_messages_autocomplete_results', array( $this, 'setup_for_message_auto_complete' ), 0 );
	}

	/**
	 * Load plugin text domain for translation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'bp-profile-visibility-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Loads the Visibility Manager component/required files
	 */
	public function load() {

		$path = plugin_dir_path( __FILE__ );

		$files = array(
			'bp-profile-visibility-component.php',
			'core/functions.php',
			'core/hooks.php',
			'core/protected-component.php',
			'core/class-bppv-members-directory-protector.php',
		);

		if ( $this->get_option( 'enable_friends_list_visibility' ) === 'yes' ) {
			$files[] = 'core/friends-visibility.php';
		}

		if ( $this->get_option( 'enable_group_tab_visibility' ) === 'yes' ) {
			$files[] = 'core/groups-visibility.php';
		}

		if (  $this->get_option( 'enable_follow_request_visibility' ) === 'yes' ) {

			$files[] = 'core/follow-request-handler.php';
		}

		if ( apply_filters( 'bp_profile_visibility_override_members_search_by_group_members', false ) ) {
			$files[] = 'snippets/group-members-search-preference-override.php';
		}

		if ( is_admin() ) {
			$files[] = 'admin/admin.php';
		}

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

	}

	/**
	 * Hide activity legacy.
	 */
	public function init_hide_activity() {

		$user_id = get_current_user_id();

		$settings = bp_profile_visibility_get_settings( $user_id, 'bp_hide_last_active' );
		// we do not stop recording if the user was never recorded.
		// But we do not allow further recording.
		if ( $settings == 'yes' && $user_id && BP_Core_User::get_last_activity( $user_id ) ) {
			// we don't unhook it as it will make user invisible in the directory.
			remove_action( 'wp_head', 'bp_core_record_activity' );
			// is SM is on, remove the record activity hook.
		}

	}

	/**
	 * Unload the Live timestamp plugin on members directory
	 *
	 * If we do not, it will set the last active time for even the non active user
	 */
	public function deregister_livetimestamp() {

		if ( bp_is_members_directory() || bp_is_user() ) {
			wp_dequeue_script( 'bp-livestamp' );
		}
	}

	/**
	 * Checks and protects user account visibility
	 */
	public function check_profile_access() {

		if ( ! function_exists( 'buddypress' ) || ! bp_is_user() ) {
			return;
		}

		if ( bp_profile_visibility_is_visible_profile( bp_displayed_user_id(), bp_loggedin_user_id() ) ) {
			return;
		}


		if ( bp_is_current_component( 'visibility-protected' ) ) {
			return;// no redirects please.
		}
		// if we are on user profile page.
		$privacy = bp_profile_visibility_get_settings( bp_displayed_user_id(), 'bp_profile_visibility' );

		$referrer = wp_get_referer();

		if ( ! $referrer ) {
			$referrer = bp_core_get_root_domain();
		}

		$redirect_location = $this->get_redirect_location( $referrer, $privacy );


		// in all other cases, user must be logged in.
		if ( ! is_user_logged_in() ) {
			bp_core_add_message( __( 'Please login to view profile.', 'bp-profile-visibility-manager' ), 'error' );
			wp_safe_redirect( $redirect_location );
			exit( 0 );
		}
		// if we are here, don't show the profile.
		bp_core_add_message( __( "This User's privacy setting does not allow you to view their profile.", 'bp-profile-visibility-manager' ), 'error' );


		if ( apply_filters( 'bp_profile_visibility_redirect_on_view_user', true ) ) {
			wp_safe_redirect( $redirect_location );
		}

		exit( 0 );

	}

	/**
	 * Disable friends button based o n settings.
	 *
	 * @param array $btn button.
	 *
	 * @return array
	 */
	public function filter_add_friends_button( $btn ) {

		if ( ! is_array( $btn ) || ! isset( $btn['id'] ) || $btn['id'] != 'not_friends' ) {
			return $btn;
		}

		$user_link_id = $btn['link_id'];
		$user_id      = absint( str_replace( 'friend-', '', $user_link_id ) );

		if ( bp_profile_visibility_get_settings( $user_id, 'bp_allow_friendship_request' ) == 'yes' ) {
			return $btn;
		}
		// otherwise, let us hide the button.
		$btn['id'] = false;

		// get the friendship setting for this user.
		return array();
	}

	/**
	 * Disable add friends if needed.
	 *
	 * @return bool
	 */
	public function gate_action_add_friend() {
		if ( ! bp_is_active( 'friends' ) || ! bp_is_friends_component() || ! bp_is_current_action( 'add-friend' ) ) {
			return false;
		}

		if ( ! $potential_friend_id = (int) bp_action_variable( 0 ) ) {
			return false;
		}

		if ( $potential_friend_id == bp_loggedin_user_id() ) {
			return false;
		}

		$allowed = bp_profile_visibility_get_settings( $potential_friend_id, 'bp_allow_friendship_request' ) == 'yes';

		$friendship_status = BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $potential_friend_id );

		if ( 'not_friends' == $friendship_status && ! $allowed ) {
			// this action is not allowed.
			bp_core_add_message( __( 'Friendship could not be requested.', 'bp-profile-visibility-manager' ), 'error' );

			bp_core_redirect( wp_get_referer() );
		}


		return false;
	}

	/**
	 * Disable ajax friend action if needed.
	 */
	public function check_ajax_add_friend() {

		// Cast fid as an integer.
		$friend_id = (int) $_POST['fid'];
		$allowed   = bp_profile_visibility_get_settings( $friend_id, 'bp_allow_friendship_request' ) == 'yes';

		// Trying to cancel friendship.
		if ( 'not_friends' == BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $friend_id ) && ! $allowed ) {
			echo __( 'Friendship could not be requested.', 'bp-profile-visibility-manager' );
			exit( 0 );
		}
	}


	/**
	 * Disable button.
	 *
	 * @param array $btn button.
	 *
	 * @return array
	 */
	public function filter_group_membership_request( $btn ) {

		if ( ! is_array( $btn ) || ! isset( $btn['id'] ) || $btn['id'] != 'request_membership' ) {
			return $btn;
		}

		$user_id = bp_loggedin_user_id();

		$settings = bp_profile_visibility_get_settings( $user_id, 'bp_profile_visibility' );

		if ( empty( $settings ) || $settings == 'public' || $settings == 'loggedin' ) {
			return $btn;
		}

		// otherwise, let us hide the button.
		$btn['id'] = false;

		// get the friendship setting for this user.
		return array();

	}

	/**
	 * Get location where the user should be redirected.
	 *
	 * @param string $referrer referer.
	 * @param string $privacy privacy.
	 *
	 * @return string
	 */
	public function get_redirect_location( $referrer = '', $privacy = '' ) {

		$settings = bp_get_option( 'bp_profile_visibility_settings' );
		// default policy is referrer.
		$policy = 'referer';

		if ( isset( $settings['protected_profile_redirection_policy'] ) ) {
			$policy = $settings['protected_profile_redirection_policy'];
		}

		$location = '';

		if ( 'referer' == $policy ) {

			if ( empty( $referrer ) ) {
				$location = home_url( '/' );
			} else {
				$location = $referrer;
			}
		} else {
			$location = bp_displayed_user_domain() . 'visibility-protected';
		}

		$location = apply_filters( 'bp_profile_visibility_redirect_location', $location, $privacy, $referrer );
		// allow to filter and redirect to somewhere else;
		// recursion detected
		// redirect to home page.
		if ( $this->recursion_detected( $referrer, $policy ) ) {

			if ( 'referer' != $policy ) {
				$location = bp_displayed_user_domain() . 'visibility-protected';
			} else {
				$location = home_url( '/' );
			}
		}

		return $location;
	}

	/**
	 * Detect possible recursion.
	 *
	 * @param string $url url.
	 * @param string $policy policy.
	 *
	 * @return bool
	 */
	public function recursion_detected( $url, $policy ) {
		$displayed_domain = bp_displayed_user_domain();

		if ( empty( $displayed_domain ) ) {
			return false;
		}

		// in case of profile protected page.
		if ( $policy != 'referer' && strpos( $url, $displayed_domain . 'visibility-protected' ) ) {
			return false;
		}

		if ( strpos( $url, $displayed_domain ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Find all users with a meta key and meta value
	 *
	 * @param string $key key.
	 *
	 * @return array()
	 */
	public function get_users( $key, $val ) {
		global $wpdb;
		$users = $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key=%s AND meta_value=%s", $key, $val ) );

		return $users;
	}

	/**
	 * Get sql for excluding users.
	 *
	 * @param string $key key.
	 * @param string $val value.
	 *
	 * @return string
	 */
	public function get_users_sql( $key, $val ) {
		global $wpdb;
		// I am using a real bad table alias here to avoid any future conflict.
		$users = $wpdb->prepare( "SELECT bumt.user_id FROM {$wpdb->usermeta} as bumt WHERE bumt.meta_key=%s AND bumt.meta_value=%s", $key, $val );

		return $users;
	}

	/**
	 * Get sql for excluding users on directory.
	 *
	 * @return string
	 */
	public function get_dir_excluded_users_sql() {
		$users = $this->get_users_sql( 'bp_exclude_in_dir', 'yes' );
		return $users;
	}

	/**
	 * Get sql for excluding users on directory search.
	 *
	 * @return string
	 */
	public function get_search_excluded_users_sql() {
		$search_excl_users_sql = $this->get_users_sql( 'bp_exclude_in_search', 'yes' );
		return apply_filters( 'bp_profile_visibility_search_excluded_sql', $search_excl_users_sql );
	}

	/**
	 * Get Excluded user on dir.
	 *
	 * @return array
	 */
	public function get_dir_excluded_users() {
		$users = $this->get_users( 'bp_exclude_in_dir', 'yes' );
		return $users;
	}

	/**
	 * Get excluded users on search.
	 *
	 * @return array
	 */
	public function get_search_excluded_users() {
		$users = $this->get_users( 'bp_exclude_in_search', 'yes' );
		return $users;
	}

	/**
	 * Currently It is filtering the excluded users by id values, in future, we may use the existing get_users_sql for the exclude clause in sql itself.
	 * The only concerns seems to be the conflict in table name
	 *
	 * Not used since 1.6.0 as it was causing incorrect no. of items in the list
	 *
	 * @param WP_User_Query $query user query object.
	 */
	public function exclude( $query ) {

		if ( ! function_exists( 'buddypress' ) || ! function_exists( 'bp_profile_visibility_loader' ) ) {
			return;
		}
		// do not hide users inside the admin.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( bp_profile_visibility_get_option( 'do_not_hide_users_from_admin' ) == 'yes' && bp_profile_visibility_is_admin_user() ) {
			return;
		}

		$qv = $query->query_vars;

		// since multiple NOT IN on ID is allowed though it is bad asthetically.
		// so we don't have to worry much here.
		global $wpdb;
		// do not stop in friends list.
		if ( bp_profile_visibility_loader()->get_data( 'is_visible' ) ) {
			return;
		}

		if ( $qv['search'] || ( bp_is_members_component() && bp_is_directory() && ! empty( $_REQUEST['search_terms'] ) ) ) {
			$sql = $this->get_search_excluded_users_sql();
		} else {
			$sql = $this->get_dir_excluded_users_sql();
		}

		if ( ! empty( $sql ) ) {
			//$list = join(',', $users );
			$query->query_where .= " AND {$wpdb->users}.ID NOT IN ({$sql}) ";
		}
	}


	/**
	 * Currently It is filtering the excluded users by id values, in future, we may use the existing get_users_sql for the exclude clause in sql itself.
	 * The only concerns seems to be the conflict in table name
	 *
	 * @param array         $sql sql query.
	 * @param BP_User_Query $query user query object.
	 *
	 * @return array
	 */
	public function exclude_bp_query( $sql, $query ) {

		if ( ! function_exists( 'buddypress' ) || ! function_exists( 'bp_profile_visibility_loader' ) ) {
			return $sql;
		}
		// do not hide users inside the admin.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $sql;
		}

		if ( bp_profile_visibility_get_option( 'do_not_hide_users_from_admin' ) == 'yes' && bp_profile_visibility_is_admin_user() ) {
			return $sql;
		}

		$qv = $query->query_vars;

		// since multiple NOT IN on ID is allowed though it is bad asthetically.
		// so we don't have to worry much here.
		global $wpdb;
		// do not stop in friends list.
		if ( bp_profile_visibility_loader()->get_data( 'is_visible' ) ) {
			return $sql;
		}

		$has_search_term = $qv['search_terms']
		                   || ( wp_doing_ajax() && 'bp_search_ajax'=== $_REQUEST['action'] && (! empty( $_REQUEST['search_term']) || !empty( $_REQUEST['s'])  ) )
							|| ( ! empty( $_GET['s']) && !empty( $_GET['bp_search']) );

		if (  $has_search_term || ( bp_is_members_component() && bp_is_directory() && ! empty( $_REQUEST['search_terms'] ) ) ) {
			$sql_exclude = $this->get_search_excluded_users_sql();
		} else {
			$sql_exclude = $this->get_dir_excluded_users_sql();
		}

		if ( ! empty( $sql_exclude ) ) {
			//$list = join(',', $users );
			$sql['where'][] = "  u.{$query->uid_name} NOT IN ({$sql_exclude}) ";
		}

		return $sql;
	}

	/**
	 * Setup exclusion.
	 */
	public function setup_for_message_auto_complete() {
		bp_profile_visibility_loader()->set_data( 'is_visible', 1 );
	}

	/**
	 * Filter sql for count.
	 *
	 * @param string        $query sql.
	 * @param BP_User_Query $object query object.
	 *
	 * @return string
	 */
	public function filter_count( $query, $object ) {

		if ( 'sql_calc_found_rows' == $object->query_vars['count_total'] ) {
			return $query;
		}

		if ( bp_profile_visibility_get_option( 'do_not_hide_users_from_admin' ) == 'yes' && bp_profile_visibility_is_admin_user() ) {
			return $query;
		}

		$qv = $object->query_vars;

		if ( ! empty( $qv['search'] ) || ( bp_is_members_component() && bp_is_directory() && ! empty( $_REQUEST['search_terms'] ) ) ) {
			$sql = $this->get_search_excluded_users_sql();
		} else {
			$sql = $this->get_dir_excluded_users_sql();
		}

		$query_in = '';

		if ( ! empty( $sql ) ) {
			$query_in = " AND u.{$object->uid_name} NOT IN({$sql})";
		}

		return $query . $query_in;
	}

	/**
	 * Filter count on Members directory (total members directory )
	 *
	 * @param int $count count.
	 *
	 * @return int
	 */
	public function filter_total_members_count( $count ) {

		if ( ! $count ) {
			return $count;
		}

		if ( bp_profile_visibility_get_option( 'do_not_hide_users_from_admin' ) == 'yes' && bp_profile_visibility_is_admin_user() ) {
			return $count;
		}

		// count the excluded user from the.
		$sql = $this->get_dir_excluded_users_sql();

		global $wpdb;

		$exclude_count_sql = str_replace( 'SELECT bumt.user_id', 'SELECT COUNT(bumt.user_id)', $sql );

		$exclude_count = $wpdb->get_var( $exclude_count_sql );

		$count = $count - (int) $exclude_count;

		return $count;
	}

	/**
	 * Filter last activity.
	 *
	 * @param BP_Activity_Activity $activity activity object.
	 * @param int                  $user_id user id.
	 *
	 * @return string
	 */
	public function filter_last_activity( $activity, $user_id ) {

		if ( bp_profile_visibility_get_settings( $user_id, 'bp_hide_last_active' ) == 'yes' ) {
			return '';// empty.
		}

		return $activity;
	}

	/**
	 * Modify suggestions
	 *
	 * @param array $suggestions Suggestions.
	 *
	 * @return array
	 */
	public function modify_suggestions( $suggestions ) {

		foreach ( $suggestions as $key => $suggestion ) {
			if ( ! bp_profile_visibility_is_visible_profile( $suggestion->user_id, get_current_user_id() ) ) {
				unset( $suggestions[ $key ] );
			}
		}

		return $suggestions;
	}

	/**
	 * Filter members last active
	 *
	 * @param string $last_active last active time.
	 * @param array  $args args.
	 *
	 * @return string
	 */
	public function filter_member_last_active( $last_active, $args = array() ) {

		$user_id = bp_get_member_user_id();

		if ( ! $user_id ) {
			return $last_active;
		}
		if ( bp_profile_visibility_get_settings( $user_id, 'bp_hide_last_active' ) == 'yes' ) {

			$last_active = '';//__( 'Not recently active', 'bp-profile-visibility-manager' );
		}

		return $last_active;
	}

	public function on_activation() {

		$default_settings = apply_filters( 'bp_profile_visibility_default_settings', array(
			'bp_profile_visibility'                => 'public',//everyone
			'bp_hide_last_active'                  => 'no',
			'bp_exclude_in_search'                 => 'no',
			'bp_exclude_in_dir'                    => 'no',
			'bp_allow_friendship_request'          => 'yes',
			'admin_only'                           => 'no',
			'protected_profile_redirection_policy' => 'referer',
			'enable_group_tab_visibility'          => 'no',
			'bp_profile_group_tab_visibility'      => '',
			'do_not_hide_users_from_admin'         => 'no'
		) );


		add_option( 'bp_profile_visibility_settings', $default_settings );
	}

	/**
	 * Get global option
	 *
	 * @param $option_name
	 *
	 * @return string
	 */
	public function get_option( $option_name ) {

		$options = $this->get_options();
		if ( isset( $options[ $option_name ] ) ) {
			return $options[ $option_name ];
		}

		return '';
	}

	public function get_options() {
		return bp_get_option( 'bp_profile_visibility_settings' );
	}

	public function get_path() {
		return $this->path;
	}

}// end of helper class.

/**
 * Shortcut function.
 *
 * @return BP_Profile_Visibility_Manager
 */
function bp_profile_visibility_manager() {
	return BP_Profile_Visibility_Manager::get_instance();
}

// Initialize the helper which will load component when BuddyPress is ready.
bp_profile_visibility_manager();

/**
 * Visibilities.
 *
 * @return array
 */
function bp_profile_visibility_get_visibilities() {

	$visibilities = array(
		'public'   => __( 'Everyone', 'bp-profile-visibility-manager' ),
		'loggedin' => __( 'Logged in Users Only', 'bp-profile-visibility-manager' ),
	);

	if ( bp_is_active( 'friends' ) ) {
		$visibilities['friends'] = __( 'Friends Only', 'bp-profile-visibility-manager' );
	}

	if ( bp_is_active( 'groups' ) ) {
		$visibilities['groups'] = __( 'My Group Members Only', 'bp-profile-visibility-manager' );
	}

	$visibilities['self'] = __( 'Only Me (private)', 'bp-profile-visibility-manager' );

	// old, mis typed hooks.
	$visibilities =  apply_filters( 'bp_profile_visibility_visiility_levels', $visibilities );

	return apply_filters( 'bp_profile_visibility_visibility_levels', $visibilities );
}

/**
 * List visibilities.
 *
 * @param string $name html form field name.
 * @param string $id select field id.
 * @param string $selected selected item.
 * @param bool   $echo whether to echo or return.
 *
 * @return string
 */
function bp_profile_visibility_list_visibilities( $name, $id = '', $selected = '', $echo = true ) {

	$html = "<select name='{$name}'";
	if ( $id ) {
		$html .= " id='{$id}'";
	}
	$html .= ">";

	$visibilities = bp_profile_visibility_get_visibilities();
	foreach ( $visibilities as $value => $label ) {
		$html .= "<option value='{$value}' " . selected( $value, $selected, false ) . ">{$label}</option>";
	}

	$html .= "</select>";

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}

}
